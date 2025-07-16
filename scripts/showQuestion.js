  function showCode(button){
  document.getElementById('code-overlay').style.display = 'block';
  document.getElementById('code-dis').style.display = 'flex';
    document.getElementById('ques-title').textContent = button.dataset.title; 
  document.getElementById('ques-cat').textContent = button.dataset.category; 
  document.getElementById('ques-topic').textContent = button.dataset.name; 
  document.getElementById('ques-diff').textContent = button.dataset.difficulty; 
  document.getElementById('ques-des').textContent = button.dataset.description; 
  document.getElementById('ques-solution').textContent = button.dataset.solution; 
  document.getElementById('ques-hint').textContent = button.dataset.hint; 
    document.getElementById('ques-test-case').textContent = button.dataset.input + button.dataset.output;
}

function closeCode(){
  document.getElementById('code-overlay').style.display = 'none';
  document.getElementById('code-dis').style.display = 'none'; 
}