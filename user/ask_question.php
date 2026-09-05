<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_student();

$subjects = $conn->query("SELECT * FROM subjects ORDER BY subject_name");
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = clean($conn, $_POST['title'] ?? '');
    $details = clean($conn, $_POST['details'] ?? '');
    $subject_id = (int)($_POST['subject_id'] ?? 0);
    $uid = $_SESSION['user_id'];

    if ($title === '' || $subject_id === 0) {
        $errors[] = "Please enter a question title and select a computing module.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO questions (subject_id, posted_by, title, details) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $subject_id, $uid, $title, $details);
        $stmt->execute();
        $newId = $stmt->insert_id;
        $stmt->close();
        $_SESSION['flash_success'] = "Your question has been posted successfully!";
        header("Location: question_detail.php?id=$newId");
        exit;
    }
}

$base = '../';
$page_title = 'Ask Question';
include __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card card-lms p-4 p-md-5">
      <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
        <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-4 p-3" style="width: 56px; height: 56px;">
          <i class="fa-solid fa-circle-question fa-2xl"></i>
        </div>
        <div>
          <h4 class="fw-bold font-heading mb-1">Post a Question</h4>
          <p class="text-muted small mb-0">Ask your peers for help on assignment logic, coursework, or exam prep</p>
        </div>
      </div>

      <?php foreach ($errors as $err): ?>
        <div class="alert alert-danger rounded-3 py-2 small mb-3"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err); ?></div>
      <?php endforeach; ?>

      <form method="POST" id="askForm" novalidate>
        <div class="mb-3">
          <label class="form-label small fw-bold">Question Summary / Title <span class="text-danger">*</span></label>
          <input type="text" name="title" class="form-control" placeholder="e.g. How to implement binary search trees in C++?" required value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
        </div>

        <div class="mb-3">
          <label class="form-label small fw-bold">Computing Module <span class="text-danger">*</span></label>
          <select name="subject_id" class="form-select" required>
            <option value="">-- Select Subject Module --</option>
            <?php while ($s = $subjects->fetch_assoc()): ?>
              <option value="<?php echo $s['subject_id']; ?>"><?php echo htmlspecialchars($s['subject_name']); ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="mb-4">
          <label class="form-label small fw-bold">Question Details &amp; Context</label>
          <textarea name="details" class="form-control" rows="5" placeholder="Explain what you are trying to solve, error messages, or code snippets..."><?php echo htmlspecialchars($_POST['details'] ?? ''); ?></textarea>
        </div>

        <div class="d-flex align-items-center justify-content-between pt-2">
          <a href="questions.php" class="btn btn-lms-outline rounded-pill px-4">Cancel</a>
          <button type="submit" class="btn btn-lms-primary rounded-pill px-4"><i class="fa-solid fa-paper-plane me-2"></i>Post Question</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
