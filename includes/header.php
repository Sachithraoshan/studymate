<?php require_once __DIR__ . '/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($page_title) ? $page_title . ' - StudyMate LMS' : 'StudyMate — NSBM Academic Portal'; ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="<?php echo isset($base) ? $base : ''; ?>assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="lms-wrapper">
  <!-- LMS Left Sidebar Shell -->
  <aside class="lms-sidebar" id="lmsSidebar">
    <div class="lms-sidebar-brand">
      <span class="lms-brand-logo"><i class="fa-solid fa-graduation-cap"></i></span>
      <div>
        <h5 class="fw-bold mb-0 text-white font-heading">StudyMate LMS</h5>
        <small class="text-white-50 fs-7">NSBM Faculty of Computing</small>
      </div>
    </div>

    <div class="lms-sidebar-nav">
      <div class="lms-nav-label">Academic Portal</div>
      <?php if (is_admin()): ?>
        <a href="<?php echo $base; ?>admin/dashboard.php" class="lms-nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'admin/dashboard.php') !== false) ? 'active' : ''; ?>">
          <i class="fa-solid fa-chart-pie"></i><span>Admin Console</span>
        </a>
        <a href="<?php echo $base; ?>admin/approve_materials.php" class="lms-nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'approve_materials.php') !== false) ? 'active' : ''; ?>">
          <i class="fa-solid fa-file-circle-check"></i><span>Material Approvals</span>
        </a>
        <a href="<?php echo $base; ?>admin/manage_subjects.php" class="lms-nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'manage_subjects.php') !== false) ? 'active' : ''; ?>">
          <i class="fa-solid fa-book"></i><span>Computing Modules</span>
        </a>
        <a href="<?php echo $base; ?>admin/manage_users.php" class="lms-nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'manage_users.php') !== false) ? 'active' : ''; ?>">
          <i class="fa-solid fa-users"></i><span>Student Directory</span>
        </a>
        <a href="<?php echo $base; ?>admin/moderate_discussions.php" class="lms-nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'moderate_discussions.php') !== false) ? 'active' : ''; ?>">
          <i class="fa-solid fa-comments"></i><span>Forum Moderation</span>
        </a>
      <?php elseif (is_logged_in()): ?>
        <a href="<?php echo $base; ?>user/dashboard.php" class="lms-nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'user/dashboard.php') !== false) ? 'active' : ''; ?>">
          <i class="fa-solid fa-grip"></i><span>Student Dashboard</span>
        </a>
        <a href="<?php echo $base; ?>user/materials.php" class="lms-nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'materials.php') !== false) ? 'active' : ''; ?>">
          <i class="fa-solid fa-folder-open"></i><span>Learning Resources</span>
        </a>
        <a href="<?php echo $base; ?>user/questions.php" class="lms-nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'questions.php') !== false) ? 'active' : ''; ?>">
          <i class="fa-solid fa-comments"></i><span>Q&amp;A Forum</span>
        </a>
        <a href="<?php echo $base; ?>user/study_groups.php" class="lms-nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'study_groups.php') !== false) ? 'active' : ''; ?>">
          <i class="fa-solid fa-people-group"></i><span>Study Circles</span>
        </a>
      <?php else: ?>
        <a href="<?php echo $base; ?>index.php" class="lms-nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'index.php') !== false) ? 'active' : ''; ?>">
          <i class="fa-solid fa-house"></i><span>Home Portal</span>
        </a>
        <a href="<?php echo $base; ?>login.php" class="lms-nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'login.php') !== false) ? 'active' : ''; ?>">
          <i class="fa-solid fa-right-to-bracket"></i><span>Student Login</span>
        </a>
        <a href="<?php echo $base; ?>register.php" class="lms-nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'register.php') !== false) ? 'active' : ''; ?>">
          <i class="fa-solid fa-user-plus"></i><span>Register Account</span>
        </a>
      <?php endif; ?>

      <div class="lms-nav-label mt-3">Quick References</div>
      <a href="https://library.nsbm.ac.lk/pages/e-journals.php" target="_blank" class="lms-nav-link">
        <i class="fa-solid fa-book-bookmark text-warning"></i><span>NSBM E-Journals</span>
      </a>
      <a href="https://github.com/Lithara/First-Year-Past-Papers---NSBM" target="_blank" class="lms-nav-link">
        <i class="fa-brands fa-github text-info"></i><span>GitHub Past Papers</span>
      </a>
    </div>

    <?php if (is_logged_in()): ?>
      <div class="lms-sidebar-footer">
        <div class="d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
              <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
            </div>
            <div style="max-width: 130px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
              <div class="fw-bold text-white fs-7 mb-0"><?php echo htmlspecialchars($_SESSION['full_name']); ?></div>
              <small class="text-white-50 fs-8 uppercase"><?php echo htmlspecialchars($_SESSION['role']); ?></small>
            </div>
          </div>
          <a href="<?php echo $base; ?>logout.php" class="btn btn-sm btn-outline-light rounded-circle" style="width: 32px; height: 32px; padding: 0;" title="Logout">
            <i class="fa-solid fa-right-from-bracket fs-7"></i>
          </a>
        </div>
      </div>
    <?php endif; ?>
  </aside>

  <!-- LMS Main Content Wrapper -->
  <div class="lms-content-wrapper">
    <!-- Top Utility Bar -->
    <header class="lms-topbar">
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-sm btn-light d-lg-none" type="button" id="sidebarToggle">
          <i class="fa-solid fa-bars"></i>
        </button>
        <span class="lms-badge-pill bg-success text-white border-0 d-none d-sm-inline-flex">
          <i class="fa-solid fa-seedling"></i> NSBM Green University
        </span>
      </div>

      <div class="d-flex align-items-center gap-3">
        <div class="d-none d-md-block text-end">
          <div class="small fw-bold text-dark mb-0"><?php echo date('l, d F Y'); ?></div>
          <small class="text-muted fs-8">Academic Year 2026 &bull; Batch 26.1</small>
        </div>

        <?php if (!is_logged_in()): ?>
          <a href="<?php echo $base; ?>login.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Login</a>
          <a href="<?php echo $base; ?>register.php" class="btn btn-sm btn-lms-primary rounded-pill px-3">Join Portal</a>
        <?php else: ?>
          <div class="dropdown">
            <button class="btn btn-sm btn-light rounded-pill px-3 dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
              <i class="fa-solid fa-circle-user text-success"></i>
              <span><?php echo htmlspecialchars(explode(' ', $_SESSION['full_name'])[0]); ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-4 border-0">
              <li><a class="dropdown-item" href="<?php echo $base; ?>user/dashboard.php"><i class="fa-solid fa-gauge me-2 text-success"></i> Dashboard</a></li>
              <li><a class="dropdown-item" href="<?php echo $base; ?>user/materials.php"><i class="fa-solid fa-book-open me-2 text-primary"></i> Learning Resources</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="<?php echo $base; ?>logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
            </ul>
          </div>
        <?php endif; ?>
      </div>
    </header>

    <!-- Main Workspace Container -->
    <main class="lms-main">
      <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
          <i class="fa-solid fa-circle-check fa-lg me-3 text-success"></i>
          <div><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
          <i class="fa-solid fa-circle-exclamation fa-lg me-3 text-danger"></i>
          <div><?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>
