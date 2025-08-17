function showBoard(type){
  document.getElementById('weekly-leaderboard').style.display =
   (type === 'weekly') ? 'block' : 'none';

  document.getElementById('alltime-leaderboard').style.display =
   (type === 'alltime') ? 'block' : 'none';
}

function setActive(button){
  document.querySelectorAll('.toggle-btn').forEach(btn=>{
    btn.classList.remove('active');
  });

  button.classList.add('active');
}