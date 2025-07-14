
  window.addEventListener('DOMContentLoaded', () => {
    const intro = document.getElementById('logo-intro');

    // Step 1: Shrink after 1 second
    setTimeout(() => {
      intro.classList.add('shrink');
    }, 1000);

    // Step 2: Remove after animation finishes
    setTimeout(() => {
      intro.classList.add('hidden');
    }, 1800); // slightly after animation ends
  });

