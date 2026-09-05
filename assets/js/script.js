// Confirm before delete actions
document.addEventListener('click', function (e) {
  if (e.target.closest('.confirm-delete')) {
    if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
      e.preventDefault();
    }
  }
});

// Simple client-side form validation helper
function smValidateForm(formId) {
  const form = document.getElementById(formId);
  if (!form) return true;
  let valid = true;
  form.querySelectorAll('[required]').forEach(function (el) {
    if (!el.value.trim()) {
      el.classList.add('is-invalid');
      valid = false;
    } else {
      el.classList.remove('is-invalid');
    }
  });
  return valid;
}

// Star rating widget: turns a hidden input + .star-input group into clickable stars
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.star-input').forEach(function (group) {
    const stars = group.querySelectorAll('i');
    const input = document.getElementById(group.dataset.target);
    stars.forEach(function (star) {
      star.addEventListener('click', function () {
        const val = parseInt(star.dataset.value, 10);
        input.value = val;
        stars.forEach(function (s) {
          s.classList.toggle('fa-solid', parseInt(s.dataset.value, 10) <= val);
          s.classList.toggle('fa-regular', parseInt(s.dataset.value, 10) > val);
        });
      });
    });
  });
});
