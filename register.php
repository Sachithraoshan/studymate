<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

if (is_logged_in()) {
    header("Location: " . (is_admin() ? "admin/dashboard.php" : "user/dashboard.php"));
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = clean($conn, $_POST['full_name'] ?? '');
    $email     = clean($conn, $_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';

    if ($full_name === '' || $email === '' || $password === '') {
        $errors[] = "All fields are required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $errors[] = "An account with this email address already exists.";
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, 'student')");
        $stmt->bind_param("sss", $full_name, $email, $hashed);
        if ($stmt->execute()) {
            $_SESSION['flash_success'] = "Registration successful! You can now log in with your account credentials.";
            header("Location: login.php");
            exit;
        } else {
            $errors[] = "Something went wrong during registration. Please try again.";
        }
        $stmt->close();
    }
}

$base = '';
$page_title = 'Student Registration';
include __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center py-4">
  <div class="col-md-6 col-lg-5">
    <div class="card card-lms p-4 p-md-5">
      <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-4 p-3 mb-3" style="width: 60px; height: 60px;">
          <i class="fa-solid fa-user-plus fa-2xl"></i>
        </div>
        <h3 class="fw-bold font-heading mb-1">Student Registration</h3>
        <p class="text-muted small mb-0">Create your StudyMate NSBM Student Account</p>
      </div>

      <?php foreach ($errors as $err): ?>
        <div class="alert alert-danger rounded-3 py-2 small mb-3"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err); ?></div>
      <?php endforeach; ?>

      <form method="POST" id="registerForm" novalidate>
        <div class="mb-3">
          <label class="form-label small fw-bold">Full Name</label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-user"></i></span>
            <input type="text" name="full_name" class="form-control border-start-0" placeholder="e.g. Kasun Perera" required value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label small fw-bold">Email Address</label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-envelope"></i></span>
            <input type="email" name="email" class="form-control border-start-0" placeholder="e.g. kasun@nsbm.ac.lk" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label small fw-bold">Password</label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-lock"></i></span>
            <input type="password" name="password" class="form-control border-start-0" placeholder="Minimum 6 characters" required minlength="6">
          </div>
        </div>

        <div class="mb-4">
          <label class="form-label small fw-bold">Confirm Password</label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-shield"></i></span>
            <input type="password" name="confirm_password" class="form-control border-start-0" placeholder="Re-enter password" required minlength="6">
          </div>
        </div>

        <button type="submit" class="btn btn-lms-primary w-100 py-2.5 fw-bold mb-3"><i class="fa-solid fa-user-check me-2"></i>Complete Registration</button>
      </form>

      <div class="border-top pt-3 text-center">
        <p class="small text-muted mb-0">Already registered? <a href="login.php" class="text-success fw-bold text-decoration-none">Sign In Here</a></p>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
