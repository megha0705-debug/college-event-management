<?php
/**
 * Contact Us Page
 * CampusPulse — College Event Management Website
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = get_db_connection();
$errors = [];
$success_message = false;

$form_name = '';
$form_email = '';
$form_subject = '';
$form_message = '';

// Pre-fill user data if signed in
if (is_logged_in()) {
    $form_name = $_SESSION['user_name'] ?? '';
    $form_email = $_SESSION['user_email'] ?? '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security token invalid or expired. Please submit the form again.';
    }

    $form_name    = sanitize_input($_POST['name'] ?? '');
    $form_email   = sanitize_input($_POST['email'] ?? '');
    $form_subject = sanitize_input($_POST['subject'] ?? '');
    $form_message = trim($_POST['message'] ?? '');

    if (empty($form_name) || strlen($form_name) < 2) {
        $errors[] = 'Please enter your full name.';
    }

    if (empty($form_email) || !filter_var($form_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (empty($form_subject) || strlen($form_subject) < 4) {
        $errors[] = 'Subject must be at least 4 characters long.';
    }

    if (empty($form_message) || strlen($form_message) < 10) {
        $errors[] = 'Message content must be at least 10 characters long.';
    }

    if (empty($errors) && $pdo) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO contact_messages (name, email, subject, message, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$form_name, $form_email, $form_subject, $form_message]);

            $success_message = true;
            flash_set('success', 'Thank you! Your inquiry has been received by the Event Coordination Desk. We will get back to you shortly.');

            // Clear form fields
            $form_subject = '';
            $form_message = '';
        } catch (PDOException $e) {
            $errors[] = 'Could not save message due to database error.';
            error_log($e->getMessage());
        }
    }
}

$page_title = 'Contact & Support';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Contact Hero -->
<div class="py-5 text-white" style="background: linear-gradient(135deg, #0a0f1d 0%, #1e1b4b 60%, #31104b 100%);">
  <div class="container py-3 text-center">
    <span class="badge bg-purple px-3 py-1 rounded-pill mb-2">We Are Here to Assist You</span>
    <h1 class="display-6 fw-bold mb-2">Get in Touch with CampusPulse</h1>
    <p class="text-light mx-auto mb-0" style="max-width: 650px; opacity: 0.9;">
      Have a question regarding event registrations, venue logistics, sponsorship, or student club partnerships? Reach out to our campus team.
    </p>
  </div>
</div>

<div class="container py-5">
  <div class="row g-5">
    <!-- Contact Form -->
    <div class="col-lg-7">
      <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
        <h3 class="fw-bold mb-1">Send Us a Message</h3>
        <p class="text-muted small mb-4">Complete the form below and our campus event coordinators will respond within 24 hours.</p>

        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger shadow-sm mb-4">
            <ul class="mb-0 ps-3 small">
              <?php foreach ($errors as $err): ?>
                <li><?php echo e($err); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
          <div class="alert alert-success shadow-sm mb-4">
            <i class="fas fa-check-circle me-1"></i> Your message was submitted successfully! Our committee will reply to <strong><?php echo e($form_email); ?></strong> soon.
          </div>
        <?php endif; ?>

        <form action="<?php echo base_url('contact.php'); ?>" method="POST" class="needs-validation" novalidate>
          <?php echo csrf_field(); ?>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Your Full Name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control" placeholder="Aarav Sharma" value="<?php echo e($form_name); ?>" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Email Address <span class="text-danger">*</span></label>
              <input type="email" name="email" class="form-control" placeholder="student@college.edu" value="<?php echo e($form_email); ?>" required>
            </div>

            <div class="col-12">
              <label class="form-label">Subject / Topic <span class="text-danger">*</span></label>
              <input type="text" name="subject" class="form-control" placeholder="e.g. Inquiring about Hackathon team registration" value="<?php echo e($form_subject); ?>" required>
            </div>

            <div class="col-12">
              <label class="form-label">Your Message <span class="text-danger">*</span></label>
              <textarea name="message" class="form-control" rows="5" placeholder="Write your questions, feedback, or sponsorship inquiry here..." required><?php echo e($form_message); ?></textarea>
            </div>

            <div class="col-12 mt-4">
              <button type="submit" class="btn btn-gradient-primary w-100 py-3 fs-6">
                <i class="fas fa-paper-plane me-2"></i> Submit Message
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- College Contact Details & Campus Map Card -->
    <div class="col-lg-5">
      <div class="card border-0 shadow-sm rounded-4 p-4 p-md-4 bg-white mb-4">
        <h4 class="fw-bold mb-4">College Contact Information</h4>

        <div class="d-flex align-items-start gap-3 mb-4">
          <div class="brand-icon flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.1rem; background: #e0e7ff; color: #4338ca;">
            <i class="fas fa-location-dot"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-1">Campus Address</h6>
            <p class="text-secondary small mb-0">
              Apex Institute of Technology &bull; Student Affairs Center<br>
              Innovation Boulevard, Building 4, Knowledge Park<br>
              Campus Postal Code: 560001
            </p>
          </div>
        </div>

        <div class="d-flex align-items-start gap-3 mb-4">
          <div class="brand-icon flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.1rem; background: #dcfce7; color: #15803d;">
            <i class="fas fa-phone"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-1">Event Helpline &amp; WhatsApp</h6>
            <p class="text-secondary small mb-0">
              Helpline: +1 (800) 555-EVENT<br>
              Office Desk: +1 (555) 019-2834
            </p>
          </div>
        </div>

        <div class="d-flex align-items-start gap-3 mb-4">
          <div class="brand-icon flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.1rem; background: #ede9fe; color: #7c3aed;">
            <i class="fas fa-envelope"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-1">Email Inquiries</h6>
            <p class="text-secondary small mb-0">
              General Events: <a href="mailto:events@college.edu">events@college.edu</a><br>
              Technical Support: <a href="mailto:support@college.edu">support@college.edu</a>
            </p>
          </div>
        </div>

        <div class="d-flex align-items-start gap-3">
          <div class="brand-icon flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.1rem; background: #fef3c7; color: #b45309;">
            <i class="fas fa-clock"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-1">Desk Working Hours</h6>
            <p class="text-secondary small mb-0">
              Monday &ndash; Friday: 8:30 AM &ndash; 5:30 PM<br>
              Saturday: 9:00 AM &ndash; 1:00 PM (During Fest Weeks)
            </p>
          </div>
        </div>
      </div>

      <!-- Campus Map Illustration -->
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-navy text-white p-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6 class="fw-bold mb-0 text-white"><i class="fas fa-map me-2 text-warning"></i> Campus Navigation</h6>
          <span class="badge bg-purple">Main Auditorium</span>
        </div>
        <p class="small text-light opacity-75 mb-3">
          Most flagship events are organized at the Central Auditorium and the Student Recreation &amp; Sports Complex.
        </p>
        <div class="p-3 bg-dark bg-opacity-50 rounded-3 text-center border border-secondary border-opacity-25">
          <i class="fas fa-landmark fs-1 text-purple mb-2"></i>
          <div class="small fw-bold">College Main Campus Gateway</div>
          <small class="text-muted">Security Checkpoint 1 &bull; Visitor Passes Issued at Gate</small>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

