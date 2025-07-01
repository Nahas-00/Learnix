 function togglePassword(){

  const input = document.getElementById('password');
  const icon = document.querySelector('.shield-icon');

  if(input.type == "password"){
    input.type = "string";
    icon.classList.add("bi-shield-x");
    icon.classList.remove("bi-shield-lock");
  }else{
    input.type = "password";
    icon.classList.add("bi-shield-lock");
    icon.classList.remove("bi-shield-x");
  }


 }