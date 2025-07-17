  function showCode(button){
  document.getElementById('ques-title').textContent = button.dataset.title; 
  document.getElementById('ques-cat').textContent = button.dataset.category; 
  document.getElementById('ques-topic').textContent = button.dataset.name; 
  document.getElementById('ques-diff').textContent = button.dataset.difficulty; 
  document.getElementById('ques-des').textContent = button.dataset.description; 
  document.getElementById('ques-solution').textContent = button.dataset.solution; 
  document.getElementById('ques-hint').textContent = button.dataset.hint; 
  
  
  const id = button.dataset.id;

  fetch(`functions/get_testcase.php?qid=${id}`)
  .then(res => res.json())
  .then(data=>{
    let html ='';
    if(data.length === 0){
      html = '<p>No test case available</p>';
    }else{
      data.forEach((tc,index)=>{
        html+= `<div><strong> Test Case ${index+1}</strong><br>  Input: <code>${tc.input}</code><br>  Output: <code>${tc.output}</code><br>
                </div>`;
      });
    }

    document.querySelector('.ques-testcase p').innerHTML = html;
  })

    document.getElementById('code-overlay').style.display = 'block';
    document.getElementById('code-dis').style.display = 'flex';

}

function closeCode(){
  document.getElementById('code-overlay').style.display = 'none';
  document.getElementById('code-dis').style.display = 'none'; 
}