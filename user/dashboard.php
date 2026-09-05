<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_student();

$uid = $_SESSION['user_id'];

$myMaterials = $conn->query("SELECT COUNT(*) c FROM materials WHERE uploaded_by=$uid")->fetch_assoc()['c'];
$myQuestions = $conn->query("SELECT COUNT(*) c FROM questions WHERE posted_by=$uid")->fetch_assoc()['c'];
$myGroups    = $conn->query("SELECT COUNT(*) c FROM group_members WHERE user_id=$uid")->fetch_assoc()['c'];
$myAnswers   = $conn->query("SELECT COUNT(*) c FROM answers WHERE answered_by=$uid")->fetch_assoc()['c'];

$recentMaterials = $conn->query("SELECT m.title, s.subject_name, m.status, m.created_at FROM materials m JOIN subjects s ON m.subject_id=s.subject_id WHERE m.uploaded_by=$uid ORDER BY m.created_at DESC LIMIT 5");

$base = '../';
$page_title = 'My Dashboard';
include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
  <div>
    <h3 class="fw-bold mb-1"><i class="fa-solid fa-house-user text-success me-2"></i>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></h3>
    <p class="text-muted small mb-0">NSBM Student Portal &bull; Peer Learning Dashboard</p>
  </div>
  <a href="upload_material.php" class="btn btn-sm-primary rounded-pill"><i class="fa-solid fa-cloud-arrow-up me-2"></i>Upload Resource</a>
</div>

<div class="row g-4 mb-4">
  <div class="col-md-3 col-6">
    <div class="dashboard-stat stat-1">
      <i class="fa-solid fa-file-arrow-up stat-icon"></i>
      <div class="text-white-50 small font-monospace uppercase fw-bold">My Uploads</div>
      <div class="fs-1 fw-extrabold mt-1"><?php echo $myMaterials; ?></div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="dashboard-stat stat-2">
      <i class="fa-solid fa-circle-question stat-icon"></i>
      <div class="text-white-50 small font-monospace uppercase fw-bold">Questions Asked</div>
      <div class="fs-1 fw-extrabold mt-1"><?php echo $myQuestions; ?></div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="dashboard-stat stat-3">
      <i class="fa-solid fa-comment-dots stat-icon"></i>
      <div class="text-white-50 small font-monospace uppercase fw-bold">Answers Provided</div>
      <div class="fs-1 fw-extrabold mt-1"><?php echo $myAnswers; ?></div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="dashboard-stat stat-4">
      <i class="fa-solid fa-people-group stat-icon"></i>
      <div class="text-white-50 small font-monospace uppercase fw-bold">Groups Joined</div>
      <div class="fs-1 fw-extrabold mt-1"><?php echo $myGroups; ?></div>
    </div>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-8">
    <div class="card card-sm p-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="fw-bold mb-0"><i class="fa-solid fa-clock-rotate-left text-success me-2"></i>My Upload Activity</h5>
        <a href="materials.php" class="btn btn-sm btn-outline-secondary rounded-pill">Browse All Materials</a>
      </div>
      <div class="table-responsive">
        <table class="table table-custom">
          <thead>
            <tr>
              <th>Material Title</th>
              <th>Subject Module</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
          <?php $c=0; while ($m = $recentMaterials->fetch_assoc()): $c++; ?>
            <tr>
              <td class="fw-semibold text-dark"><?php echo htmlspecialchars($m['title']); ?></td>
              <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($m['subject_name']); ?></span></td>
              <td><span class="badge badge-<?php echo $m['status']; ?>"><?php echo ucfirst($m['status']); ?></span></td>
            </tr>
          <?php endwhile; ?>
          <?php if ($c===0): ?>
            <tr>
              <td colspan="3">
                <div class="empty-state py-4">
                  <i class="fa-solid fa-folder-open"></i>
                  <p class="text-muted small mb-0">No uploaded materials yet. Share your lecture notes or past paper solutions!</p>
                </div>
              </td>
            </tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card card-sm p-4">
      <h5 class="fw-bold mb-3"><i class="fa-solid fa-bolt text-warning me-2"></i>Quick Actions</h5>
      <div class="d-grid gap-2">
        <a href="upload_material.php" class="btn btn-sm-primary text-start d-flex align-items-center justify-content-between p-3 rounded-3">
          <span><i class="fa-solid fa-upload me-2"></i> Upload Lecture Notes / Papers</span>
          <i class="fa-solid fa-chevron-right small"></i>
        </a>
        <a href="ask_question.php" class="btn btn-outline-secondary text-start d-flex align-items-center justify-content-between p-3 rounded-3">
          <span><i class="fa-solid fa-circle-question me-2 text-primary"></i> Post Module Question</span>
          <i class="fa-solid fa-chevron-right small"></i>
        </a>
        <a href="create_group.php" class="btn btn-outline-secondary text-start d-flex align-items-center justify-content-between p-3 rounded-3">
          <span><i class="fa-solid fa-people-group me-2 text-success"></i> Create Study Group</span>
          <i class="fa-solid fa-chevron-right small"></i>
        </a>
        <a href="materials.php" class="btn btn-outline-secondary text-start d-flex align-items-center justify-content-between p-3 rounded-3">
          <span><i class="fa-solid fa-book-open me-2 text-warning"></i> Browse NSBM Materials</span>
          <i class="fa-solid fa-chevron-right small"></i>
        </a>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
