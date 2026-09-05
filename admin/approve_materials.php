<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

if (isset($_GET['action'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] === 'approve') {
        $conn->query("UPDATE materials SET status='approved' WHERE material_id=$id");
        $_SESSION['flash_success'] = "Material approved successfully!";
    } elseif ($_GET['action'] === 'reject') {
        $conn->query("UPDATE materials SET status='rejected' WHERE material_id=$id");
        $_SESSION['flash_success'] = "Material status set to rejected.";
    } elseif ($_GET['action'] === 'delete') {
        $res = $conn->query("SELECT file_path FROM materials WHERE material_id=$id");
        if ($row = $res->fetch_assoc()) {
            $filePath = __DIR__ . '/../uploads/materials/' . $row['file_path'];
            if (file_exists($filePath)) unlink($filePath);
        }
        $conn->query("DELETE FROM materials WHERE material_id=$id");
        $_SESSION['flash_success'] = "Material file permanently deleted.";
    }
    header("Location: approve_materials.php?status=" . ($_GET['status'] ?? 'pending'));
    exit;
}

$filter = $_GET['status'] ?? 'pending';
$allowed = ['pending', 'approved', 'rejected', 'all'];
if (!in_array($filter, $allowed)) $filter = 'pending';

$sql = "SELECT m.*, u.full_name, s.subject_name FROM materials m
        JOIN users u ON m.uploaded_by = u.user_id
        JOIN subjects s ON m.subject_id = s.subject_id";
if ($filter !== 'all') {
    $sql .= " WHERE m.status = '" . $conn->real_escape_string($filter) . "'";
}
$sql .= " ORDER BY m.created_at DESC";
$materials = $conn->query($sql);

$base = '../';
$page_title = 'Approve Materials';
include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <div>
    <h3 class="fw-bold mb-1"><i class="fa-solid fa-file-circle-check text-success me-2"></i> Material Moderation</h3>
    <p class="text-muted small mb-0">Review student uploads before publication to the main learning platform</p>
  </div>
  <div class="btn-group bg-white p-1 border rounded-pill shadow-sm">
    <a href="?status=pending" class="btn btn-sm rounded-pill px-3 <?php echo $filter=='pending'?'btn-sm-primary':'text-dark'; ?>">Pending</a>
    <a href="?status=approved" class="btn btn-sm rounded-pill px-3 <?php echo $filter=='approved'?'btn-sm-primary':'text-dark'; ?>">Approved</a>
    <a href="?status=rejected" class="btn btn-sm rounded-pill px-3 <?php echo $filter=='rejected'?'btn-sm-primary':'text-dark'; ?>">Rejected</a>
    <a href="?status=all" class="btn btn-sm rounded-pill px-3 <?php echo $filter=='all'?'btn-sm-primary':'text-dark'; ?>">All Files</a>
  </div>
</div>

<div class="card card-sm p-4">
  <div class="table-responsive">
    <table class="table table-custom">
      <thead>
        <tr>
          <th>Resource Title</th>
          <th>Subject Module</th>
          <th>Uploaded By</th>
          <th>Submission Date</th>
          <th>Status</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php $count = 0; while ($m = $materials->fetch_assoc()): $count++; ?>
        <tr>
          <td>
            <div class="fw-bold text-dark"><?php echo htmlspecialchars($m['title']); ?></div>
            <a href="../uploads/materials/<?php echo htmlspecialchars($m['file_path']); ?>" target="_blank" class="small text-primary text-decoration-none">
              <i class="fa-solid fa-file-arrow-down me-1"></i> Preview / Download File
            </a>
          </td>
          <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($m['subject_name']); ?></span></td>
          <td class="text-dark small"><i class="fa-solid fa-circle-user text-success me-1"></i><?php echo htmlspecialchars($m['full_name']); ?></td>
          <td class="text-muted small"><?php echo date('d M Y', strtotime($m['created_at'])); ?></td>
          <td><span class="badge badge-<?php echo $m['status']; ?>"><?php echo ucfirst($m['status']); ?></span></td>
          <td class="text-end">
            <div class="btn-group">
              <?php if ($m['status'] !== 'approved'): ?>
                <a href="?action=approve&id=<?php echo $m['material_id']; ?>&status=<?php echo $filter; ?>" class="btn btn-sm btn-outline-success rounded-start-pill px-3">Approve</a>
              <?php endif; ?>
              <?php if ($m['status'] !== 'rejected'): ?>
                <a href="?action=reject&id=<?php echo $m['material_id']; ?>&status=<?php echo $filter; ?>" class="btn btn-sm btn-outline-warning px-3">Reject</a>
              <?php endif; ?>
              <a href="?action=delete&id=<?php echo $m['material_id']; ?>&status=<?php echo $filter; ?>" class="btn btn-sm btn-outline-danger rounded-end-pill px-3" onclick="return confirm('Are you sure you want to delete this resource file?');">Delete</a>
            </div>
          </td>
        </tr>
      <?php endwhile; ?>
      <?php if ($count === 0): ?>
        <tr>
          <td colspan="6">
            <div class="empty-state py-4">
              <i class="fa-solid fa-circle-check text-muted mb-2" style="font-size: 42px;"></i>
              <p class="text-muted small mb-0">No materials currently in this status view.</p>
            </div>
          </td>
        </tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
