<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_student();

$search = isset($_GET['q']) ? clean($conn, $_GET['q']) : '';
$subjectFilter = isset($_GET['subject']) ? (int)$_GET['subject'] : 0;

$sql = "SELECT q.*, u.full_name, s.subject_name,
        (SELECT COUNT(*) FROM answers a WHERE a.question_id=q.question_id AND a.status='visible') AS answer_count
        FROM questions q
        JOIN users u ON q.posted_by = u.user_id
        JOIN subjects s ON q.subject_id = s.subject_id
        WHERE q.status='open'";
if ($search !== '') $sql .= " AND (q.title LIKE '%$search%' OR q.details LIKE '%$search%')";
if ($subjectFilter > 0) $sql .= " AND q.subject_id = $subjectFilter";
$sql .= " ORDER BY q.created_at DESC";
$questions = $conn->query($sql);

$subjects = $conn->query("SELECT * FROM subjects ORDER BY subject_name");

$base = '../';
$page_title = 'Academic Q&A Forum';
include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <div>
    <h3 class="fw-bold mb-1"><i class="fa-solid fa-comments text-success me-2"></i> Peer Q&amp;A Forum</h3>
    <p class="text-muted small mb-0">Ask questions, share solutions, and discuss NSBM module coursework</p>
  </div>
  <a href="ask_question.php" class="btn btn-sm-primary rounded-pill"><i class="fa-solid fa-plus me-1"></i> Post Question</a>
</div>

<div class="card card-sm p-3 mb-4">
  <form class="row g-2" method="GET">
    <div class="col-md-6">
      <div class="input-group">
        <span class="input-group-text bg-white text-muted border-end-0"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" name="q" class="form-control border-start-0" placeholder="Search questions or keywords..." value="<?php echo htmlspecialchars($search); ?>">
      </div>
    </div>
    <div class="col-md-4">
      <select name="subject" class="form-select">
        <option value="0">All Module Subjects</option>
        <?php while ($s = $subjects->fetch_assoc()): ?>
          <option value="<?php echo $s['subject_id']; ?>" <?php echo $subjectFilter==$s['subject_id']?'selected':''; ?>><?php echo htmlspecialchars($s['subject_name']); ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-2">
      <button class="btn btn-sm-primary w-100"><i class="fa-solid fa-filter me-1"></i> Filter</button>
    </div>
  </form>
</div>

<div class="d-flex flex-column gap-3">
<?php $c=0; while ($q = $questions->fetch_assoc()): $c++; ?>
  <div class="card card-sm p-4">
    <div class="d-flex align-items-start justify-content-between gap-3">
      <div class="flex-grow-1">
        <div class="d-flex align-items-center gap-2 mb-2">
          <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 fw-bold small">
            <?php echo htmlspecialchars($q['subject_name']); ?>
          </span>
          <span class="text-muted fs-7"><i class="fa-solid fa-user me-1"></i><?php echo htmlspecialchars($q['full_name']); ?> &bull; <?php echo date('d M Y', strtotime($q['created_at'])); ?></span>
        </div>
        <h5 class="fw-bold mb-2">
          <a href="question_detail.php?id=<?php echo $q['question_id']; ?>" class="text-dark text-decoration-none hover-primary">
            <?php echo htmlspecialchars($q['title']); ?>
          </a>
        </h5>
        <p class="text-muted small mb-0" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
          <?php echo htmlspecialchars($q['details']); ?>
        </p>
      </div>

      <div class="text-center flex-shrink-0 ms-2">
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold d-inline-flex align-items-center gap-1">
          <i class="fa-solid fa-comment-dots text-primary"></i> <?php echo $q['answer_count']; ?>
        </span>
        <div class="mt-2">
          <a href="question_detail.php?id=<?php echo $q['question_id']; ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fs-7">Reply</a>
        </div>
      </div>
    </div>
  </div>
<?php endwhile; ?>

<?php if ($c===0): ?>
  <div class="card card-sm p-5 text-center">
    <div class="empty-state">
      <i class="fa-solid fa-comments text-muted mb-3" style="font-size: 48px;"></i>
      <h5 class="fw-bold mb-1">No questions posted yet</h5>
      <p class="text-muted small mb-3">Be the first student to post a question for your peers!</p>
      <a href="ask_question.php" class="btn btn-sm-primary rounded-pill px-4"><i class="fa-solid fa-plus me-1"></i> Ask a Question</a>
    </div>
  </div>
<?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
