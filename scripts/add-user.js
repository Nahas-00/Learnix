
document.addEventListener("DOMContentLoaded",function(){
  const searchInput = document.querySelector('input[name="q"]');
  const form = searchInput.closest('form');

  searchInput.addEventListener('input', function () {
      if (this.value.trim() === '') {
       
        setTimeout(() => {
          form.submit();
        }, 200);
      }
    });
})

function openAddUser() {
  document.getElementById('overlay').style.display = 'block';
  document.getElementById('add-user').style.display = 'block';
}


 function closeAddUser() {
  document.getElementById('overlay').style.display = 'none';
  document.getElementById('add-user').style.display = 'none';
}
