<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_student();

$uid = $_SESSION['user_id'];

// Track download
if (isset($_GET['download'])) {
    $id = (int)$_GET['download'];
    $conn->query("UPDATE materials SET download_count = download_count + 1 WHERE material_id=$id AND status='approved'");
    $res = $conn->query("SELECT file_path FROM materials WHERE material_id=$id");
    if ($row = $res->fetch_assoc()) {
        header("Location: ../uploads/materials/" . $row['file_path']);
        exit;
    }
}

// Post comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_material_id'])) {
    $mid = (int)$_POST['comment_material_id'];
    $comment = clean($conn, $_POST['comment'] ?? '');
    if ($comment !== '') {
        $stmt = $conn->prepare("INSERT INTO material_comments (material_id, user_id, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $mid, $uid, $comment);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: materials.php?view=$mid#comments");
    exit;
}

// Post/update rating
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rate_material_id'])) {
    $mid = (int)$_POST['rate_material_id'];
    $rating = (int)$_POST['rating'];
    if ($rating >= 1 && $rating <= 5) {
        $stmt = $conn->prepare("INSERT INTO material_ratings (material_id, user_id, rating) VALUES (?, ?, ?)
                                 ON DUPLICATE KEY UPDATE rating = VALUES(rating)");
        $stmt->bind_param("iii", $mid, $uid, $rating);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: materials.php?view=$mid#rate");
    exit;
}

$search = isset($_GET['q']) ? clean($conn, $_GET['q']) : '';
$subjectFilter = isset($_GET['subject']) ? (int)$_GET['subject'] : 0;

$sql = "SELECT m.*, u.full_name, s.subject_name,
        (SELECT ROUND(AVG(rating),1) FROM material_ratings r WHERE r.material_id = m.material_id) AS avg_rating,
        (SELECT COUNT(*) FROM material_ratings r WHERE r.material_id = m.material_id) AS rating_count
        FROM materials m
        JOIN users u ON m.uploaded_by = u.user_id
        JOIN subjects s ON m.subject_id = s.subject_id
        WHERE m.status='approved'";
if ($search !== '') $sql .= " AND (m.title LIKE '%$search%' OR m.description LIKE '%$search%')";
if ($subjectFilter > 0) $sql .= " AND m.subject_id = $subjectFilter";
$sql .= " ORDER BY m.created_at DESC";
$materials = $conn->query($sql);

$subjects = $conn->query("SELECT * FROM subjects ORDER BY subject_name");

$viewId = isset($_GET['view']) ? (int)$_GET['view'] : 0;
$viewMaterial = null;
if ($viewId) {
    $r = $conn->query("SELECT m.*, u.full_name, s.subject_name FROM materials m JOIN users u ON m.uploaded_by=u.user_id JOIN subjects s ON m.subject_id=s.subject_id WHERE m.material_id=$viewId AND m.status='approved'");
    $viewMaterial = $r->fetch_assoc();
}

$base = '../';
$page_title = 'Learning Resources';
include __DIR__ . '/../includes/header.php';
?>

<?php if ($viewMaterial): ?>
  <div class="mb-3">
    <a href="materials.php" class="btn btn-sm btn-lms-outline rounded-pill"><i class="fa-solid fa-arrow-left me-1"></i> Back to Resource Hub</a>
  </div>

  <div class="card card-lms p-4 p-md-5 mb-4">
    <div class="d-flex align-items-center gap-2 mb-2">
      <span class="badge badge-module">MODULE RESOURCE</span>
      <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 fw-bold">
        <?php echo htmlspecialchars($viewMaterial['subject_name']); ?>
      </span>
    </div>

    <h2 class="fw-bold font-heading mb-2"><?php echo htmlspecialchars($viewMaterial['title']); ?></h2>
    <p class="text-muted small mb-3">Uploaded by <strong><?php echo htmlspecialchars($viewMaterial['full_name']); ?></strong> &bull; Verified Academic File</p>

    <div class="p-4 bg-light rounded-4 mb-4">
      <p class="mb-0 text-dark"><?php echo nl2br(htmlspecialchars($viewMaterial['description'])); ?></p>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-4">
      <a href="?download=<?php echo $viewMaterial['material_id']; ?>" class="btn btn-lms-primary rounded-pill px-4"><i class="fa-solid fa-cloud-arrow-down me-2"></i> Download Resource File</a>
    </div>

    <hr class="my-4">

    <div id="rate" class="mb-4 p-3 bg-light rounded-3">
      <h6 class="fw-bold font-heading mb-2"><i class="fa-solid fa-star text-warning me-1"></i> Rate this learning resource</h6>
      <form method="POST" class="d-flex align-items-center gap-3">
        <input type="hidden" name="rate_material_id" value="<?php echo $viewMaterial['material_id']; ?>">
        <input type="hidden" id="ratingInput" name="rating" value="5">
        <div class="star-input d-flex gap-1" data-target="ratingInput">
          <?php for ($s=1;$s<=5;$s++): ?>
            <i class="fa-solid fa-star text-warning" data-value="<?php echo $s; ?>" style="cursor:pointer;font-size:24px;"></i>
          <?php endfor; ?>
        </div>
        <button class="btn btn-sm btn-lms-primary rounded-pill px-3">Submit Rating</button>
      </form>
    </div>

    <div id="comments">
      <h5 class="fw-bold font-heading mb-3"><i class="fa-solid fa-comments text-success me-2"></i> Discussion &amp; Peer Notes</h5>
      <?php
      $comments = $conn->query("SELECT c.*, u.full_name FROM material_comments c JOIN users u ON c.user_id=u.user_id WHERE c.material_id=" . $viewMaterial['material_id'] . " ORDER BY c.created_at DESC");
      if ($comments->num_rows === 0): ?>
        <p class="text-muted small italic">No comments yet. Be the first to share feedback or ask a question about this resource!</p>
      <?php endif; ?>

      <?php while ($c = $comments->fetch_assoc()): ?>
        <div class="p-3 bg-white border rounded-3 mb-2">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <strong class="text-dark small"><i class="fa-solid fa-circle-user text-success me-1"></i><?php echo htmlspecialchars($c['full_name']); ?></strong>
            <span class="text-muted fs-8"><?php echo date('d M Y, H:i', strtotime($c['created_at'])); ?></span>
          </div>
          <p class="mb-0 small text-secondary"><?php echo nl2br(htmlspecialchars($c['comment'])); ?></p>
        </div>
      <?php endwhile; ?>

      <form method="POST" class="mt-3">
        <input type="hidden" name="comment_material_id" value="<?php echo $viewMaterial['material_id']; ?>">
        <div class="mb-2">
          <textarea name="comment" class="form-control" rows="3" placeholder="Write a comment or solution note..." required></textarea>
        </div>
        <button class="btn btn-sm btn-lms-primary rounded-pill px-4"><i class="fa-solid fa-paper-plane me-1"></i> Post Comment</button>
      </form>
    </div>
  </div>

<?php else: ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <div>
    <h2 class="fw-bold font-heading mb-1"><i class="fa-solid fa-folder-open text-success me-2"></i> Course Learning Resources</h2>
    <p class="text-muted small mb-0">Browse NSBM library references, slide decks, past papers, and student notes</p>
  </div>
  <a href="upload_material.php" class="btn btn-lms-primary rounded-pill"><i class="fa-solid fa-cloud-arrow-up me-2"></i> Upload Resource</a>
</div>

<!-- Horizontal Subject Pill Filter Bar -->
<div class="lms-pill-filter mb-4">
  <a href="materials.php" class="pill-item <?php echo $subjectFilter==0 ? 'active' : ''; ?>">All Computing Modules</a>
  <?php $subjects->data_seek(0); while ($s = $subjects->fetch_assoc()): ?>
    <a href="materials.php?subject=<?php echo $s['subject_id']; ?>" class="pill-item <?php echo $subjectFilter==$s['subject_id'] ? 'active' : ''; ?>">
      <?php echo htmlspecialchars($s['subject_name']); ?>
    </a>
  <?php endwhile; ?>
</div>

<div class="card card-lms p-3 mb-4">
  <form class="row g-2" method="GET">
    <div class="col-md-9">
      <div class="input-group">
        <span class="input-group-text bg-white text-muted border-end-0"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" name="q" class="form-control border-start-0" placeholder="Search by paper title, keywords or topic..." value="<?php echo htmlspecialchars($search); ?>">
      </div>
    </div>
    <div class="col-md-3">
      <button class="btn btn-lms-primary w-100"><i class="fa-solid fa-filter me-1"></i> Filter Resources</button>
    </div>
  </form>
</div>

<div class="row g-4">
<?php $c = 0; while ($m = $materials->fetch_assoc()): $c++;
  // Assign source badge based on title keywords for realism
  $titleLower = strtolower($m['title']);
  $sourceBadge = '<span class="badge-source-journal"><i class="fa-solid fa-graduation-cap me-1"></i>NSBM Library</span>';
  if (strpos($titleLower, 'github') !== false || strpos($titleLower, 'repository') !== false) {
    $sourceBadge = '<span class="badge-source-github"><i class="fa-brands fa-github me-1"></i>GitHub</span>';
  } elseif (strpos($titleLower, 'studydrive') !== false || strpos($titleLower, 'notes') !== false) {
    $sourceBadge = '<span class="badge-source-studydrive"><i class="fa-solid fa-book-open me-1"></i>StudyDrive</span>';
  } elseif (strpos($titleLower, 'slideshare') !== false || strpos($titleLower, 'exam') !== false) {
    $sourceBadge = '<span class="badge-source-slideshare"><i class="fa-solid fa-file-powerpoint me-1"></i>SlideShare</span>';
  }
?>
  <div class="col-md-6 col-lg-4">
    <div class="card card-lms p-4 h-100 d-flex flex-column">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <?php echo $sourceBadge; ?>
        <small class="text-muted fs-8"><i class="fa-solid fa-download me-1"></i><?php echo $m['download_count']; ?></small>
      </div>

      <h5 class="fw-bold font-heading mb-2 text-dark" style="font-size: 1.05rem; min-height: 2.6rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
        <?php echo htmlspecialchars($m['title']); ?>
      </h5>

      <p class="text-muted small mb-3 flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
        <?php echo htmlspecialchars($m['description']); ?>
      </p>

      <div class="border-top pt-3 mt-auto">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <span class="small text-muted">
            <?php if ($m['avg_rating']): ?>
              <i class="fa-solid fa-star text-warning"></i> <strong><?php echo $m['avg_rating']; ?></strong> (<?php echo $m['rating_count']; ?>)
            <?php else: ?>
              <span class="text-muted fs-8">No ratings</span>
            <?php endif; ?>
          </span>
          <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($m['subject_name']); ?></span>
        </div>

        <div class="row g-2">
          <div class="col-6">
            <a href="?view=<?php echo $m['material_id']; ?>" class="btn btn-sm btn-lms-outline w-100 rounded-pill fw-semibold">View</a>
          </div>
          <div class="col-6">
            <a href="?download=<?php echo $m['material_id']; ?>" class="btn btn-sm btn-lms-primary w-100 rounded-pill fw-semibold"><i class="fa-solid fa-download me-1"></i> File</a>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endwhile; ?>

<?php if ($c === 0): ?>
  <div class="col-12">
    <div class="card card-lms p-5 text-center">
      <div class="py-4">
        <i class="fa-solid fa-folder-open text-muted mb-3" style="font-size: 48px; opacity: 0.4;"></i>
        <h5 class="fw-bold font-heading mb-1">No course resources found</h5>
        <p class="text-muted small mb-3">Try clearing search filters or choosing another computing module.</p>
        <a href="materials.php" class="btn btn-sm btn-lms-outline rounded-pill px-4">Reset Filters</a>
      </div>
    </div>
  </div>
<?php endif; ?>
</div>

<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
