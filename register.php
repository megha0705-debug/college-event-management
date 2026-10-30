<?php
/**
 * Student Registration Page
 * CampusPulse — College Event Management Website
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

// Redirect if already logged in
if (is_logged_in()) {
    header("Location: " . (is_admin() ? base_url('admin/dashboard.php') : base_url('student/dashboard.php')));
    exit;
}

$pdo = get_db_connection();
$errors = [];
$form_data = [
    'name' => '',
    'email' => '',
    'student_id' => '',
    'department' => '',
    'year_of_study' => '',
    'phone' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Verify CSRF Token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security token invalid or expired. Please submit the form again.';
    }

    // 2. Collect and sanitize inputs
    $form_data['name']          = sanitize_input($_POST['name'] ?? '');
    $form_data['email']         = strtolower(sanitize_input($_POST['email'] ?? ''));
    $form_data['student_id']    = strtoupper(sanitize_input($_POST['student_id'] ?? ''));
    $form_data['department']    = sanitize_input($_POST['department'] ?? '');
    $form_data['year_of_study'] = sanitize_input($_POST['year_of_study'] ?? '');
    $form_data['phone']         = sanitize_input($_POST['phone'] ?? '');
    $password                   = $_POST['password'] ?? '';
    $confirm_password           = $_POST['confirm_password'] ?? '';

    // 3. Validation checks
    if (empty($form_data['name']) || strlen($form_data['name']) < 3) {
        $errors[] = 'Full name must be at least 3 characters long.';
    }

    if (empty($form_data['email']) || !filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please provide a valid email address.';
    }

    if (empty($form_data['student_id'])) {
        $errors[] = 'Student ID / Roll Number is required.';
    }

    if (empty($form_data['department'])) {
        $errors[] = 'Please select your academic department.';
    }

    if (empty($form_data['year_of_study'])) {
        $errors[] = 'Please select your current year of study.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }

    if ($password !== $confirm_password) {
        $errors[] = 'Password confirmation does not match.';
    }

    // 4. Check for duplicate email or student ID in database
    if (empty($errors) && $pdo) {
        try {
            $checkStmt = $pdo->prepare("SELECT id, email, student_id FROM users WHERE email = ? OR student_id = ? LIMIT 1");
            $checkStmt->execute([$form_data['email'], $form_data['student_id']]);
            $existingUser = $checkStmt->fetch();

            if ($existingUser) {
                if (strtolower($existingUser['email']) === $form_data['email']) {
                    $errors[] = 'An account with this email address already exists. Please sign in instead.';
                } else {
                    $errors[] = 'An account with this Student ID is already registered.';
                }
            }
        } catch (PDOException $e) {
            $errors[] = 'Database error checking existing account.';
            error_log($e->getMessage());
        }
    }

    // 5. Create new Student Account
    if (empty($errors) && $pdo) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $insertStmt = $pdo->prepare("
                INSERT INTO users (name, email, student_id, department, year_of_study, phone, password, role, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'student', NOW())
            ");
            $insertStmt->execute([
                $form_data['name'],
                $form_data['email'],
                $form_data['student_id'],
                $form_data['department'],
                $form_data['year_of_study'],
                $form_data['phone'],
                $hashedPassword
            ]);

            $newUserId = (int)$pdo->lastInsertId();

            // Automatically log in the user
            $userRecord = [
                'id'            => $newUserId,
                'name'          => $form_data['name'],
                'email'         => $form_data['email'],
                'role'          => 'student',
                'student_id'    => $form_data['student_id'],
                'department'    => $form_data['department'],
                'year_of_study' => $form_data['year_of_study']
            ];
            login_user($userRecord);

            flash_set('success', 'Welcome to CampusPulse, ' . e($form_data['name']) . '! Your student account is now active.');
            header("Location: " . base_url('student/dashboard.php'));
            exit;

        } catch (PDOException $e) {
            $errors[] = 'Could not create account: ' . $e->getMessage();
            error_log($e->getMessage());
        }
    }
}

$page_title = 'Student Registration';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-7 col-md-9">
      <div class="form-card">
        <div class="text-center mb-4">
          <div class="brand-icon mx-auto mb-3" style="width: 52px; height: 52px; font-size: 1.5rem;">
            <i class="fas fa-user-plus"></i>
          </div>
          <h2 class="fw-bold mb-1">Create Student Account</h2>
          <p class="text-muted small">Register to participate in hackathons, symposiums, and sports championships.</p>
        </div>

        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger shadow-sm mb-4">
            <h6 class="fw-bold mb-2"><i class="fas fa-exclamation-circle me-1"></i> Registration Errors:</h6>
            <ul class="mb-0 ps-3 small">
              <?php foreach ($errors as $err): ?>
                <li><?php echo e($err); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form action="<?php echo base_url('register.php'); ?>" method="POST" class="needs-validation" novalidate>
          <?php echo csrf_field(); ?>

          <div class="row g-3">
            <!-- Full Name -->
            <div class="col-12">
              <label class="form-label">Full Name <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="fas fa-user text-muted"></i></span>
                <input type="text" name="name" class="form-control" placeholder="e.g. Aarav Sharma" value="<?php echo e($form_data['name']); ?>" required>
              </div>
            </div>

            <!-- Email Address -->
            <div class="col-md-6">
              <label class="form-label">College Email Address <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="fas fa-envelope text-muted"></i></span>
                <input type="email" name="email" class="form-control" placeholder="student@college.edu" value="<?php echo e($form_data['email']); ?>" required>
              </div>
            </div>

            <!-- Student ID -->
            <div class="col-md-6">
              <label class="form-label">Student ID / Roll No <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="fas fa-id-card text-muted"></i></span>
                <input type="text" name="student_id" class="form-control" placeholder="e.g. STU-2024-0101" value="<?php echo e($form_data['student_id']); ?>" required>
              </div>
            </div>

            <!-- Department -->
            <div class="col-md-6">
              <label class="form-label">Department <span class="text-danger">*</span></label>
              <select name="department" class="form-select" required>
                <option value="">Select Department...</option>
                <option value="Computer Science & Engineering" <?php echo ($form_data['department'] === 'Computer Science & Engineering') ? 'selected' : ''; ?>>Computer Science & Engineering</option>
                <option value="Information Technology" <?php echo ($form_data['department'] === 'Information Technology') ? 'selected' : ''; ?>>Information Technology</option>
                <option value="Electronics & Communication" <?php echo ($form_data['department'] === 'Electronics & Communication') ? 'selected' : ''; ?>>Electronics & Communication</option>
                <option value="Mechanical Engineering" <?php echo ($form_data['department'] === 'Mechanical Engineering') ? 'selected' : ''; ?>>Mechanical Engineering</option>
                <option value="Civil Engineering" <?php echo ($form_data['department'] === 'Civil Engineering') ? 'selected' : ''; ?>>Civil Engineering</option>
                <option value="Business Administration" <?php echo ($form_data['department'] === 'Business Administration') ? 'selected' : ''; ?>>Business Administration</option>
                <option value="Biotechnology & Sciences" <?php echo ($form_data['department'] === 'Biotechnology & Sciences') ? 'selected' : ''; ?>>Biotechnology & Sciences</option>
              </select>
            </div>

            <!-- Year of Study -->
            <div class="col-md-6">
              <label class="form-label">Year of Study <span class="text-danger">*</span></label>
              <select name="year_of_study" class="form-select" required>
                <option value="">Select Year...</option>
                <option value="1st Year" <?php echo ($form_data['year_of_study'] === '1st Year') ? 'selected' : ''; ?>>1st Year (Freshman)</option>
                <option value="2nd Year" <?php echo ($form_data['year_of_study'] === '2nd Year') ? 'selected' : ''; ?>>2nd Year (Sophomore)</option>
                <option value="3rd Year" <?php echo ($form_data['year_of_study'] === '3rd Year') ? 'selected' : ''; ?>>3rd Year (Junior)</option>
                <option value="4th Year" <?php echo ($form_data['year_of_study'] === '4th Year') ? 'selected' : ''; ?>>4th Year (Senior)</option>
                <option value="Postgraduate" <?php echo ($form_data['year_of_study'] === 'Postgraduate') ? 'selected' : ''; ?>>Postgraduate / Masters</option>
              </select>
            </div>

            <!-- Phone Number -->
            <div class="col-12">
              <label class="form-label">Phone Number (Optional)</label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="fas fa-phone text-muted"></i></span>
                <input type="tel" name="phone" class="form-control" placeholder="+1 (555) 012-3456" value="<?php echo e($form_data['phone']); ?>">
              </div>
            </div>

            <!-- Password -->
            <div class="col-md-6">
              <label class="form-label">Password <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="fas fa-lock text-muted"></i></span>
                <input type="password" name="password" class="form-control" placeholder="Min. 6 characters" minlength="6" required>
              </div>
            </div>

            <!-- Confirm Password -->
            <div class="col-md-6">
              <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="fas fa-lock-open text-muted"></i></span>
                <input type="password" name="confirm_password" class="form-control" placeholder="Re-enter password" minlength="6" required>
              </div>
            </div>

            <!-- Terms agreement checkbox -->
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="termsCheck" required>
                <label class="form-check-label small text-muted" for="termsCheck">
                  I agree to adhere to the College Event Code of Conduct and Participation Guidelines.
                </label>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="col-12 mt-4">
              <button type="submit" class="btn btn-gradient-primary w-100 py-3 fs-6">
                <i class="fas fa-user-check me-2"></i> Complete Registration
              </button>
            </div>
          </div>
        </form>

        <div class="text-center mt-4 pt-3 border-top small text-muted">
          Already have an account? <a href="<?php echo base_url('login.php'); ?>" class="fw-bold">Sign In here</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

