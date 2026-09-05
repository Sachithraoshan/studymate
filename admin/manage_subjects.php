<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

$editSubject = null;

// Delete
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    $id = (int)$_GET['id'];
    $conn->query("DELETE FROM subjects WHERE subject_id=$id");
    $_SESSION['flash_success'] = "Subject module deleted successfully.";
    header("Location: manage_subjects.php");
    exit;
}

// Load for editing
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'edit') {
    $id = (int)$_GET['id'];
    $res = $conn->query("SELECT * FROM subjects WHERE subject_id=$id");
    $editSubject = $res->fetch_assoc();
}

// Create / Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean($conn, $_POST['subject_name'] ?? '');
    $desc = clean($conn, $_POST['description'] ?? '');
    $subject_id = $_POST['subject_id'] ?? '';

    if ($name === '') {
        $_SESSION['flash_error'] = "Subject name is required.";
    } else {
        if ($subject_id !== '') {
            $sid = (int)$subject_id;
            $stmt = $conn->prepare("UPDATE subjects SET subject_name=?, description=? WHERE subject_id=?");
            $stmt->bind_param("ssi", $name, $desc, $sid);
            $stmt->execute();
            $_SESSION['flash_success'] = "Subject module updated successfully.";
        } else {
            $stmt = $conn->prepare("INSERT INTO subjects (subject_name, description) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $desc);
            $stmt->execute();
            $_SESSION['flash_success'] = "New subject module added successfully.";
        }
        $stmt->close();
    }
    header("Location: manage_subjects.php");
    exit;
}

$subjects = $conn->query("SELECT s.*, (SELECT COUNT(*) FROM materials m WHERE m.subject_id = s.subject_id) AS material_count FROM subjects s ORDER BY s.subject_name");

$base = '../';
$page_title = 'Manage Subjects';
include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
  <div>
    <h3 class="fw-bold mb-1"><i class="fa-solid fa-book text-success me-2"></i> Computing Modules &amp; Subjects</h3>
    <p class="text-muted small mb-0">Manage subject categories for study resources and student Q&amp;A</p>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-4">
    <div class="card card-sm p-4">
      <h5 class="fw-bold mb-3"><i class="fa-solid fa-pen-to-square text-primary me-2"></i><?php echo $editSubject ? 'Edit Subject' : 'Add Subject Module'; ?></h5>
      <form method="POST">
        <?php if ($editSubject): ?>
          <input type="hidden" name="subject_id" value="<?php echo $editSubject['subject_id']; ?>">
        <?php endif; ?>

        <div class="mb-3">
          <label class="form-label small fw-bold">Subject Name <span class="text-danger">*</span></label>
          <input type="text" name="subject_name" class="form-control" placeholder="e.g. Data Structures &amp; Algorithms" required
                 value="<?php echo $editSubject ? htmlspecialchars($editSubject['subject_name']) : ''; ?>">
        </div>

        <div class="mb-4">
          <label class="form-label small fw-bold">Module Description</label>
          <textarea name="description" class="form-control" rows="3" placeholder="Brief outline of module content..."><?php echo $editSubject ? htmlspecialchars($editSubject['description']) : ''; ?></textarea>
        </div>

        <button type="submit" class="btn btn-sm-primary w-100 rounded-pill mb-2"><i class="fa-solid fa-save me-1"></i> <?php echo $editSubject ? 'Update Module' : 'Add Module'; ?></button>
        <?php if ($editSubject): ?>
          <a href="manage_subjects.php" class="btn btn-outline-secondary w-100 rounded-pill">Cancel Edit</a>
        <?php endif; ?>
      </form>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card card-sm p-4">
      <div class="table-responsive">
        <table class="table table-custom">
          <thead>
            <tr>
              <th>Module Name</th>
              <th>Description</th>
              <th>Materials</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php while ($s = $subjects->fetch_assoc()): ?>
            <tr>
              <td class="fw-semibold text-dark"><i class="fa-solid fa-book-bookmark text-success me-2"></i><?php echo htmlspecialchars($s['subject_name']); ?></td>
              <td class="text-muted small"><?php echo htmlspecialchars($s['description']); ?></td>
              <td><span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill"><i class="fa-solid fa-file me-1 text-primary"></i><?php echo $s['material_count']; ?></span></td>
              <td class="text-end">
                <div class="btn-group">
                  <a href="?action=edit&id=<?php echo $s['subject_id']; ?>" class="btn btn-sm btn-outline-primary rounded-start-pill px-3">Edit</a>
                  <a href="?action=delete&id=<?php echo $s['subject_id']; ?>" class="btn btn-sm btn-outline-danger rounded-end-pill px-3" onclick="return confirm('Are you sure you want to delete this subject module?');">Delete</a>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
