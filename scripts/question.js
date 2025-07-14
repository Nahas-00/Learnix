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