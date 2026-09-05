<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_student();

$qid = (int)($_GET['id'] ?? 0);
$uid = $_SESSION['user_id'];

$res = $conn->query("SELECT q.*, u.full_name, s.subject_name FROM questions q
                      JOIN users u ON q.posted_by = u.user_id
                      JOIN subjects s ON q.subject_id = s.subject_id
                      WHERE q.question_id = $qid");
$question = $res->fetch_assoc();
if (!$question) {
    header("Location: questions.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer_text'])) {
    $answer = clean($conn, $_POST['answer_text']);
    if ($answer !== '') {
        $stmt = $conn->prepare("INSERT INTO answers (question_id, answered_by, answer_text) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $qid, $uid, $answer);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: question_detail.php?id=$qid");
    exit;
}

$answers = $conn->query("SELECT a.*, u.full_name FROM answers a JOIN users u ON a.answered_by = u.user_id WHERE a.question_id=$qid AND a.status='visible' ORDER BY a.created_at ASC");

$base = '../';
$page_title = 'Question Discussion';
include __DIR__ . '/../includes/header.php';
?>

<div class="mb-3">
  <a href="questions.php" class="btn btn-sm btn-lms-outline rounded-pill"><i class="fa-solid fa-arrow-left me-1"></i> Back to Q&amp;A Forum</a>
</div>

<div class="card card-lms p-4 p-md-5 mb-4">
  <div class="d-flex align-items-center gap-2 mb-2">
    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 fw-bold">
      <?php echo htmlspecialchars($question['subject_name']); ?>
    </span>
  </div>

  <h2 class="fw-bold font-heading mb-2"><?php echo htmlspecialchars($question['title']); ?></h2>
  <div class="text-muted small mb-3">
    <i class="fa-solid fa-circle-user text-primary me-1"></i> Asked by <strong><?php echo htmlspecialchars($question['full_name']); ?></strong> &bull; <?php echo date('d M Y, H:i', strtotime($question['created_at'])); ?>
  </div>

  <div class="p-4 bg-light rounded-4">
    <p class="mb-0 text-dark"><?php echo nl2br(htmlspecialchars($question['details'])); ?></p>
  </div>
</div>

<h4 class="fw-bold font-heading mb-3"><i class="fa-solid fa-comments text-success me-2"></i> <?php echo $answers->num_rows; ?> Answer(s)</h4>

<?php while ($a = $answers->fetch_assoc()): ?>
  <div class="card card-lms p-4 mb-3 border-start border-4 border-success">
    <div class="d-flex align-items-center justify-content-between mb-2">
      <strong class="text-dark"><i class="fa-solid fa-user-gear text-success me-2"></i><?php echo htmlspecialchars($a['full_name']); ?></strong>
      <small class="text-muted fs-8"><?php echo date('d M Y, H:i', strtotime($a['created_at'])); ?></small>
    </div>
    <p class="mb-0 text-secondary"><?php echo nl2br(htmlspecialchars($a['answer_text'])); ?></p>
  </div>
<?php endwhile; ?>

<div class="card card-lms p-4 mt-4">
  <h5 class="fw-bold font-heading mb-3"><i class="fa-solid fa-reply text-primary me-2"></i> Contribute Your Answer</h5>
  <form method="POST">
    <div class="mb-3">
      <textarea name="answer_text" class="form-control" rows="4" required placeholder="Write a clear, helpful solution to assist your classmate..."></textarea>
    </div>
    <button type="submit" class="btn btn-lms-primary rounded-pill px-4"><i class="fa-solid fa-paper-plane me-1"></i> Post Solution</button>
  </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
