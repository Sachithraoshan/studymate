<?php
require_once __DIR__ . '/includes/auth.php';
$base = '';
$page_title = 'Academic Portal';
include __DIR__ . '/includes/header.php';
?>

<div class="lms-hero-card mb-5">
  <div class="row align-items-center">
    <div class="col-lg-8">
      <span class="lms-badge-pill mb-3">
        <i class="fa-solid fa-seedling me-1"></i> NSBM Green University Student Hub &bull; Batch 26.1
      </span>
      <h1 class="fw-extrabold display-5 mb-3 font-heading">Empowering Peer Learning &amp; Academic Success.</h1>
      <p class="lead text-white-50 mb-4" style="max-width: 600px;">
        Access verified computing past exam papers, lecture slides, NSBM library e-journals, and collaborate with your classmates across all first-year modules.
      </p>
      <div class="d-flex flex-wrap gap-3">
        <?php if (!is_logged_in()): ?>
          <a href="register.php" class="btn btn-light btn-lg rounded-pill fw-bold text-success px-4"><i class="fa-solid fa-user-plus me-2"></i>Create Student Account</a>
          <a href="login.php" class="btn btn-outline-light btn-lg rounded-pill px-4"><i class="fa-solid fa-right-to-bracket me-2"></i>Student Login</a>
        <?php elseif (is_admin()): ?>
          <a href="admin/dashboard.php" class="btn btn-light btn-lg rounded-pill fw-bold text-success px-4"><i class="fa-solid fa-chart-pie me-2"></i>Admin Dashboard</a>
        <?php else: ?>
          <a href="user/dashboard.php" class="btn btn-light btn-lg rounded-pill fw-bold text-success px-4"><i class="fa-solid fa-grip me-2"></i>Open Student Dashboard</a>
          <a href="user/materials.php" class="btn btn-outline-light btn-lg rounded-pill px-4"><i class="fa-solid fa-folder-open me-2"></i>Browse Learning Resources</a>
        <?php endif; ?>
      </div>
    </div>
    <div class="col-lg-4 text-center d-none d-lg-block">
      <div class="p-4" style="background: rgba(255,255,255,0.08); border-radius: 20px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.15);">
        <i class="fa-solid fa-graduation-cap text-warning mb-3" style="font-size: 72px;"></i>
        <h4 class="fw-bold mb-1 font-heading text-white">Faculty of Computing</h4>
        <p class="small text-white-50 mb-0">Built by undergraduates to foster peer collaboration and resource sharing.</p>
      </div>
    </div>
  </div>
</div>

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h3 class="fw-bold mb-1 font-heading">First-Year Computing Modules</h3>
    <p class="text-muted small mb-0">Explore course materials and discussion groups by subject</p>
  </div>
  <a href="user/materials.php" class="btn btn-sm btn-lms-outline rounded-pill">View All Resources <i class="fa-solid fa-arrow-right ms-1"></i></a>
</div>

<div class="row g-4 mb-5">
  <div class="col-md-6 col-lg-3">
    <div class="card card-lms p-4 h-100">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <span class="badge badge-module">SE101</span>
        <i class="fa-solid fa-code text-primary fs-4"></i>
      </div>
      <h5 class="fw-bold font-heading mb-2">Programming Fundamentals</h5>
      <p class="text-muted small mb-3">C++ algorithms, logic building, array data structures, and lab practical guides.</p>
      <div class="mt-auto pt-2 border-top">
        <small class="text-muted"><i class="fa-solid fa-book-open me-1 text-success"></i> 12 Resources</small>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-lg-3">
    <div class="card card-lms p-4 h-100">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <span class="badge badge-module">SE102</span>
        <i class="fa-solid fa-calculator text-warning fs-4"></i>
      </div>
      <h5 class="fw-bold font-heading mb-2">Mathematics for Computing</h5>
      <p class="text-muted small mb-3">Discrete math, set theory, matrix algebra, graph theory, and tutorial answer sheets.</p>
      <div class="mt-auto pt-2 border-top">
        <small class="text-muted"><i class="fa-solid fa-book-open me-1 text-success"></i> 8 Resources</small>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-lg-3">
    <div class="card card-lms p-4 h-100">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <span class="badge badge-module">SE103</span>
        <i class="fa-brands fa-java text-danger fs-4"></i>
      </div>
      <h5 class="fw-bold font-heading mb-2">Object-Oriented Programming</h5>
      <p class="text-muted small mb-3">Java classes, inheritance, polymorphism, encapsulation, and assignment walkthroughs.</p>
      <div class="mt-auto pt-2 border-top">
        <small class="text-muted"><i class="fa-solid fa-book-open me-1 text-success"></i> 15 Resources</small>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-lg-3">
    <div class="card card-lms p-4 h-100">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <span class="badge badge-module">CS104</span>
        <i class="fa-solid fa-network-wired text-info fs-4"></i>
      </div>
      <h5 class="fw-bold font-heading mb-2">Computer Networks</h5>
      <p class="text-muted small mb-3">OSI layers, TCP/IP protocol suite, Cisco packet tracer labs, and exam revision guides.</p>
      <div class="mt-auto pt-2 border-top">
        <small class="text-muted"><i class="fa-solid fa-book-open me-1 text-success"></i> 10 Resources</small>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
