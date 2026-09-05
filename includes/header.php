<?php require_once __DIR__ . '/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($page_title) ? $page_title . ' - StudyMate NSBM' : 'StudyMate - NSBM Peer Learning Platform'; ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="<?php echo isset($base) ? $base : ''; ?>assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark sm-navbar sticky-top">
  <div class="container">
    <a class="navbar-brand" href="<?php echo isset($base) ? $base : ''; ?>index.php">
      <span class="brand-icon-wrapper"><i class="fa-solid fa-graduation-cap"></i></span>
      <span>StudyMate</span>
    </a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
      <?php if (is_admin()): ?>
        <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>admin/dashboard.php"><i class="fa-solid fa-chart-line me-1"></i> Admin Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>admin/manage_users.php"><i class="fa-solid fa-users me-1"></i> Users</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>admin/manage_subjects.php"><i class="fa-solid fa-book me-1"></i> Subjects</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>admin/approve_materials.php"><i class="fa-solid fa-file-circle-check me-1"></i> Approvals</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>admin/moderate_discussions.php"><i class="fa-solid fa-comments me-1"></i> Discussions</a></li>
        <li class="nav-item ms-lg-2"><a class="btn btn-sm btn-outline-light rounded-pill px-3" href="<?php echo $base; ?>logout.php"><i class="fa-solid fa-right-from-bracket me-1"></i> Logout</a></li>
      <?php elseif (is_logged_in()): ?>
        <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>user/dashboard.php"><i class="fa-solid fa-gauge-high me-1"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>user/materials.php"><i class="fa-solid fa-folder-open me-1"></i> Materials</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>user/questions.php"><i class="fa-solid fa-comments me-1"></i> Q&amp;A Forum</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>user/study_groups.php"><i class="fa-solid fa-people-group me-1"></i> Study Groups</a></li>
        <li class="nav-item ms-lg-2 me-lg-2">
          <span class="user-pill d-inline-flex align-items-center gap-2">
            <i class="fa-solid fa-circle-user text-warning"></i>
            <span><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
          </span>
        </li>
        <li class="nav-item"><a class="btn btn-sm btn-outline-light rounded-pill px-3 mt-2 mt-lg-0" href="<?php echo $base; ?>logout.php"><i class="fa-solid fa-right-from-bracket me-1"></i> Logout</a></li>
      <?php else: ?>
        <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>login.php"><i class="fa-solid fa-right-to-bracket me-1"></i> Login</a></li>
        <li class="nav-item ms-lg-2"><a class="btn btn-light rounded-pill text-success fw-bold px-4" href="<?php echo $base; ?>register.php"><i class="fa-solid fa-user-plus me-1"></i> Register</a></li>
      <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<main class="py-4 py-md-5">
<div class="container">
<?php if (isset($_SESSION['flash_success'])): ?>
  <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
    <i class="fa-solid fa-circle-check fa-lg me-3"></i>
    <div><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>
<?php if (isset($_SESSION['flash_error'])): ?>
  <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
    <i class="fa-solid fa-triangle-exclamation fa-lg me-3"></i>
    <div><?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>
