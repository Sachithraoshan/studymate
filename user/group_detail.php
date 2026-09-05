<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_student();

$gid = (int)($_GET['id'] ?? 0);
$uid = $_SESSION['user_id'];

$res = $conn->query("SELECT g.*, s.subject_name, u.full_name FROM study_groups g
                      JOIN subjects s ON g.subject_id = s.subject_id
                      JOIN users u ON g.created_by = u.user_id
                      WHERE g.group_id = $gid");
$group = $res->fetch_assoc();
if (!$group) {
    header("Location: study_groups.php");
    exit;
}

$isMember = $conn->query("SELECT COUNT(*) c FROM group_members WHERE group_id=$gid AND user_id=$uid")->fetch_assoc()['c'] > 0;

if (isset($_GET['action'])) {
    if ($_GET['action'] === 'join') {
        $stmt = $conn->prepare("INSERT IGNORE INTO group_members (group_id, user_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $gid, $uid);
        $stmt->execute();
    } elseif ($_GET['action'] === 'leave') {
        $stmt = $conn->prepare("DELETE FROM group_members WHERE group_id=? AND user_id=?");
        $stmt->bind_param("ii", $gid, $uid);
        $stmt->execute();
    }
    header("Location: group_detail.php?id=$gid");
    exit;
}

$members = $conn->query("SELECT u.full_name, u.email, gm.joined_at FROM group_members gm JOIN users u ON gm.user_id = u.user_id WHERE gm.group_id=$gid ORDER BY gm.joined_at ASC");

$base = '../';
$page_title = htmlspecialchars($group['group_name']);
include __DIR__ . '/../includes/header.php';
?>

<div class="mb-3">
  <a href="study_groups.php" class="btn btn-sm btn-outline-secondary rounded-pill"><i class="fa-solid fa-arrow-left me-1"></i> Back to Study Groups</a>
</div>

<div class="card card-sm p-4 mb-4">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
    <div>
      <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 fw-bold mb-2">
        <?php echo htmlspecialchars($group['subject_name']); ?>
      </span>
      <h3 class="fw-bold mb-1"><?php echo htmlspecialchars($group['group_name']); ?></h3>
      <p class="text-muted small mb-0"><i class="fa-solid fa-crown text-warning me-1"></i> Created by <strong><?php echo htmlspecialchars($group['full_name']); ?></strong></p>
    </div>
    <div>
      <?php if ($isMember): ?>
        <a href="?action=leave" class="btn btn-outline-danger rounded-pill px-4"><i class="fa-solid fa-user-minus me-1"></i> Leave Group</a>
      <?php else: ?>
        <a href="?action=join" class="btn btn-sm-primary rounded-pill px-4"><i class="fa-solid fa-user-plus me-1"></i> Join Group</a>
      <?php endif; ?>
    </div>
  </div>

  <div class="p-3 bg-light rounded-3">
    <p class="mb-0 text-dark"><?php echo nl2br(htmlspecialchars($group['description'])); ?></p>
  </div>
</div>

<div class="card card-sm p-4">
  <h5 class="fw-bold mb-3"><i class="fa-solid fa-users text-success me-2"></i> Group Members (<?php echo $members->num_rows; ?>)</h5>
  <div class="table-responsive">
    <table class="table table-custom">
      <thead>
        <tr>
          <th>Student Name</th>
          <th>Student Email</th>
          <th>Joined Date</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($m = $members->fetch_assoc()): ?>
        <tr>
          <td class="fw-semibold"><i class="fa-solid fa-circle-user text-primary me-2"></i><?php echo htmlspecialchars($m['full_name']); ?></td>
          <td class="text-muted"><?php echo htmlspecialchars($m['email']); ?></td>
          <td class="text-muted small"><?php echo date('d M Y', strtotime($m['joined_at'])); ?></td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
