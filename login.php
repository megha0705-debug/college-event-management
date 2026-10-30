<?php
/**
 * Login Page
 * CampusPulse — College Event Management Website
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$redirect = $_GET['redirect'] ?? '';

// If already logged in, redirect to respective dashboard
if (is_logged_in()) {
    if (is_admin()) {
        header("Location: " . base_url('admin/dashboard.php'));
    } else {
        header("Location: " . base_url('student/dashboard.php'));
    }
    exit;
}

$pdo = get_db_connection();
$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Verify CSRF Token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security token invalid or expired. Please submit the form again.';
    }

    $email    = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $redirect = $_POST['redirect'] ?? '';

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (empty($password)) {
        $errors[] = 'Please enter your account password.';
    }

    if (empty($errors)) {
        if (!$pdo) {
            $errors[] = 'Database is offline. Please make sure MySQL is running in XAMPP.';
        } else {
            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user && verify_user_password($password, $user['password'])) {
                    // Successful authentication
                    login_user($user);

                    flash_set('success', 'Welcome back, ' . e($user['name']) . '!');

                    // Redirect logic
                    if (!empty($redirect) && strpos($redirect, 'http') === false) {
                        header("Location: " . base_url(ltrim($redirect, '/')));
                    } elseif ($user['role'] === 'admin') {
                        header("Location: " . base_url('admin/dashboard.php'));
                    } else {
                        header("Location: " . base_url('student/dashboard.php'));
                    }
                    exit;
                } else {
                    $errors[] = 'Invalid email address or password. Please check your credentials.';
                }
            } catch (PDOException $e) {
                $errors[] = 'Authentication failed due to a database error.';
                error_log($e->getMessage());
            }
        }
    }
}

$page_title = 'Sign In';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-5 col-md-7">
      <div class="form-card">
        <div class="text-center mb-4">
          <div class="brand-icon mx-auto mb-3" style="width: 52px; height: 52px; font-size: 1.5rem;">
            <i class="fas fa-sign-in-alt"></i>
          </div>
          <h2 class="fw-bold mb-1">Welcome Back</h2>
          <p class="text-muted small">Sign in to manage registrations, access tickets, or manage campus events.</p>
        </div>

        <!-- Quick Demo Credentials Box for Evaluation -->
        <div class="p-3 bg-light rounded-3 border mb-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="small fw-bold text-dark"><i class="fas fa-bolt text-warning me-1"></i> Quick Demo Logins:</span>
            <small class="text-muted">Click to auto-fill</small>
          </div>
          <div class="row g-2">
            <div class="col-6">
              <button type="button" class="btn btn-sm btn-outline-purple w-100" onclick="fillDemoCredentials('meghana')">
                <i class="fas fa-user-graduate me-1"></i> Meghana (Student)
              </button>
            </div>
            <div class="col-6">
              <button type="button" class="btn btn-sm btn-outline-primary w-100" onclick="fillDemoCredentials('admin')">
                <i class="fas fa-shield-alt me-1"></i> Admin Portal
              </button>
            </div>
            <div class="col-6">
              <button type="button" class="btn btn-sm btn-outline-secondary w-100" onclick="fillDemoCredentials('priya')">
                <i class="fas fa-user me-1"></i> Priya Patel
              </button>
            </div>
            <div class="col-6">
              <button type="button" class="btn btn-sm btn-outline-secondary w-100" onclick="fillDemoCredentials('student')">
                <i class="fas fa-user me-1"></i> Aarav Sharma
              </button>
            </div>
          </div>
        </div>

        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger shadow-sm mb-4">
            <ul class="mb-0 ps-3 small">
              <?php foreach ($errors as $err): ?>
                <li><?php echo e($err); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form action="<?php echo base_url('login.php'); ?>" method="POST" class="needs-validation" novalidate>
          <?php echo csrf_field(); ?>
          <input type="hidden" name="redirect" value="<?php echo e($redirect); ?>">

          <div class="mb-3">
            <label class="form-label" for="login_email">Email Address</label>
            <div class="input-group">
              <span class="input-group-text bg-light"><i class="fas fa-envelope text-muted"></i></span>
              <input type="email" name="email" id="login_email" class="form-control" placeholder="admin@college.edu or student@college.edu" value="<?php echo e($email); ?>" required autofocus>
            </div>
          </div>

          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label class="form-label mb-0" for="login_password">Password</label>
              <a href="#" onclick="alert('For evaluation, you can use Admin@123 for Administrator or Student@123 for Students, or reset passwords directly in phpMyAdmin.'); return false;" class="small text-muted">Forgot password?</a>
            </div>
            <div class="input-group">
              <span class="input-group-text bg-light"><i class="fas fa-lock text-muted"></i></span>
              <input type="password" name="password" id="login_password" class="form-control" placeholder="••••••••" required>
            </div>
          </div>

          <div class="mb-4 form-check">
            <input type="checkbox" class="form-check-input" id="rememberMe">
            <label class="form-check-label small text-muted" for="rememberMe">Keep me signed in on this computer</label>
          </div>

          <button type="submit" class="btn btn-gradient-primary w-100 py-3 fs-6">
            <i class="fas fa-arrow-right-to-bracket me-2"></i> Sign In to Portal
          </button>
        </form>

        <div class="text-center mt-4 pt-3 border-top small text-muted">
          Don't have an account yet? <a href="<?php echo base_url('register.php'); ?>" class="fw-bold">Register as a Student</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

