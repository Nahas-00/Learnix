function togglePassword(inputId, icon) {
  const input = document.getElementById(inputId);
  
  if (input.type === "password") {
    input.type = "text";
    icon.classList.remove("bi-shield-lock");
    icon.classList.add("bi-shield-x");
  } else {
    input.type = "password";
    icon.classList.remove("bi-shield-x");
    icon.classList.add("bi-shield-lock");
  }
}
