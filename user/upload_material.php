<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_student();

$subjects = $conn->query("SELECT * FROM subjects ORDER BY subject_name");
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = clean($conn, $_POST['title'] ?? '');
    $description = clean($conn, $_POST['description'] ?? '');
    $subject_id = (int)($_POST['subject_id'] ?? 0);

    if ($title === '' || $subject_id === 0) {
        $errors[] = "Please provide both material title and select a computing module.";
    }

    if (empty($_FILES['material_file']['name'])) {
        $errors[] = "Please choose a document or file to upload.";
    } else {
        $allowedExt = ['pdf','doc','docx','ppt','pptx','txt','zip','xls','xlsx'];
        $fileName = $_FILES['material_file']['name'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt)) {
            $errors[] = "Invalid file format. Allowed types: " . strtoupper(implode(', ', $allowedExt));
        }
        if ($_FILES['material_file']['size'] > 20 * 1024 * 1024) {
            $errors[] = "File size exceeds the 20MB limit.";
        }
    }

    if (empty($errors)) {
        $newName = uniqid('mat_') . '.' . $ext;
        $destination = __DIR__ . '/../uploads/materials/' . $newName;
        if (move_uploaded_file($_FILES['material_file']['tmp_name'], $destination)) {
            $uid = $_SESSION['user_id'];
            $stmt = $conn->prepare("INSERT INTO materials (title, description, subject_id, uploaded_by, file_path) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiis", $title, $description, $subject_id, $uid, $newName);
            $stmt->execute();
            $stmt->close();
            $_SESSION['flash_success'] = "Resource submitted successfully! It is now pending admin approval.";
            header("Location: materials.php");
            exit;
        } else {
            $errors[] = "File upload encountered a server error. Please try again.";
        }
    }
}

$base = '../';
$page_title = 'Upload Material';
include __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card card-lms p-4 p-md-5">
      <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
        <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-4 p-3" style="width: 56px; height: 56px;">
          <i class="fa-solid fa-cloud-arrow-up fa-2xl"></i>
        </div>
        <div>
          <h4 class="fw-bold font-heading mb-1">Submit Academic Resource</h4>
          <p class="text-muted small mb-0">Contribute lecture slides, past paper solutions, or tutorial guides</p>
        </div>
      </div>

      <?php foreach ($errors as $err): ?>
        <div class="alert alert-danger rounded-3 py-2 small mb-3"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err); ?></div>
      <?php endforeach; ?>

      <form method="POST" enctype="multipart/form-data" id="uploadForm" novalidate>
        <div class="mb-3">
          <label class="form-label small fw-bold">Resource Title <span class="text-danger">*</span></label>
          <input type="text" name="title" class="form-control" placeholder="e.g. SE101 Programming Fundamentals Mid-Semester Answers" required value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
        </div>

        <div class="mb-3">
          <label class="form-label small fw-bold">Computing Subject Module <span class="text-danger">*</span></label>
          <select name="subject_id" class="form-select" required>
            <option value="">-- Select Subject Module --</option>
            <?php while ($s = $subjects->fetch_assoc()): ?>
              <option value="<?php echo $s['subject_id']; ?>"><?php echo htmlspecialchars($s['subject_name']); ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label small fw-bold">Overview / Notes</label>
          <textarea name="description" class="form-control" rows="3" placeholder="Provide context about this material (e.g. exam year, batch notes, lecturer references)..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
        </div>

        <div class="mb-4">
          <label class="form-label small fw-bold">Upload File Document <span class="text-danger">*</span></label>
          <input type="file" name="material_file" class="form-control" required>
          <div class="form-text mt-2 text-muted small">
            <i class="fa-solid fa-circle-info me-1"></i> Supported: <span class="badge bg-light text-dark border">PDF</span> <span class="badge bg-light text-dark border">DOCX</span> <span class="badge bg-light text-dark border">PPTX</span> <span class="badge bg-light text-dark border">ZIP</span> (Max 20MB)
          </div>
        </div>

        <div class="d-flex align-items-center justify-content-between pt-2">
          <a href="materials.php" class="btn btn-lms-outline rounded-pill px-4">Cancel</a>
          <button type="submit" class="btn btn-lms-primary rounded-pill px-4"><i class="fa-solid fa-paper-plane me-2"></i>Submit for Review</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
