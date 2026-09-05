<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_admin();

// Handle actions: block, unblock, delete
if (isset($_GET['action'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] === 'block') {
        $conn->query("UPDATE users SET status='blocked' WHERE user_id=$id AND role='student'");
        $_SESSION['flash_success'] = "User account has been blocked.";
    } elseif ($_GET['action'] === 'unblock') {
        $conn->query("UPDATE users SET status='active' WHERE user_id=$id AND role='student'");
        $_SESSION['flash_success'] = "User account has been activated.";
    } elseif ($_GET['action'] === 'delete') {
        $conn->query("DELETE FROM users WHERE user_id=$id AND role='student'");
        $_SESSION['flash_success'] = "User record deleted permanently.";
    }
    header("Location: manage_users.php");
    exit;
}

$search = isset($_GET['q']) ? clean($conn, $_GET['q']) : '';
$sql = "SELECT * FROM users WHERE role='student'";
if ($search !== '') {
    $sql .= " AND (full_name LIKE '%$search%' OR email LIKE '%$search%')";
}
$sql .= " ORDER BY created_at DESC";
$users = $conn->query($sql);

$base = '../';
$page_title = 'Manage Users';
include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <div>
    <h3 class="fw-bold mb-1"><i class="fa-solid fa-users text-success me-2"></i> Student Account Directory</h3>
    <p class="text-muted small mb-0">Manage registered NSBM student accounts, access permissions, and status</p>
  </div>
  <form class="d-flex" method="GET" style="max-width: 320px;">
    <div class="input-group">
      <input type="text" name="q" class="form-control form-control-sm" placeholder="Search name or email..." value="<?php echo htmlspecialchars($search); ?>">
      <button class="btn btn-sm btn-sm-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
    </div>
  </form>
</div>

<div class="card card-sm p-4">
  <div class="table-responsive">
    <table class="table table-custom">
      <thead>
        <tr>
          <th>#</th>
          <th>Student Name</th>
          <th>Email Address</th>
          <th>Account Status</th>
          <th>Registered Date</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php $i = 1; while ($u = $users->fetch_assoc()): ?>
        <tr>
          <td class="text-muted small"><?php echo $i++; ?></td>
          <td class="fw-semibold text-dark"><i class="fa-solid fa-circle-user text-primary me-2"></i><?php echo htmlspecialchars($u['full_name']); ?></td>
          <td class="text-muted small"><?php echo htmlspecialchars($u['email']); ?></td>
          <td>
            <?php if ($u['status'] === 'active'): ?>
              <span class="badge badge-approved"><i class="fa-solid fa-check-circle me-1"></i>Active</span>
            <?php else: ?>
              <span class="badge badge-rejected"><i class="fa-solid fa-ban me-1"></i>Blocked</span>
            <?php endif; ?>
          </td>
          <td class="text-muted small"><?php echo date('d M Y', strtotime($u['created_at'])); ?></td>
          <td class="text-end">
            <div class="btn-group">
              <?php if ($u['status'] === 'active'): ?>
                <a href="?action=block&id=<?php echo $u['user_id']; ?>" class="btn btn-sm btn-outline-warning rounded-start-pill px-3">Block</a>
              <?php else: ?>
                <a href="?action=unblock&id=<?php echo $u['user_id']; ?>" class="btn btn-sm btn-outline-success rounded-start-pill px-3">Unblock</a>
              <?php endif; ?>
              <a href="?action=delete&id=<?php echo $u['user_id']; ?>" class="btn btn-sm btn-outline-danger rounded-end-pill px-3" onclick="return confirm('Are you sure you want to delete this user account?');">Delete</a>
            </div>
          </td>
        </tr>
      <?php endwhile; ?>
      <?php if ($i === 1): ?>
        <tr>
          <td colspan="6">
            <div class="empty-state py-4">
              <i class="fa-solid fa-users-slash text-muted mb-2" style="font-size: 42px;"></i>
              <p class="text-muted small mb-0">No matching student accounts found.</p>
            </div>
          </td>
        </tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
