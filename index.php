<?php
/**
 * Home Page
 * CampusPulse — College Event Management Website
 */

$page_title = 'Discover. Participate. Celebrate.';
require_once __DIR__ . '/includes/header.php';

// Fetch stats and upcoming events from DB
$pdo = get_db_connection();

$total_events = 0;
$total_students = 0;
$completed_events = 0;
$upcoming_events = [];
$category_counts = [];

if ($pdo) {
    try {
        // Overall Statistics
        $total_events = (int)$pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
        $total_students = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();
        $completed_events = (int)$pdo->query("SELECT COUNT(*) FROM events WHERE status = 'completed' OR event_date < CURDATE()")->fetchColumn();

        // Upcoming events (limit 6)
        $stmt = $pdo->prepare("
            SELECT e.*, 
                   COUNT(r.id) AS registered_count 
            FROM events e
            LEFT JOIN registrations r ON e.id = r.event_id AND r.status = 'confirmed'
            WHERE e.status = 'upcoming' AND e.event_date >= CURDATE()
            GROUP BY e.id
            ORDER BY e.event_date ASC
            LIMIT 6
        ");
        $stmt->execute();
        $upcoming_events = $stmt->fetchAll();

        // Category counts
        $catStmt = $pdo->query("SELECT category, COUNT(*) as count FROM events WHERE status != 'cancelled' GROUP BY category");
        while ($row = $catStmt->fetch()) {
            $category_counts[$row['category']] = (int)$row['count'];
        }
    } catch (PDOException $e) {
        error_log("Home query error: " . $e->getMessage());
    }
}
?>

<!-- Hero Section -->
<section class="hero-section">
  <div class="container position-relative">
    <div class="row align-items-center g-5">
      <div class="col-lg-7">
        <div class="hero-badge">
          <i class="fas fa-sparkles text-warning"></i>
          <span>Campus Life &bull; Academic Year 2025–2026</span>
        </div>
        <h1 class="hero-title">
          Discover. Participate. <br>
          <span class="text-gradient">Celebrate Campus Life.</span>
        </h1>
        <p class="hero-lead">
          Your one-stop gateway to campus happenings. Discover technical symposiums, cultural fests, hackathons, and sports tournaments. Register with a single click and create unforgettable memories.
        </p>

        <!-- Search Bar within Hero -->
        <form action="<?php echo base_url('events.php'); ?>" method="GET" class="mb-4">
          <div class="input-group p-1 bg-white rounded-pill shadow-lg" style="max-width: 520px;">
            <span class="input-group-text bg-transparent border-0 ps-3">
              <i class="fas fa-search text-muted"></i>
            </span>
            <input type="text" name="search" class="form-control border-0 shadow-none ps-2" placeholder="Search events by name, topic, or keyword...">
            <button class="btn btn-gradient-primary rounded-pill px-4" type="submit">
              Explore
            </button>
          </div>
        </form>

        <div class="d-flex flex-wrap gap-3 align-items-center">
          <a href="<?php echo base_url('events.php'); ?>" class="btn btn-gradient-primary btn-lg">
            <i class="fas fa-compass me-1"></i> Browse All Events
          </a>
          <?php if (!is_logged_in()): ?>
            <a href="<?php echo base_url('register.php'); ?>" class="btn btn-gradient-secondary btn-lg">
              <i class="fas fa-user-plus me-1"></i> Join as Student
            </a>
          <?php else: ?>
            <a href="<?php echo is_admin() ? base_url('admin/dashboard.php') : base_url('student/dashboard.php'); ?>" class="btn btn-gradient-secondary btn-lg">
              <i class="fas fa-tachometer-alt me-1"></i> Go to Dashboard
            </a>
          <?php endif; ?>
        </div>
      </div>

      <div class="col-lg-5">
        <!-- Floating Highlights Card -->
        <div class="hero-floating-card">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="badge bg-purple px-3 py-1 rounded-pill"><i class="fas fa-star text-warning me-1"></i> Next Major Fest</span>
            <span class="text-white-50 small"><i class="fas fa-clock me-1"></i> Registrations Open</span>
          </div>

          <h3 class="h4 text-white mb-2">TechFest 2026 Symposium</h3>
          <p class="text-light small mb-3">
            Annual national engineering symposium featuring AI robotics showcases, paper presentations, and $10,000+ prize pools.
          </p>

          <div class="p-3 bg-dark bg-opacity-50 rounded-3 mb-3 border border-secondary border-opacity-25">
            <div class="row text-center g-2">
              <div class="col-4 border-end border-secondary border-opacity-25">
                <span class="d-block fs-5 fw-bold text-info">14</span>
                <small class="text-white-50">Days Left</small>
              </div>
              <div class="col-4 border-end border-secondary border-opacity-25">
                <span class="d-block fs-5 fw-bold text-warning">250</span>
                <small class="text-white-50">Capacity</small>
              </div>
              <div class="col-4">
                <span class="d-block fs-5 fw-bold text-success">Main Hall</span>
                <small class="text-white-50">Auditorium</small>
              </div>
            </div>
          </div>

          <a href="<?php echo base_url('events.php'); ?>" class="btn btn-light w-100 fw-bold py-2 text-dark">
            <i class="fas fa-ticket-alt me-1 text-purple"></i> Reserve Your Spot
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Event Categories Section -->
<section class="py-5 bg-white">
  <div class="container py-4">
    <div class="text-center mb-5">
      <span class="text-uppercase fw-bold text-purple small letter-spacing-1">Explore by Passion</span>
      <h2 class="fw-bold mt-1">Browse Event Categories</h2>
      <p class="text-muted mx-auto" style="max-width: 600px;">
        Whether you love coding under the clock, showcasing stage drama, hitting the tracks, or mastering AI, find the community that moves you.
      </p>
    </div>

    <div class="row g-4">
      <!-- Technical -->
      <div class="col-xl-2 col-lg-4 col-md-4 col-6">
        <a href="<?php echo base_url('events.php?category=Technical'); ?>" class="category-card text-decoration-none">
          <div class="category-icon-box bg-primary-subtle text-primary">
            <i class="fas fa-microchip"></i>
          </div>
          <h5 class="fs-6 fw-bold text-dark mb-1">Technical</h5>
          <span class="badge bg-light text-muted border"><?php echo $category_counts['Technical'] ?? '3'; ?> Events</span>
        </a>
      </div>

      <!-- Cultural -->
      <div class="col-xl-2 col-lg-4 col-md-4 col-6">
        <a href="<?php echo base_url('events.php?category=Cultural'); ?>" class="category-card text-decoration-none">
          <div class="category-icon-box bg-danger-subtle text-danger">
            <i class="fas fa-masks-theater"></i>
          </div>
          <h5 class="fs-6 fw-bold text-dark mb-1">Cultural</h5>
          <span class="badge bg-light text-muted border"><?php echo $category_counts['Cultural'] ?? '2'; ?> Events</span>
        </a>
      </div>

      <!-- Sports -->
      <div class="col-xl-2 col-lg-4 col-md-4 col-6">
        <a href="<?php echo base_url('events.php?category=Sports'); ?>" class="category-card text-decoration-none">
          <div class="category-icon-box bg-success-subtle text-success">
            <i class="fas fa-trophy"></i>
          </div>
          <h5 class="fs-6 fw-bold text-dark mb-1">Sports</h5>
          <span class="badge bg-light text-muted border"><?php echo $category_counts['Sports'] ?? '1'; ?> Events</span>
        </a>
      </div>

      <!-- Workshops -->
      <div class="col-xl-2 col-lg-4 col-md-4 col-6">
        <a href="<?php echo base_url('events.php?category=Workshops'); ?>" class="category-card text-decoration-none">
          <div class="category-icon-box bg-purple-soft text-purple">
            <i class="fas fa-laptop-code"></i>
          </div>
          <h5 class="fs-6 fw-bold text-dark mb-1">Workshops</h5>
          <span class="badge bg-light text-muted border"><?php echo $category_counts['Workshops'] ?? '1'; ?> Events</span>
        </a>
      </div>

      <!-- Seminars -->
      <div class="col-xl-2 col-lg-4 col-md-4 col-6">
        <a href="<?php echo base_url('events.php?category=Seminars'); ?>" class="category-card text-decoration-none">
          <div class="category-icon-box bg-warning-subtle text-warning">
            <i class="fas fa-lightbulb"></i>
          </div>
          <h5 class="fs-6 fw-bold text-dark mb-1">Seminars</h5>
          <span class="badge bg-light text-muted border"><?php echo $category_counts['Seminars'] ?? '2'; ?> Events</span>
        </a>
      </div>

      <!-- All Events -->
      <div class="col-xl-2 col-lg-4 col-md-4 col-6">
        <a href="<?php echo base_url('events.php'); ?>" class="category-card text-decoration-none border-primary">
          <div class="category-icon-box bg-dark text-white">
            <i class="fas fa-arrow-right"></i>
          </div>
          <h5 class="fs-6 fw-bold text-dark mb-1">View All</h5>
          <span class="badge bg-dark text-white">All Events</span>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- Upcoming Events Section -->
<section class="py-5 bg-light">
  <div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
      <div>
        <span class="text-uppercase fw-bold text-purple small">Don't Miss Out</span>
        <h2 class="fw-bold mb-0">Upcoming College Events</h2>
      </div>
      <a href="<?php echo base_url('events.php'); ?>" class="btn btn-outline-purple mt-3 mt-md-0">
        View Full Schedule <i class="fas fa-arrow-right ms-1"></i>
      </a>
    </div>

    <div class="row g-4">
      <?php if (!empty($upcoming_events)): ?>
        <?php foreach ($upcoming_events as $event): ?>
          <?php
            $reg_count = (int)($event['registered_count'] ?? 0);
            $capacity = (int)$event['capacity'];
            $seats_left = max(0, $capacity - $reg_count);
            $deadline_passed = is_deadline_passed($event['registration_deadline']);
            $is_full = ($seats_left <= 0);
          ?>
          <div class="col-lg-4 col-md-6">
            <div class="event-card">
              <div class="event-card-img-wrapper">
                <img src="<?php echo get_event_image_url($event['image'], $event['category']); ?>" alt="<?php echo e($event['title']); ?>" loading="lazy">
                <span class="event-category-badge <?php echo get_category_badge_class($event['category']); ?>">
                  <?php echo e($event['category']); ?>
                </span>
                <span class="event-date-chip">
                  <i class="fas fa-calendar-day"></i> <?php echo format_date($event['event_date'], 'M d'); ?>
                </span>
              </div>

              <div class="event-card-body">
                <h3 class="event-card-title">
                  <a href="<?php echo base_url('event-details.php?id=' . $event['id']); ?>">
                    <?php echo e($event['title']); ?>
                  </a>
                </h3>
                <p class="event-card-desc"><?php echo e($event['description']); ?></p>

                <ul class="event-meta-list">
                  <li class="event-meta-item">
                    <i class="fas fa-clock"></i>
                    <span><?php echo format_time($event['start_time']); ?> - <?php echo format_time($event['end_time']); ?></span>
                  </li>
                  <li class="event-meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span class="text-truncate"><?php echo e($event['venue']); ?></span>
                  </li>
                  <li class="event-meta-item">
                    <i class="fas fa-users"></i>
                    <span><strong><?php echo $seats_left; ?></strong> seats available (of <?php echo $capacity; ?>)</span>
                  </li>
                </ul>
              </div>

              <div class="event-card-footer">
                <a href="<?php echo base_url('event-details.php?id=' . $event['id']); ?>" class="btn btn-sm btn-outline-secondary">
                  Details
                </a>

                <?php if ($deadline_passed): ?>
                  <button class="btn btn-sm btn-secondary disabled" disabled>Deadline Passed</button>
                <?php elseif ($is_full): ?>
                  <button class="btn btn-sm btn-danger disabled" disabled>Event Full</button>
                <?php else: ?>
                  <a href="<?php echo base_url('event-details.php?id=' . $event['id']); ?>" class="btn btn-sm btn-gradient-primary">
                    <i class="fas fa-ticket-alt me-1"></i> Register
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12">
          <div class="card p-5 text-center border-0 shadow-sm">
            <div class="empty-state-icon mx-auto">
              <i class="fas fa-calendar-times"></i>
            </div>
            <h4 class="fw-bold">No upcoming events scheduled</h4>
            <p class="text-muted">Check back soon or explore past campus events.</p>
            <div class="mt-2">
              <a href="<?php echo base_url('events.php'); ?>" class="btn btn-gradient-primary">Browse All Events</a>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Statistics Section -->
<section class="stats-section">
  <div class="container position-relative">
    <div class="row g-4 text-center">
      <div class="col-lg-3 col-sm-6">
        <div class="stat-item">
          <i class="fas fa-calendar-check fs-2 text-info mb-2"></i>
          <div class="stat-number stat-counter" data-target="<?php echo max(15, $total_events); ?>" data-suffix="+">
            <?php echo max(15, $total_events); ?>+
          </div>
          <div class="stat-label">Total Events Hosted</div>
        </div>
      </div>

      <div class="col-lg-3 col-sm-6">
        <div class="stat-item">
          <i class="fas fa-user-graduate fs-2 text-warning mb-2"></i>
          <div class="stat-number stat-counter" data-target="<?php echo max(1200, $total_students * 50); ?>" data-suffix="+">
            1,200+
          </div>
          <div class="stat-label">Registered Students</div>
        </div>
      </div>

      <div class="col-lg-3 col-sm-6">
        <div class="stat-item">
          <i class="fas fa-award fs-2 text-success mb-2"></i>
          <div class="stat-number stat-counter" data-target="<?php echo max(35, $completed_events + 20); ?>" data-suffix="+">
            35+
          </div>
          <div class="stat-label">Completed Milestones</div>
        </div>
      </div>

      <div class="col-lg-3 col-sm-6">
        <div class="stat-item">
          <i class="fas fa-sitemap fs-2 text-purple mb-2"></i>
          <div class="stat-number stat-counter" data-target="24" data-suffix="">
            24
          </div>
          <div class="stat-label">Active Clubs &amp; Societies</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Testimonials Section -->
<section class="py-5 bg-white">
  <div class="container py-4">
    <div class="text-center mb-5">
      <span class="text-uppercase fw-bold text-purple small">Student Voices</span>
      <h2 class="fw-bold mt-1">What Students Say About CampusPulse</h2>
      <p class="text-muted mx-auto" style="max-width: 600px;">
        Hear how our unified event management platform is transforming student life, competition participation, and campus networking.
      </p>
    </div>

    <div class="row g-4">
      <div class="col-lg-4 col-md-6">
        <div class="testimonial-card">
          <i class="fas fa-quote-right testimonial-quote-icon"></i>
          <div class="d-flex align-items-center gap-3 mb-3">
            <div class="student-avatar">AS</div>
            <div>
              <h6 class="fw-bold mb-0">Aarav Sharma</h6>
              <small class="text-muted">Computer Science &bull; 3rd Year</small>
            </div>
          </div>
          <p class="text-secondary mb-3">
            "Registering for CodeSprint was seamless! In previous semesters we had to stand in long department lines. Now I get instant confirmation passes on my dashboard."
          </p>
          <div class="text-warning small">
            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-md-6">
        <div class="testimonial-card">
          <i class="fas fa-quote-right testimonial-quote-icon"></i>
          <div class="d-flex align-items-center gap-3 mb-3">
            <div class="student-avatar" style="background: linear-gradient(135deg, #ec4899, #f43f5e);">PP</div>
            <div>
              <h6 class="fw-bold mb-0">Priya Patel</h6>
              <small class="text-muted">Information Technology &bull; 2nd Year</small>
            </div>
          </div>
          <p class="text-secondary mb-3">
            "The Cultural Fiesta stage schedule was updated live on the website. I loved how easily I could browse categories and filter workshops fitting my timetable."
          </p>
          <div class="text-warning small">
            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-md-6">
        <div class="testimonial-card">
          <i class="fas fa-quote-right testimonial-quote-icon"></i>
          <div class="d-flex align-items-center gap-3 mb-3">
            <div class="student-avatar" style="background: linear-gradient(135deg, #059669, #10b981);">MC</div>
            <div>
              <h6 class="fw-bold mb-0">Marcus Chen</h6>
              <small class="text-muted">Electronics &bull; 4th Year</small>
            </div>
          </div>
          <p class="text-secondary mb-3">
            "As an event lead for the Robotics club, the administrator dashboard makes tracking attendee capacity and exporting registration CSV rosters so painless!"
          </p>
          <div class="text-warning small">
            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Call to Action Banner -->
<section class="py-5 bg-light">
  <div class="container">
    <div class="p-5 rounded-4 shadow-lg text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #31104b 50%, #4c1d95 100%);">
      <div class="row align-items-center position-relative g-4">
        <div class="col-lg-8">
          <h2 class="fw-bold mb-2">Ready to Make Your Mark on Campus?</h2>
          <p class="text-light mb-0 fs-5 opacity-90">
            Sign up today with your student email, browse upcoming competitions and symposiums, and never miss a registration deadline again.
          </p>
        </div>
        <div class="col-lg-4 text-lg-end">
          <a href="<?php echo base_url('register.php'); ?>" class="btn btn-light btn-lg px-4 py-3 fw-bold text-dark shadow">
            <i class="fas fa-rocket text-purple me-2"></i> Create Free Account
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

