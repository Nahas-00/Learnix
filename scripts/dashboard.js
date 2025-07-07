const navLinks = document.querySelectorAll('.nav-item');
const urlParams = new URLSearchParams(window.location.search);
const page = urlParams.get('page') || 'home';

navLinks.forEach(link => {
  const href = new URL(link.href).searchParams.get('page');
  if (href === page) {
    link.classList.add('active');
  } else {
    link.classList.remove('active');
  }
});
