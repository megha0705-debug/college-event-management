<?php
/**
 * Event Details & Registration Page
 * CampusPulse — College Event Management Website
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = get_db_connection();
$event_id = (int)($_GET['id'] ?? 0);

if (!$pdo || $event_id <= 0) {
    header("Location: " . base_url('events.php'));
    exit;
}

// Handle Event Registration POST Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    // 1. Check authentication
    if (!is_logged_in()) {
        flash_set('warning', 'Please sign in to register for events.');
        header("Location: " . base_url('login.php?redirect=' . urlencode('event-details.php?id=' . $event_id)));
        exit;
    }

    // Admins cannot register as attendees
    if (is_admin()) {
        flash_set('warning', 'Administrators cannot register as attendees. Use a student account.');
        header("Location: " . base_url('event-details.php?id=' . $event_id));
        exit;
    }

    // 2. Validate CSRF token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        flash_set('danger', 'Security verification failed (invalid CSRF token). Please try again.');
        header("Location: " . base_url('event-details.php?id=' . $event_id));
        exit;
    }

    $user_id = (int)$_SESSION['user_id'];

    try {
        // Begin transaction to prevent race conditions and overbooking
        $pdo->beginTransaction();

        // Lock event row for update
        $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? FOR UPDATE");
        $stmt->execute([$event_id]);
        $target_event = $stmt->fetch();

        if (!$target_event) {
            $pdo->rollBack();
            flash_set('danger', 'Event not found.');
            header("Location: " . base_url('events.php'));
            exit;
        }

        // Check if event is cancelled or completed
        if ($target_event['status'] === 'cancelled') {
            $pdo->rollBack();
            flash_set('danger', 'This event has been cancelled by the organizers.');
            header("Location: " . base_url('event-details.php?id=' . $event_id));
            exit;
        }

        // Check registration deadline
        if (strtotime($target_event['registration_deadline']) < time()) {
            $pdo->rollBack();
            flash_set('danger', 'The registration deadline for this event has passed.');
            header("Location: " . base_url('event-details.php?id=' . $event_id));
            exit;
        }

        // Check if already registered
        $chkStmt = $pdo->prepare("SELECT id, status FROM registrations WHERE user_id = ? AND event_id = ?");
        $chkStmt->execute([$user_id, $event_id]);
        $existing = $chkStmt->fetch();

        if ($existing) {
            if ($existing['status'] === 'confirmed') {
                $pdo->rollBack();
                flash_set('info', 'You are already registered for this event!');
                header("Location: " . base_url('event-details.php?id=' . $event_id));
                exit;
            } elseif ($existing['status'] === 'cancelled') {
                // Re-activate registration
                $upd = $pdo->prepare("UPDATE registrations SET status = 'confirmed', registration_date = NOW() WHERE id = ?");
                $upd->execute([$existing['id']]);
                $pdo->commit();
                flash_set('success', 'Your registration has been reinstated successfully!');
                header("Location: " . base_url('student/my-registrations.php'));
                exit;
            }
        }

        // Check capacity
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM registrations WHERE event_id = ? AND status = 'confirmed'");
        $countStmt->execute([$event_id]);
        $current_registered = (int)$countStmt->fetchColumn();

        if ($current_registered >= (int)$target_event['capacity']) {
            $pdo->rollBack();
            flash_set('danger', 'Sorry, this event has reached maximum capacity!');
            header("Location: " . base_url('event-details.php?id=' . $event_id));
            exit;
        }

        // Insert new confirmed registration
        $insStmt = $pdo->prepare("INSERT INTO registrations (user_id, event_id, registration_date, status) VALUES (?, ?, NOW(), 'confirmed')");
        $insStmt->execute([$user_id, $event_id]);

        $pdo->commit();

        flash_set('success', 'Congratulations! You have successfully registered for ' . e($target_event['title']) . '.');
        header("Location: " . base_url('student/my-registrations.php'));
        exit;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Registration error: " . $e->getMessage());
        flash_set('danger', 'An unexpected error occurred during registration. Please try again.');
        header("Location: " . base_url('event-details.php?id=' . $event_id));
        exit;
    }
}

// Fetch Event Details
try {
    $stmt = $pdo->prepare("
        SELECT e.*, 
               COUNT(r.id) AS registered_count 
        FROM events e
        LEFT JOIN registrations r ON e.id = r.event_id AND r.status = 'confirmed'
        WHERE e.id = ?
        GROUP BY e.id
    ");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch();

    if (!$event) {
        flash_set('danger', 'The requested event could not be found.');
        header("Location: " . base_url('events.php'));
        exit;
    }
} catch (PDOException $e) {
    flash_set('danger', 'Database error loading event.');
    header("Location: " . base_url('events.php'));
    exit;
}

$page_title = $event['title'];
require_once __DIR__ . '/includes/header.php';

$capacity = (int)$event['capacity'];
$registered_count = (int)$event['registered_count'];
$seats_left = max(0, $capacity - $registered_count);
$deadline_passed = is_deadline_passed($event['registration_deadline']);
$is_full = ($seats_left <= 0);
$is_cancelled = ($event['status'] === 'cancelled');
$is_completed = ($event['status'] === 'completed' || strtotime($event['event_date']) < strtotime(date('Y-m-d')));

// Check if current user is registered
$user_reg = null;
if (is_logged_in() && !is_admin()) {
    $user_reg = is_user_registered($pdo, $_SESSION['user_id'], $event_id);
}

// Fetch 3 related events in same category
$related_events = [];
try {
    $relStmt = $pdo->prepare("
        SELECT * FROM events 
        WHERE category = ? AND id != ? AND status != 'cancelled'
        ORDER BY event_date ASC 
        LIMIT 3
    ");
    $relStmt->execute([$event['category'], $event_id]);
    $related_events = $relStmt->fetchAll();
} catch (PDOException $e) {
    // ignore
}
?>

<div class="container py-4">
  <!-- Breadcrumbs -->
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?php echo base_url('index.php'); ?>">Home</a></li>
      <li class="breadcrumb-item"><a href="<?php echo base_url('events.php'); ?>">Events</a></li>
      <li class="breadcrumb-item"><a href="<?php echo base_url('events.php?category=' . urlencode($event['category'])); ?>"><?php echo e($event['category']); ?></a></li>
      <li class="breadcrumb-item active text-truncate" style="max-width: 300px;" aria-current="page"><?php echo e($event['title']); ?></li>
    </ol>
  </nav>

  <div class="row g-4">
    <!-- Left Column: Banner & Event Full Information -->
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
        <!-- Event Banner Image -->
        <div class="position-relative" style="height: 380px; background-color: #0f172a;">
          <img src="<?php echo get_event_image_url($event['image'], $event['category']); ?>" 
               alt="<?php echo e($event['title']); ?>" 
               class="w-100 h-100 object-fit-cover">
          <div class="position-absolute top-0 start-0 m-3 d-flex gap-2">
            <span class="badge <?php echo get_category_badge_class($event['category']); ?> fs-6 px-3 py-2 shadow">
              <?php echo e($event['category']); ?>
            </span>
            <?php if ($is_cancelled): ?>
              <span class="badge bg-danger fs-6 px-3 py-2 shadow">Cancelled</span>
            <?php elseif ($is_completed): ?>
              <span class="badge bg-secondary fs-6 px-3 py-2 shadow">Concluded</span>
            <?php elseif ($deadline_passed): ?>
              <span class="badge bg-warning text-dark fs-6 px-3 py-2 shadow">Registration Closed</span>
            <?php else: ?>
              <span class="badge bg-success fs-6 px-3 py-2 shadow"><i class="fas fa-check-circle me-1"></i> Registration Open</span>
            <?php endif; ?>
          </div>
        </div>

        <div class="card-body p-4 p-md-5">
          <h1 class="h2 fw-bold text-navy mb-3"><?php echo e($event['title']); ?></h1>

          <!-- Meta Pills -->
          <div class="d-flex flex-wrap gap-3 pb-3 mb-4 border-bottom text-muted small">
            <div><i class="fas fa-calendar-alt text-purple me-1"></i> <?php echo format_date($event['event_date'], 'l, F d, Y'); ?></div>
            <div><i class="fas fa-clock text-purple me-1"></i> <?php echo format_time($event['start_time']); ?> &ndash; <?php echo format_time($event['end_time']); ?></div>
            <div><i class="fas fa-map-marker-alt text-purple me-1"></i> <?php echo e($event['venue']); ?></div>
            <div><i class="fas fa-user-tie text-purple me-1"></i> Organized by <?php echo e($event['organizer']); ?></div>
          </div>

          <!-- Description Section -->
          <h4 class="fw-bold mb-3"><i class="fas fa-align-left text-purple me-2"></i> About This Event</h4>
          <div class="text-secondary leading-relaxed mb-4" style="font-size: 1.05rem; white-space: pre-line;">
            <?php echo e($event['description']); ?>
          </div>

          <!-- Event Schedule & Guidelines Card -->
          <div class="p-4 rounded-3 bg-light border mb-4">
            <h5 class="fw-bold mb-3"><i class="fas fa-clipboard-list text-purple me-2"></i> Participation Highlights &amp; Rules</h5>
            <ul class="mb-0 text-secondary ps-3">
              <li class="mb-2">Open to all verified college undergraduate and postgraduate students.</li>
              <li class="mb-2">Must present digital ticket QR or confirmation slip along with College ID card at entry.</li>
              <li class="mb-2">Certificate of participation will be generated upon verified attendance.</li>
              <li>Please arrive 15 minutes prior to the scheduled start time at <strong><?php echo e($event['venue']); ?></strong>.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Column: Registration Card & Organizer Details -->
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top bg-white" style="top: 90px;">
        <h4 class="fw-bold mb-3">Event Registration</h4>

        <!-- Capacity & Deadline Stats -->
        <div class="mb-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted small">Seats Availability</span>
            <span class="fw-bold <?php echo ($seats_left < 10) ? 'text-danger' : 'text-success'; ?>">
              <?php echo $seats_left; ?> seats left
            </span>
          </div>
          <div class="progress mb-2" style="height: 8px;">
            <?php $pct = ($capacity > 0) ? min(100, round(($registered_count / $capacity) * 100)) : 0; ?>
            <div class="progress-bar <?php echo ($pct > 85) ? 'bg-danger' : 'bg-primary'; ?>" 
                 role="progressbar" 
                 style="width: <?php echo $pct; ?>%;" 
                 aria-valuenow="<?php echo $pct; ?>" 
                 aria-valuemin="0" 
                 aria-valuemax="100"></div>
          </div>
          <div class="d-flex justify-content-between text-muted small">
            <span>Total Capacity: <?php echo $capacity; ?></span>
            <span>Registered: <?php echo $registered_count; ?></span>
          </div>
        </div>

        <div class="p-3 bg-light rounded-3 mb-4">
          <div class="d-flex align-items-center gap-2 text-danger small fw-semibold">
            <i class="fas fa-stopwatch fs-5"></i>
            <div>
              <div>Registration Closes On:</div>
              <span class="text-dark fw-bold"><?php echo format_datetime($event['registration_deadline']); ?></span>
            </div>
          </div>
        </div>

        <!-- Registration Action State -->
        <?php if ($user_reg && $user_reg['status'] === 'confirmed'): ?>
          <div class="alert alert-success text-center mb-3">
            <i class="fas fa-check-circle fs-3 d-block mb-2 text-success"></i>
            <h6 class="fw-bold mb-1">You Are Registered!</h6>
            <small>Your seat is confirmed. We look forward to seeing you at the event.</small>
          </div>
          <a href="<?php echo base_url('student/my-registrations.php'); ?>" class="btn btn-outline-purple w-100 mb-2">
            <i class="fas fa-ticket me-1"></i> View My Registrations
          </a>
        <?php elseif ($is_cancelled): ?>
          <div class="alert alert-danger text-center mb-0">
            <i class="fas fa-ban fs-3 d-block mb-1"></i>
            <strong>This event has been cancelled.</strong>
          </div>
        <?php elseif ($is_completed): ?>
          <div class="alert alert-secondary text-center mb-0">
            <i class="fas fa-flag-checkered fs-3 d-block mb-1"></i>
            <strong>This event has concluded.</strong>
          </div>
        <?php elseif ($deadline_passed): ?>
          <div class="alert alert-warning text-center mb-0">
            <i class="fas fa-hourglass-end fs-3 d-block mb-1"></i>
            <strong>Registration is now closed.</strong>
          </div>
        <?php elseif ($is_full): ?>
          <div class="alert alert-danger text-center mb-0">
            <i class="fas fa-users-slash fs-3 d-block mb-1"></i>
            <strong>Event is fully booked!</strong>
          </div>
        <?php elseif (!is_logged_in()): ?>
          <a href="<?php echo base_url('login.php?redirect=' . urlencode('event-details.php?id=' . $event['id'])); ?>" class="btn btn-gradient-primary w-100 py-2 fs-6 mb-2">
            <i class="fas fa-sign-in-alt me-1"></i> Sign In to Register
          </a>
          <small class="text-muted d-block text-center">New student? <a href="<?php echo base_url('register.php'); ?>">Register account</a></small>
        <?php elseif (is_admin()): ?>
          <div class="alert alert-info text-center small mb-2">
            <i class="fas fa-shield-halved me-1"></i> You are logged in as Administrator.
          </div>
          <a href="<?php echo base_url('admin/edit-event.php?id=' . $event['id']); ?>" class="btn btn-outline-primary w-100 mb-2">
            <i class="fas fa-edit me-1"></i> Edit This Event
          </a>
          <a href="<?php echo base_url('admin/registrations.php?event_id=' . $event['id']); ?>" class="btn btn-secondary w-100">
            <i class="fas fa-list me-1"></i> View Attendee Roster
          </a>
        <?php else: ?>
          <!-- Registration Form Submission -->
          <form action="<?php echo base_url('event-details.php?id=' . $event['id']); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="register">
            <button type="submit" class="btn btn-gradient-primary w-100 py-3 fs-5 shadow-sm">
              <i class="fas fa-ticket-alt me-2"></i> Confirm Registration
            </button>
          </form>
          <small class="text-muted d-block text-center mt-2">
            <i class="fas fa-shield-alt me-1"></i> Free registration &bull; Instant confirmation
          </small>
        <?php endif; ?>

        <hr class="my-4">

        <!-- Organizer Profile Box -->
        <div>
          <h6 class="fw-bold mb-2">Event Organizer</h6>
          <div class="d-flex align-items-center gap-3">
            <div class="brand-icon" style="width: 44px; height: 44px; font-size: 1.2rem;">
              <i class="fas fa-university"></i>
            </div>
            <div>
              <div class="fw-bold text-dark small"><?php echo e($event['organizer']); ?></div>
              <small class="text-muted">Verified Campus Department</small>
            </div>
          </div>
        </div>

        <div class="mt-4 pt-3 border-top">
          <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted small">Share Event:</span>
            <div class="d-flex gap-2">
              <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode('Check out ' . $event['title'] . ' on CampusPulse!'); ?>" target="_blank" class="btn btn-sm btn-light rounded-circle" title="Share on Twitter"><i class="fab fa-twitter"></i></a>
              <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(base_url('event-details.php?id=' . $event['id'])); ?>" target="_blank" class="btn btn-sm btn-light rounded-circle" title="Share on LinkedIn"><i class="fab fa-linkedin-in"></i></a>
              <a href="whatsapp://send?text=<?php echo urlencode('Check out this college event: ' . $event['title'] . ' ' . base_url('event-details.php?id=' . $event['id'])); ?>" class="btn btn-sm btn-light rounded-circle" title="Share on WhatsApp"><i class="fab fa-whatsapp"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Related Events Section -->
  <?php if (!empty($related_events)): ?>
    <div class="mt-5 pt-4 border-top">
      <h3 class="fw-bold mb-4">Related <?php echo e($event['category']); ?> Events</h3>
      <div class="row g-4">
        <?php foreach ($related_events as $rel): ?>
          <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
              <img src="<?php echo get_event_image_url($rel['image'], $rel['category']); ?>" class="card-img-top" style="height: 140px; object-fit: cover;" alt="<?php echo e($rel['title']); ?>">
              <div class="card-body p-3 d-flex flex-direction-column">
                <span class="badge <?php echo get_category_badge_class($rel['category']); ?> mb-2 align-self-start small"><?php echo e($rel['category']); ?></span>
                <h6 class="fw-bold mb-2">
                  <a href="<?php echo base_url('event-details.php?id=' . $rel['id']); ?>" class="text-dark text-decoration-none">
                    <?php echo e($rel['title']); ?>
                  </a>
                </h6>
                <div class="text-muted small mt-auto">
                  <i class="fas fa-calendar-day text-purple me-1"></i> <?php echo format_date($rel['event_date']); ?>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

