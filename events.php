<?php
/**
 * Events Directory Page
 * CampusPulse — College Event Management Website
 */

$page_title = 'Explore Campus Events';
require_once __DIR__ . '/includes/header.php';

$pdo = get_db_connection();

// Filters from Query Parameters
$search   = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');
$status   = trim($_GET['status'] ?? 'upcoming'); // upcoming, past, all
$sort     = trim($_GET['sort'] ?? 'date_asc');   // date_asc, date_desc, capacity

$where = [];
$params = [];

// Search condition
if (!empty($search)) {
    $where[] = "(e.title LIKE ? OR e.description LIKE ? OR e.venue LIKE ? OR e.organizer LIKE ?)";
    $term = "%{$search}%";
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
}

// Category condition
if (!empty($category) && $category !== 'All') {
    $where[] = "e.category = ?";
    $params[] = $category;
}

// Status condition
$today = date('Y-m-d');
if ($status === 'upcoming') {
    $where[] = "e.event_date >= ? AND e.status != 'cancelled'";
    $params[] = $today;
} elseif ($status === 'past') {
    $where[] = "(e.event_date < ? OR e.status = 'completed')";
    $params[] = $today;
} elseif ($status === 'all') {
    // No date restriction
}

// Order by
$order_sql = "e.event_date ASC, e.start_time ASC";
if ($sort === 'date_desc') {
    $order_sql = "e.event_date DESC, e.start_time DESC";
} elseif ($sort === 'capacity') {
    $order_sql = "e.capacity DESC";
}

$where_clause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

$events = [];
if ($pdo) {
    try {
        $sql = "
            SELECT e.*, 
                   COUNT(r.id) AS registered_count 
            FROM events e
            LEFT JOIN registrations r ON e.id = r.event_id AND r.status = 'confirmed'
            {$where_clause}
            GROUP BY e.id
            ORDER BY {$order_sql}
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $events = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Events search error: " . $e->getMessage());
    }
}

// Available categories for dropdown
$categories_list = ['Technical', 'Cultural', 'Sports', 'Workshops', 'Seminars'];
?>

<div class="bg-navy py-4 text-white mb-4" style="background: linear-gradient(135deg, #0a0f1d 0%, #1e1b4b 100%);">
  <div class="container py-2">
    <div class="row align-items-center">
      <div class="col-md-7">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-2 small">
            <li class="breadcrumb-item"><a href="<?php echo base_url('index.php'); ?>" class="text-white-50">Home</a></li>
            <li class="breadcrumb-item active text-white" aria-current="page">Events Directory</li>
          </ol>
        </nav>
        <h1 class="h2 fw-bold mb-1 text-white">Campus Events Directory</h1>
        <p class="text-white-50 mb-0">Discover upcoming college symposiums, workshops, and extracurricular tournaments.</p>
      </div>
      <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <span class="badge bg-purple px-3 py-2 fs-6">
          <i class="fas fa-calendar-check me-1"></i> <?php echo count($events); ?> Events Available
        </span>
      </div>
    </div>
  </div>
</div>

