    </main>

    <footer class="bg-white border-top py-3 px-4 mt-auto">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 fs-7 text-muted">
        <div>
          <span class="fw-bold text-dark font-heading"><i class="fa-solid fa-graduation-cap text-success me-1"></i>StudyMate LMS</span> &bull; NSBM Green University Peer Collaboration Platform
        </div>
        <div>
          &copy; <?php echo date('Y'); ?> Faculty of Computing. Developed by 1st Year Undergraduates.
        </div>
      </div>
    </footer>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const sidebar = document.getElementById('lmsSidebar');
  const toggleBtn = document.getElementById('sidebarToggle');
  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener('click', function() {
      sidebar.classList.toggle('show');
    });
  }
});
</script>
<script src="<?php echo isset($base) ? $base : ''; ?>assets/js/script.js"></script>
</body>
</html>
