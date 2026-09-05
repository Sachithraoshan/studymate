<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_student();

$subjects = $conn->query("SELECT * FROM subjects ORDER BY subject_name");
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean($conn, $_POST['group_name'] ?? '');
    $desc = clean($conn, $_POST['description'] ?? '');
    $subject_id = (int)($_POST['subject_id'] ?? 0);
    $uid = $_SESSION['user_id'];

    if ($name === '' || $subject_id === 0) {
        $errors[] = "Please specify a group title and select a computing module.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO study_groups (group_name, subject_id, description, created_by) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sisi", $name, $subject_id, $desc, $uid);
        $stmt->execute();
        $newId = $stmt->insert_id;
        $stmt->close();

        // Auto-join creator as first member
        $stmt2 = $conn->prepare("INSERT INTO group_members (group_id, user_id) VALUES (?, ?)");
        $stmt2->bind_param("ii", $newId, $uid);
        $stmt2->execute();
        $stmt2->close();

        $_SESSION['flash_success'] = "Study circle created successfully!";
        header("Location: group_detail.php?id=$newId");
        exit;
    }
}

$base = '../';
$page_title = 'Create Study Circle';
include __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card card-lms p-4 p-md-5">
      <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
        <div class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning rounded-4 p-3" style="width: 56px; height: 56px;">
          <i class="fa-solid fa-people-group fa-2xl"></i>
        </div>
        <div>
          <h4 class="fw-bold font-heading mb-1">Create a Study Circle</h4>
          <p class="text-muted small mb-0">Build a collaborative study group with fellow undergraduates for module goals</p>
        </div>
      </div>

      <?php foreach ($errors as $err): ?>
        <div class="alert alert-danger rounded-3 py-2 small mb-3"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err); ?></div>
      <?php endforeach; ?>

      <form method="POST" id="groupForm" novalidate>
        <div class="mb-3">
          <label class="form-label small fw-bold">Study Group Name <span class="text-danger">*</span></label>
          <input type="text" name="group_name" class="form-control" placeholder="e.g. OOP Java Final Exam Revision Squad" required value="<?php echo htmlspecialchars($_POST['group_name'] ?? ''); ?>">
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
          <label class="form-label small fw-bold">Group Goals &amp; Overview</label>
          <textarea name="description" class="form-control" rows="4" placeholder="Describe what this study group will focus on (e.g. weekly revision, coursework prep, sharing past paper answers)..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
        </div>

        <div class="d-flex align-items-center justify-content-between pt-2">
          <a href="study_groups.php" class="btn btn-lms-outline rounded-pill px-4">Cancel</a>
          <button type="submit" class="btn btn-lms-primary rounded-pill px-4"><i class="fa-solid fa-check me-2"></i>Create Group</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