<div class="container mb-5">
  <!-- Filter & Search Bar -->
  <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
    <form action="<?php echo base_url('events.php'); ?>" method="GET" class="row g-3 align-items-end">
      <!-- Search Input -->
      <div class="col-lg-4 col-md-6">
        <label class="form-label small text-muted mb-1"><i class="fas fa-search me-1"></i> Search by Keyword</label>
        <input type="text" name="search" class="form-control" placeholder="Search title, venue, organizer..." value="<?php echo e($search); ?>">
      </div>

      <!-- Category Filter -->
      <div class="col-lg-3 col-md-6">
        <label class="form-label small text-muted mb-1"><i class="fas fa-tag me-1"></i> Category</label>
        <select name="category" class="form-select">
          <option value="">All Categories</option>
          <?php foreach ($categories_list as $cat): ?>
            <option value="<?php echo e($cat); ?>" <?php echo ($category === $cat) ? 'selected' : ''; ?>>
              <?php echo e($cat); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Event Status Timeline -->
      <div class="col-lg-2 col-md-6">
        <label class="form-label small text-muted mb-1"><i class="fas fa-clock me-1"></i> Timeline</label>
        <select name="status" class="form-select">
          <option value="upcoming" <?php echo ($status === 'upcoming') ? 'selected' : ''; ?>>Upcoming Only</option>
          <option value="past" <?php echo ($status === 'past') ? 'selected' : ''; ?>>Past Events</option>
          <option value="all" <?php echo ($status === 'all') ? 'selected' : ''; ?>>All (Any Date)</option>
        </select>
      </div>

      <!-- Sort Filter -->
      <div class="col-lg-2 col-md-6">
        <label class="form-label small text-muted mb-1"><i class="fas fa-sort me-1"></i> Sort By</label>
        <select name="sort" class="form-select">
          <option value="date_asc" <?php echo ($sort === 'date_asc') ? 'selected' : ''; ?>>Date: Earliest First</option>
          <option value="date_desc" <?php echo ($sort === 'date_desc') ? 'selected' : ''; ?>>Date: Latest First</option>
          <option value="capacity" <?php echo ($sort === 'capacity') ? 'selected' : ''; ?>>Largest Capacity</option>
        </select>
      </div>

      <!-- Submit & Clear Buttons -->
      <div class="col-lg-1 col-md-12 d-flex gap-2">
        <button type="submit" class="btn btn-gradient-primary w-100" title="Apply Filters">
          <i class="fas fa-filter"></i>
        </button>
        <?php if (!empty($search) || !empty($category) || $status !== 'upcoming' || $sort !== 'date_asc'): ?>
          <a href="<?php echo base_url('events.php'); ?>" class="btn btn-outline-secondary" title="Reset Filters">
            <i class="fas fa-times"></i>
          </a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- Event Grid -->
  <?php if (!empty($events)): ?>
    <div class="row g-4">
      <?php foreach ($events as $event): ?>
        <?php
          $reg_count = (int)($event['registered_count'] ?? 0);
          $capacity = (int)$event['capacity'];
          $seats_left = max(0, $capacity - $reg_count);
          $deadline_passed = is_deadline_passed($event['registration_deadline']);
          $is_past = (strtotime($event['event_date']) < strtotime(date('Y-m-d')));
          $is_cancelled = ($event['status'] === 'cancelled');
          $is_full = ($seats_left <= 0);
          $pct_filled = ($capacity > 0) ? min(100, round(($reg_count / $capacity) * 100)) : 0;
        ?>
        <div class="col-lg-4 col-md-6">
          <div class="event-card">
            <div class="event-card-img-wrapper">
              <img src="<?php echo get_event_image_url($event['image'], $event['category']); ?>" alt="<?php echo e($event['title']); ?>" loading="lazy">
              
              <!-- Category Badge -->
              <span class="event-category-badge <?php echo get_category_badge_class($event['category']); ?>">
                <?php echo e($event['category']); ?>
              </span>

              <!-- Status Badge (if cancelled or completed) -->
              <?php if ($is_cancelled): ?>
                <span class="event-status-badge bg-danger text-white">Cancelled</span>
              <?php elseif ($is_past || $event['status'] === 'completed'): ?>
                <span class="event-status-badge bg-secondary text-white">Concluded</span>
              <?php elseif ($is_full): ?>
                <span class="event-status-badge bg-warning text-dark">Seats Full</span>
              <?php endif; ?>

              <!-- Event Date Chip -->
              <span class="event-date-chip">
                <i class="fas fa-calendar-day"></i> <?php echo format_date($event['event_date'], 'M d, Y'); ?>
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
                  <span><?php echo format_time($event['start_time']); ?> &ndash; <?php echo format_time($event['end_time']); ?></span>
                </li>
                <li class="event-meta-item">
                  <i class="fas fa-map-marker-alt"></i>
                  <span class="text-truncate"><?php echo e($event['venue']); ?></span>
                </li>
                <li class="event-meta-item">
                  <i class="fas fa-user-tie"></i>
                  <span class="text-truncate"><?php echo e($event['organizer']); ?></span>
                </li>
              </ul>

              <!-- Capacity Progress Indicator -->
              <div class="mt-auto pt-2">
                <div class="d-flex justify-content-between small text-muted mb-1">
                  <span>Seats Availability</span>
                  <span><strong><?php echo $seats_left; ?></strong> left / <?php echo $capacity; ?> total</span>
                </div>
                <div class="progress" style="height: 6px;">
                  <div class="progress-bar <?php echo ($pct_filled > 85) ? 'bg-danger' : 'bg-primary'; ?>" 
                       role="progressbar" 
                       style="width: <?php echo $pct_filled; ?>%;" 
                       aria-valuenow="<?php echo $pct_filled; ?>" 
                       aria-valuemin="0" 
                       aria-valuemax="100"></div>
                </div>
              </div>
            </div>

            <div class="event-card-footer">
              <a href="<?php echo base_url('event-details.php?id=' . $event['id']); ?>" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-eye me-1"></i> View Details
              </a>

              <?php if ($is_cancelled): ?>
                <button class="btn btn-sm btn-secondary disabled" disabled>Cancelled</button>
              <?php elseif ($is_past || $event['status'] === 'completed'): ?>
                <button class="btn btn-sm btn-light border text-muted disabled" disabled>Concluded</button>
              <?php elseif ($deadline_passed): ?>
                <button class="btn btn-sm btn-secondary disabled" disabled>Closed</button>
              <?php elseif ($is_full): ?>
                <button class="btn btn-sm btn-danger disabled" disabled>Full</button>
              <?php else: ?>
                <a href="<?php echo base_url('event-details.php?id=' . $event['id']); ?>" class="btn btn-sm btn-gradient-primary">
                  <i class="fas fa-ticket-alt me-1"></i> Register Now
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <!-- Empty State -->
    <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
      <div class="empty-state-icon mx-auto">
        <i class="fas fa-search"></i>
      </div>
      <h3 class="fw-bold mb-2">No Events Found</h3>
      <p class="text-muted mx-auto" style="max-width: 480px;">
        We couldn't find any college events matching your current filters or search query. Try refining your keyword or resetting filters.
      </p>
      <div class="mt-3">
        <a href="<?php echo base_url('events.php'); ?>" class="btn btn-gradient-primary">
          <i class="fas fa-redo me-1"></i> Reset All Filters
        </a>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

