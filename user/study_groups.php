<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_student();

$uid = $_SESSION['user_id'];

if (isset($_GET['action'], $_GET['id'])) {
    $gid = (int)$_GET['id'];
    if ($_GET['action'] === 'join') {
        $stmt = $conn->prepare("INSERT IGNORE INTO group_members (group_id, user_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $gid, $uid);
        $stmt->execute();
        $_SESSION['flash_success'] = "You successfully joined the study group!";
    } elseif ($_GET['action'] === 'leave') {
        $stmt = $conn->prepare("DELETE FROM group_members WHERE group_id=? AND user_id=?");
        $stmt->bind_param("ii", $gid, $uid);
        $stmt->execute();
        $_SESSION['flash_success'] = "You have left the study group.";
    }
    header("Location: study_groups.php");
    exit;
}

$subjectFilter = isset($_GET['subject']) ? (int)$_GET['subject'] : 0;
$sql = "SELECT g.*, s.subject_name, u.full_name,
        (SELECT COUNT(*) FROM group_members gm WHERE gm.group_id=g.group_id) AS member_count,
        (SELECT COUNT(*) FROM group_members gm2 WHERE gm2.group_id=g.group_id AND gm2.user_id=$uid) AS is_member
        FROM study_groups g
        JOIN subjects s ON g.subject_id = s.subject_id
        JOIN users u ON g.created_by = u.user_id";
if ($subjectFilter > 0) $sql .= " WHERE g.subject_id = $subjectFilter";
$sql .= " ORDER BY g.created_at DESC";
$groups = $conn->query($sql);

$subjects = $conn->query("SELECT * FROM subjects ORDER BY subject_name");

$base = '../';
$page_title = 'Study Groups';
include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <div>
    <h3 class="fw-bold mb-1"><i class="fa-solid fa-people-group text-success me-2"></i> Peer Study Groups</h3>
    <p class="text-muted small mb-0">Form or join subject study circles for coursework and exam prep</p>
  </div>
  <a href="create_group.php" class="btn btn-sm-primary rounded-pill"><i class="fa-solid fa-plus me-1"></i> Create Study Group</a>
</div>

<div class="card card-sm p-3 mb-4">
  <form class="row g-2" method="GET">
    <div class="col-md-9">
      <select name="subject" class="form-select">
        <option value="0">All Subject Modules</option>
        <?php while ($s = $subjects->fetch_assoc()): ?>
          <option value="<?php echo $s['subject_id']; ?>" <?php echo $subjectFilter==$s['subject_id']?'selected':''; ?>><?php echo htmlspecialchars($s['subject_name']); ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-3">
      <button class="btn btn-sm-primary w-100"><i class="fa-solid fa-filter me-1"></i> Filter Groups</button>
    </div>
  </form>
</div>

<div class="row g-4">
<?php $c=0; while ($g = $groups->fetch_assoc()): $c++; ?>
  <div class="col-md-6 col-lg-4">
    <div class="card card-sm p-4 h-100 d-flex flex-column">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1 small fw-bold">
          <?php echo htmlspecialchars($g['subject_name']); ?>
        </span>
        <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill small">
          <i class="fa-solid fa-users text-primary me-1"></i><?php echo $g['member_count']; ?>
        </span>
      </div>

      <h5 class="fw-bold mb-2 text-dark" style="font-size: 1.1rem;"><?php echo htmlspecialchars($g['group_name']); ?></h5>
      <p class="text-muted small mb-3 flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
        <?php echo htmlspecialchars($g['description']); ?>
      </p>

      <div class="border-top pt-3 mt-auto">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <span class="small text-muted"><i class="fa-solid fa-crown text-warning me-1"></i><?php echo htmlspecialchars(explode(' ', $g['full_name'])[0]); ?></span>
          <?php if ($g['is_member'] > 0): ?>
            <span class="badge bg-success text-white rounded-pill px-2.5"><i class="fa-solid fa-check me-1"></i> Member</span>
          <?php endif; ?>
        </div>

        <div class="row g-2">
          <div class="col-6">
            <a href="group_detail.php?id=<?php echo $g['group_id']; ?>" class="btn btn-sm btn-outline-secondary w-100 rounded-pill fw-semibold">View</a>
          </div>
          <div class="col-6">
            <?php if ($g['is_member'] > 0): ?>
              <a href="?action=leave&id=<?php echo $g['group_id']; ?>" class="btn btn-sm btn-outline-danger w-100 rounded-pill fw-semibold">Leave</a>
            <?php else: ?>
              <a href="?action=join&id=<?php echo $g['group_id']; ?>" class="btn btn-sm btn-sm-primary w-100 rounded-pill fw-semibold">Join Group</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endwhile; ?>

<?php if ($c===0): ?>
  <div class="col-12">
    <div class="card card-sm p-5 text-center">
      <div class="empty-state">
        <i class="fa-solid fa-people-group text-muted mb-3" style="font-size: 48px;"></i>
        <h5 class="fw-bold mb-1">No active study groups found</h5>
        <p class="text-muted small mb-3">Create a new study group and invite your NSBM peers!</p>
        <a href="create_group.php" class="btn btn-sm-primary rounded-pill px-4"><i class="fa-solid fa-plus me-1"></i> Create Study Group</a>
      </div>
    </div>
  </div>
<?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
