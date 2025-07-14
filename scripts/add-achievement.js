// add-achievement.js
function setupAchievementIconPicker() {
  const iconSelect = document.getElementById('icon-img');
  const preview = document.getElementById('icon-preview');

  // Only proceed if elements exist
  if (!iconSelect || !preview) {
    console.error("Icon picker elements not found!");
    return;
  }

  function updateIcon() {
    preview.className = iconSelect.value;
  }

  updateIcon(); // Initialize
  iconSelect.addEventListener('change', updateIcon);
}


