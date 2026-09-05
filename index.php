<?php
require_once __DIR__ . '/includes/auth.php';
$base = '';
$page_title = 'Home';
include __DIR__ . '/includes/header.php';
?>

<div class="hero-card mb-5">
  <div class="row align-items-center">
    <div class="col-lg-7">
      <span class="hero-badge"><i class="fa-solid fa-seedling me-2"></i>NSBM Green University Student Hub</span>
      <h1 class="fw-extrabold display-5 mb-3">Learn together.<br>Grow together.</h1>
      <p class="lead text-white-50 mb-4 fs-5" style="max-width: 540px;">
        StudyMate is your peer learning and academic collaboration space. Access verified past papers, NSBM library e-journals, share lecture notes, and study with peers across your computing modules.
      </p>
      <div class="d-flex flex-wrap gap-3">
        <?php if (!is_logged_in()): ?>
          <a href="register.php" class="btn btn-light btn-lg rounded-pill fw-bold text-success px-4"><i class="fa-solid fa-user-plus me-2"></i>Join StudyMate</a>
          <a href="login.php" class="btn btn-outline-light btn-lg rounded-pill px-4"><i class="fa-solid fa-right-to-bracket me-2"></i>Student Login</a>
        <?php elseif (is_admin()): ?>
          <a href="admin/dashboard.php" class="btn btn-light btn-lg rounded-pill fw-bold text-success px-4"><i class="fa-solid fa-gauge-high me-2"></i>Admin Console</a>
        <?php else: ?>
          <a href="user/dashboard.php" class="btn btn-light btn-lg rounded-pill fw-bold text-success px-4"><i class="fa-solid fa-house-user me-2"></i>My Student Dashboard</a>
          <a href="user/materials.php" class="btn btn-outline-light btn-lg rounded-pill px-4"><i class="fa-solid fa-book-open me-2"></i>Browse Materials</a>
        <?php endif; ?>
      </div>
    </div>
    <div class="col-lg-5 text-center d-none d-lg-block">
      <div class="p-4" style="background: rgba(255,255,255,0.08); border-radius: 24px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.15);">
        <i class="fa-solid fa-graduation-cap text-warning mb-3" style="font-size: 80px;"></i>
        <h4 class="fw-bold mb-2">Academic Excellence</h4>
        <p class="small text-white-50 mb-0">Built by NSBM undergraduates to foster collaborative learning and seamless resource sharing.</p>
      </div>
    </div>
  </div>
</div>

<div class="text-center mb-4">
  <h2 class="fw-bold">Platform Capabilities</h2>
  <p class="text-muted">Everything you need to boost your academic performance at NSBM</p>
</div>

<div class="row g-4 mb-5">
  <div class="col-md-4">
    <div class="card card-sm p-4 h-100">
      <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-4 p-3 mb-3" style="width: 56px; height: 56px;">
        <i class="fa-solid fa-book-open-reader fa-xl"></i>
      </div>
      <h5 class="fw-bold mb-2">Verified Study Materials</h5>
      <p class="text-muted small mb-0">Access official past papers, slide decks, library e-journals, and peer notes filtered by subject.</p>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card card-sm p-4 h-100">
      <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-4 p-3 mb-3" style="width: 56px; height: 56px;">
        <i class="fa-solid fa-comments fa-xl"></i>
      </div>
      <h5 class="fw-bold mb-2">Peer Q&amp;A Discussions</h5>
      <p class="text-muted small mb-0">Ask challenging module questions, reply to classmates, and learn through collaborative problem solving.</p>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card card-sm p-4 h-100">
      <div class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning rounded-4 p-3 mb-3" style="width: 56px; height: 56px;">
        <i class="fa-solid fa-people-group fa-xl"></i>
      </div>
      <h5 class="fw-bold mb-2">Interactive Study Groups</h5>
      <p class="text-muted small mb-0">Form study circles for your modules, exchange ideas, and work on coursework targets together.</p>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
