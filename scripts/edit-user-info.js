
function editDetails(){
  const overlay = document.getElementById('overlay');
  const div = document.getElementById('edit-div');
  overlay.style.display = 'block';
  div.style.display = 'flex';
}

function closeField(){
  const overlay = document.getElementById('overlay');
  const div = document.getElementById('edit-div');
  overlay.style.display = 'none';
  div.style.display = 'none';  
}

  const photoUpload = document.getElementById('photoUpload');
  const dpDisplayImg = document.querySelector('.dp-display img');

  photoUpload.addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();

      reader.onload = function(e) {
        dpDisplayImg.src = e.target.result;
      }

      reader.readAsDataURL(file);
    }
  });

  function showSolution(button){
    const overlay = document.getElementById('overlay');
    const div = document.getElementById('view-solution');
    overlay.style.display = 'block';
    div.style.display = 'flex';
    const area = document.getElementById('code-dis');
    area.textContent = button.dataset.code;
    window.scrollTo({
      top: 0,
      behavior: 'smooth' 
    });
  }

  function closeSolution(){
    const overlay = document.getElementById('overlay');
    const div = document.getElementById('view-solution');
    overlay.style.display = 'none';
    div.style.display = 'none';
  }


  document.querySelector('.achieve-btn').addEventListener('click', () => {
  document.getElementById('overlay').style.display = 'block';
  document.getElementById('view-achievements').style.display = 'flex';
      window.scrollTo({
      top: 0,
      behavior: 'smooth' 
    });
});

function closeAchievements() {
  document.getElementById('overlay').style.display = 'none';
  document.getElementById('view-achievements').style.display = 'none';
}

  
