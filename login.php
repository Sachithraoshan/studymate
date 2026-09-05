<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

if (is_logged_in()) {
    header("Location: " . (is_admin() ? "admin/dashboard.php" : "user/dashboard.php"));
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = clean($conn, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = "Please enter both email address and password.";
    } else {
        $stmt = $conn->prepare("SELECT user_id, full_name, password, role, status FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if ($user['status'] === 'blocked') {
                $errors[] = "Your student account has been blocked. Please contact the administrator.";
            } elseif (password_verify($password, $user['password'])) {
                $_SESSION['user_id']   = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role']      = $user['role'];

                header("Location: " . ($user['role'] === 'admin' ? "admin/dashboard.php" : "user/dashboard.php"));
                exit;
            } else {
                $errors[] = "Incorrect email address or password.";
            }
        } else {
            $errors[] = "Incorrect email address or password.";
        }
        $stmt->close();
    }
}

$base = '';
$page_title = 'Login Portal';
include __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center py-4">
  <div class="col-md-6 col-lg-5">
    <div class="card card-lms p-4 p-md-5">
      <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-4 p-3 mb-3" style="width: 60px; height: 60px;">
          <i class="fa-solid fa-graduation-cap fa-2xl"></i>
        </div>
        <h3 class="fw-bold font-heading mb-1">Student &amp; Staff Login</h3>
        <p class="text-muted small mb-0">NSBM Faculty of Computing LMS Portal</p>
      </div>

      <?php foreach ($errors as $err): ?>
        <div class="alert alert-danger rounded-3 py-2 small mb-3"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err); ?></div>
      <?php endforeach; ?>

      <form method="POST" id="loginForm" novalidate>
        <div class="mb-3">
          <label class="form-label small fw-bold">Email Address</label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-envelope"></i></span>
            <input type="email" name="email" class="form-control border-start-0" placeholder="e.g. student@studymate.com" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
          </div>
        </div>

        <div class="mb-4">
          <label class="form-label small fw-bold">Password</label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-lock"></i></span>
            <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" required>
          </div>
        </div>

        <button type="submit" class="btn btn-lms-primary w-100 py-2.5 fw-bold mb-3"><i class="fa-solid fa-right-to-bracket me-2"></i>Sign In to Portal</button>
      </form>

      <div class="border-top pt-3 text-center">
        <p class="small text-muted mb-1">Don't have an account? <a href="register.php" class="text-success fw-bold text-decoration-none">Create Student Account</a></p>
        <p class="text-muted small fs-8 mb-0"><i class="fa-solid fa-shield-halved me-1"></i> Admin login uses the same portal &mdash; auto-redirected by role.</p>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
