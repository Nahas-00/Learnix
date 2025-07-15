  function showCode(button){
  document.getElementById('code-overlay').style.display = 'block';
  document.getElementById('code-dis').style.display = 'flex';
  document.getElementById('code-area').textContent = button.dataset.code;
}

function closeCode(){
  document.getElementById('code-overlay').style.display = 'none';
  document.getElementById('code-dis').style.display = 'none'; 
}