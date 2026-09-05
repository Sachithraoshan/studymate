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
$page_title = 'Student Dashboard';
include __DIR__ . '/../includes/header.php';
?>

<div class="lms-hero-card mb-4 p-4 p-md-5">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
      <span class="lms-badge-pill mb-2">
        <i class="fa-solid fa-graduation-cap"></i> Faculty of Computing &bull; Year 1 Semester 2
      </span>
      <h2 class="fw-bold mb-1 text-white font-heading">Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?> 👋</h2>
      <p class="text-white-50 mb-0">Track your module resources, ask peer questions, and manage study circles.</p>
    </div>
    <div class="d-flex gap-2">
      <a href="upload_material.php" class="btn btn-light rounded-pill fw-bold text-success px-3"><i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload File</a>
      <a href="ask_question.php" class="btn btn-outline-light rounded-pill px-3"><i class="fa-solid fa-circle-question me-1"></i> Ask Question</a>
    </div>
  </div>
</div>

<div class="row g-4 mb-4">
  <div class="col-md-3 col-6">
    <div class="lms-stat-card stat-emerald">
      <i class="fa-solid fa-file-arrow-up stat-icon"></i>
      <div class="text-white-50 small fw-bold font-heading">MY UPLOADS</div>
      <div class="fs-1 fw-extrabold font-heading mt-1"><?php echo $myMaterials; ?></div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="lms-stat-card stat-sky">
      <i class="fa-solid fa-circle-question stat-icon"></i>
      <div class="text-white-50 small fw-bold font-heading">QUESTIONS ASKED</div>
      <div class="fs-1 fw-extrabold font-heading mt-1"><?php echo $myQuestions; ?></div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="lms-stat-card stat-amber">
      <i class="fa-solid fa-comment-dots stat-icon"></i>
      <div class="text-white-50 small fw-bold font-heading">ANSWERS GIVEN</div>
      <div class="fs-1 fw-extrabold font-heading mt-1"><?php echo $myAnswers; ?></div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="lms-stat-card stat-violet">
      <i class="fa-solid fa-people-group stat-icon"></i>
      <div class="text-white-50 small fw-bold font-heading">GROUPS JOINED</div>
      <div class="fs-1 fw-extrabold font-heading mt-1"><?php echo $myGroups; ?></div>
    </div>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-8">
    <div class="card card-lms p-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="fw-bold font-heading mb-0"><i class="fa-solid fa-clock-rotate-left text-success me-2"></i>My Submissions &amp; Status</h5>
        <a href="materials.php" class="btn btn-sm btn-lms-outline rounded-pill">Browse All Materials</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr class="text-muted fs-8 font-heading uppercase">
              <th>Resource Title</th>
              <th>Module Subject</th>
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
                <div class="text-center py-4 text-muted">
                  <i class="fa-solid fa-folder-open mb-2" style="font-size: 36px; opacity: 0.4;"></i>
                  <p class="small mb-0">No resource submissions yet. Contribute your notes or past paper solutions!</p>
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
    <div class="card card-lms p-4">
      <h5 class="fw-bold font-heading mb-3"><i class="fa-solid fa-bolt text-warning me-2"></i>LMS Shortcuts</h5>
      <div class="d-grid gap-2">
        <a href="materials.php" class="btn btn-lms-outline text-start d-flex align-items-center justify-content-between p-3">
          <span><i class="fa-solid fa-book-open text-success me-2"></i> Browse Course Notes &amp; Papers</span>
          <i class="fa-solid fa-chevron-right small text-muted"></i>
        </a>
        <a href="upload_material.php" class="btn btn-lms-outline text-start d-flex align-items-center justify-content-between p-3">
          <span><i class="fa-solid fa-cloud-arrow-up text-primary me-2"></i> Upload Lecture Document</span>
          <i class="fa-solid fa-chevron-right small text-muted"></i>
        </a>
        <a href="questions.php" class="btn btn-lms-outline text-start d-flex align-items-center justify-content-between p-3">
          <span><i class="fa-solid fa-comments text-warning me-2"></i> Join Q&amp;A Forum</span>
          <i class="fa-solid fa-chevron-right small text-muted"></i>
        </a>
        <a href="study_groups.php" class="btn btn-lms-outline text-start d-flex align-items-center justify-content-between p-3">
          <span><i class="fa-solid fa-people-group text-info me-2"></i> Study Circle Groups</span>
          <i class="fa-solid fa-chevron-right small text-muted"></i>
        </a>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
