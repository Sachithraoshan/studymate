<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

if (isset($_GET['action'], $_GET['type'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['type'] === 'question') {
        if ($_GET['action'] === 'moderate') {
            $conn->query("UPDATE questions SET status='moderated' WHERE question_id=$id");
        } elseif ($_GET['action'] === 'restore') {
            $conn->query("UPDATE questions SET status='open' WHERE question_id=$id");
        } elseif ($_GET['action'] === 'delete') {
            $conn->query("DELETE FROM questions WHERE question_id=$id");
        }
    } elseif ($_GET['type'] === 'answer') {
        if ($_GET['action'] === 'moderate') {
            $conn->query("UPDATE answers SET status='moderated' WHERE answer_id=$id");
        } elseif ($_GET['action'] === 'restore') {
            $conn->query("UPDATE answers SET status='visible' WHERE answer_id=$id");
        } elseif ($_GET['action'] === 'delete') {
            $conn->query("DELETE FROM answers WHERE answer_id=$id");
        }
    }
    $_SESSION['flash_success'] = "Discussion status updated.";
    header("Location: moderate_discussions.php");
    exit;
}

$questions = $conn->query("SELECT q.*, u.full_name, s.subject_name,
    (SELECT COUNT(*) FROM answers a WHERE a.question_id = q.question_id) AS answer_count
    FROM questions q
    JOIN users u ON q.posted_by = u.user_id
    JOIN subjects s ON q.subject_id = s.subject_id
    ORDER BY q.created_at DESC");

$base = '../';
$page_title = 'Moderate Discussions';
include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
  <div>
    <h3 class="fw-bold mb-1"><i class="fa-solid fa-comments text-success me-2"></i> Discussion &amp; Forum Moderation</h3>
    <p class="text-muted small mb-0">Review student questions and answers to maintain community standards</p>
  </div>
</div>

<div class="accordion" id="qaAccordion">
<?php $i = 0; while ($q = $questions->fetch_assoc()): $i++; ?>
  <div class="card card-sm mb-3 border-0 shadow-sm overflow-hidden">
    <div class="p-3 bg-white d-flex align-items-center justify-content-between flex-wrap gap-2">
      <div class="d-flex align-items-center gap-2">
        <span class="badge badge-<?php echo $q['status']=='open'?'approved':($q['status']=='moderated'?'rejected':'pending'); ?>">
          <?php echo ucfirst($q['status']); ?>
        </span>
        <h6 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($q['title']); ?></h6>
      </div>
      <div class="d-flex align-items-center gap-2">
        <span class="badge bg-light text-dark border"><i class="fa-solid fa-book me-1 text-success"></i><?php echo htmlspecialchars($q['subject_name']); ?></span>
        <span class="small text-muted"><i class="fa-solid fa-user me-1"></i><?php echo htmlspecialchars($q['full_name']); ?></span>
        <button class="btn btn-sm btn-outline-secondary rounded-pill ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#q<?php echo $q['question_id']; ?>">
          Inspect Discussion (<?php echo $q['answer_count']; ?>)
        </button>
      </div>
    </div>

    <div id="q<?php echo $q['question_id']; ?>" class="collapse p-4 border-top bg-light" data-bs-parent="#qaAccordion">
      <div class="p-3 bg-white border rounded-3 mb-3">
        <h6 class="fw-bold text-muted small uppercase">Question Context:</h6>
        <p class="mb-0 text-dark"><?php echo nl2br(htmlspecialchars($q['details'])); ?></p>
      </div>

      <div class="d-flex gap-2 mb-4">
        <?php if ($q['status'] !== 'moderated'): ?>
          <a href="?action=moderate&type=question&id=<?php echo $q['question_id']; ?>" class="btn btn-sm btn-outline-warning rounded-pill px-3"><i class="fa-solid fa-eye-slash me-1"></i> Hide Question</a>
        <?php else: ?>
          <a href="?action=restore&type=question&id=<?php echo $q['question_id']; ?>" class="btn btn-sm btn-outline-success rounded-pill px-3"><i class="fa-solid fa-eye me-1"></i> Restore Question</a>
        <?php endif; ?>
        <a href="?action=delete&type=question&id=<?php echo $q['question_id']; ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Delete this question and all its answers?');"><i class="fa-solid fa-trash me-1"></i> Delete Question</a>
      </div>

      <h6 class="fw-bold mb-3"><i class="fa-solid fa-reply me-1 text-primary"></i> Answers (<?php echo $q['answer_count']; ?>)</h6>
      <?php
      $ares = $conn->query("SELECT a.*, u.full_name FROM answers a JOIN users u ON a.answered_by = u.user_id WHERE a.question_id = " . $q['question_id'] . " ORDER BY a.created_at ASC");
      if ($ares->num_rows === 0): ?>
        <p class="text-muted small italic">No answers posted for this question yet.</p>
      <?php endif; ?>

      <?php while ($a = $ares->fetch_assoc()): ?>
        <div class="p-3 bg-white border rounded-3 mb-2">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <strong class="text-dark small"><i class="fa-solid fa-circle-user text-primary me-1"></i><?php echo htmlspecialchars($a['full_name']); ?></strong>
            <span class="badge badge-<?php echo $a['status']=='visible'?'approved':'rejected'; ?>"><?php echo ucfirst($a['status']); ?></span>
          </div>
          <p class="mb-2 small text-secondary"><?php echo nl2br(htmlspecialchars($a['answer_text'])); ?></p>
          <div class="d-flex gap-2">
            <?php if ($a['status'] !== 'moderated'): ?>
              <a href="?action=moderate&type=answer&id=<?php echo $a['answer_id']; ?>" class="btn btn-xs btn-outline-warning rounded-pill px-2 py-0 fs-7">Hide Answer</a>
            <?php else: ?>
              <a href="?action=restore&type=answer&id=<?php echo $a['answer_id']; ?>" class="btn btn-xs btn-outline-success rounded-pill px-2 py-0 fs-7">Restore Answer</a>
            <?php endif; ?>
            <a href="?action=delete&type=answer&id=<?php echo $a['answer_id']; ?>" class="btn btn-xs btn-outline-danger rounded-pill px-2 py-0 fs-7" onclick="return confirm('Delete this answer?');">Delete Answer</a>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
<?php endwhile; ?>

<?php if ($i === 0): ?>
  <div class="card card-sm p-5 text-center">
    <div class="empty-state">
      <i class="fa-solid fa-comments-slash text-muted mb-2" style="font-size: 42px;"></i>
      <p class="text-muted small mb-0">No forum questions posted yet.</p>
    </div>
  </div>
<?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
