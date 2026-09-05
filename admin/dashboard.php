<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

$totalUsers     = $conn->query("SELECT COUNT(*) c FROM users WHERE role='student'")->fetch_assoc()['c'];
$pendingMats    = $conn->query("SELECT COUNT(*) c FROM materials WHERE status='pending'")->fetch_assoc()['c'];
$totalMaterials = $conn->query("SELECT COUNT(*) c FROM materials WHERE status='approved'")->fetch_assoc()['c'];
$totalGroups    = $conn->query("SELECT COUNT(*) c FROM study_groups")->fetch_assoc()['c'];
$totalQuestions = $conn->query("SELECT COUNT(*) c FROM questions")->fetch_assoc()['c'];

$recentUsers = $conn->query("SELECT full_name, email, created_at FROM users WHERE role='student' ORDER BY created_at DESC LIMIT 5");
$recentMats  = $conn->query("SELECT m.title, m.status, u.full_name FROM materials m JOIN users u ON m.uploaded_by = u.user_id ORDER BY m.created_at DESC LIMIT 5");

$base = '../';
$page_title = 'Admin Dashboard';
include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
  <div>
    <h3 class="fw-bold mb-1"><i class="fa-solid fa-chart-line text-success me-2"></i>Admin Console</h3>
    <p class="text-muted small mb-0">StudyMate NSBM Administration &amp; Moderation Overview</p>
  </div>
  <?php if ($pendingMats > 0): ?>
    <a href="approve_materials.php?status=pending" class="btn btn-sm btn-warning rounded-pill fw-bold"><i class="fa-solid fa-triangle-exclamation me-1"></i> <?php echo $pendingMats; ?> Materials Awaiting Review</a>
  <?php endif; ?>
</div>

<div class="row g-4 mb-4">
  <div class="col-md-3 col-6">
    <div class="dashboard-stat stat-1">
      <i class="fa-solid fa-users stat-icon"></i>
      <div class="text-white-50 small font-monospace uppercase fw-bold">Active Students</div>
      <div class="fs-1 fw-extrabold mt-1"><?php echo $totalUsers; ?></div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="dashboard-stat stat-2">
      <i class="fa-solid fa-hourglass-half stat-icon"></i>
      <div class="text-white-50 small font-monospace uppercase fw-bold">Pending Review</div>
      <div class="fs-1 fw-extrabold mt-1"><?php echo $pendingMats; ?></div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="dashboard-stat stat-3">
      <i class="fa-solid fa-circle-check stat-icon"></i>
      <div class="text-white-50 small font-monospace uppercase fw-bold">Approved Resources</div>
      <div class="fs-1 fw-extrabold mt-1"><?php echo $totalMaterials; ?></div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="dashboard-stat stat-4">
      <i class="fa-solid fa-people-group stat-icon"></i>
      <div class="text-white-50 small font-monospace uppercase fw-bold">Active Study Groups</div>
      <div class="fs-1 fw-extrabold mt-1"><?php echo $totalGroups; ?></div>
    </div>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-6">
    <div class="card card-sm p-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="fw-bold mb-0"><i class="fa-solid fa-user-plus text-primary me-2"></i>Recent Registrations</h5>
        <a href="manage_users.php" class="btn btn-sm btn-outline-secondary rounded-pill">View All Users</a>
      </div>
      <div class="table-responsive">
        <table class="table table-custom">
          <thead>
            <tr>
              <th>Student Name</th>
              <th>Email</th>
              <th>Joined Date</th>
            </tr>
          </thead>
          <tbody>
          <?php while ($u = $recentUsers->fetch_assoc()): ?>
            <tr>
              <td class="fw-semibold text-dark"><?php echo htmlspecialchars($u['full_name']); ?></td>
              <td class="text-muted small"><?php echo htmlspecialchars($u['email']); ?></td>
              <td class="text-muted small"><?php echo date('d M Y', strtotime($u['created_at'])); ?></td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card card-sm p-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="fw-bold mb-0"><i class="fa-solid fa-folder-open text-warning me-2"></i>Recent Upload Submissions</h5>
        <a href="approve_materials.php" class="btn btn-sm btn-outline-secondary rounded-pill">Moderate Files</a>
      </div>
      <div class="table-responsive">
        <table class="table table-custom">
          <thead>
            <tr>
              <th>Resource Title</th>
              <th>Uploader</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
          <?php while ($m = $recentMats->fetch_assoc()): ?>
            <tr>
              <td class="fw-semibold text-dark"><?php echo htmlspecialchars(mb_strimwidth($m['title'], 0, 30, '...')); ?></td>
              <td class="text-muted small"><?php echo htmlspecialchars($m['full_name']); ?></td>
              <td><span class="badge badge-<?php echo $m['status']; ?>"><?php echo ucfirst($m['status']); ?></span></td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
