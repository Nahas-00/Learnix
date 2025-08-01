

const editor = CodeMirror.fromTextArea(document.getElementById('editor'), {
  lineNumbers: true,
  mode: 'text/x-c++src',
  theme: 'dracula',
  indentUnit: 4,
  tabSize: 4,
  indentWithTabs: false,
  smartIndent: true,
  lineWrapping: true,
  matchBrackets: true,
  autoCloseBrackets: true,
  showCursorWhenSelecting: true,
  styleActiveLine: true,
  highlightSelectionMatches: { showToken: /\w/, annotateScrollbar: true },
  extraKeys: {
    'Ctrl-Space': 'autocomplete',
    'Ctrl-/': 'toggleComment',
    'Tab': cm => {
      if (cm.somethingSelected()) {
        cm.indentSelection("add");
      } else {
        cm.replaceSelection("    ", "end");
      }
    },
    'Ctrl-Enter': runCode,
    'Cmd-Enter': runCode
  }
});

editor.setSize('100%', '100%');

const runBtn = document.getElementById('run-btn');
const clearBtn = document.getElementById('clear-btn');
const languageSelector = document.getElementById('language-selector');
const outputContent = document.getElementById('output-content');
const statusMemory = document.getElementById('status-memory');
const statusTime = document.getElementById('status-time');
const statusExit = document.getElementById('status-exit');
const loader = document.getElementById('loader');

const testcaseInput = document.getElementById('test-input');
const tabButtons = document.querySelectorAll('.tab-button');
const tabPanels = document.querySelectorAll('.tab-panel');
const outputTabButton = document.querySelector('[data-tab="output"]');
const testcaseStat = document.getElementById('testcase-status');
const testcaseOutput = document.getElementById('test-output').textContent.trim();

const languageModes = {
     '50' : 'text/x-csrc',
     '54' : 'text/x-c++src',
     '62': 'text/x-java',   
     '71': 'text/x-python',
     '60': 'go',    
     '72': 'ruby'
}


const languageTemplates = {
  '50': `#include <stdio.h>

int main() {
    // your code goes here
    return 0;
}`, // C

  '54': `#include <iostream>
using namespace std;

int main() {
    // your code goes here
    return 0;
}`, // C++

  '62': `public class Main {
    public static void main(String[] args) {
        // your code goes here
    }
}`, // Java

  '71': `def main():
    # your code goes here
    pass

if __name__ == "__main__":
    main()`, // Python

  '60': `package main
import "fmt"

func main() {
    // your code goes here
}`, // Go

  '72': `def main
  # your code goes here
end

main`, // Ruby
};



tabButtons.forEach(button => {
  button.addEventListener('click' , ()=>{
    tabButtons.forEach(btn => btn.classList.remove('active'));
    tabPanels.forEach(panel => panel.classList.remove('active'));

    button.classList.add('active');
    const tabName = button.getAttribute('data-tab');
    document.getElementById(`${tabName}-panel`).classList.add('active');
  });
});

function switchToOutputTab(){
  outputTabButton.click();
}

languageSelector.addEventListener('change', function(){
  const languageId = this.value;
  editor.setOption('mode', languageModes[languageId] || 'text/x-c++src');

  if (languageTemplates[languageId]) {
    editor.setValue(languageTemplates[languageId]);
  } else {
    editor.setValue('// Start coding here');
  }
});

runBtn.addEventListener('click' , runCode);

clearBtn.addEventListener('click', function() {
  outputContent.textContent = '// Output cleared';
  outputContent.className = 'output-content';
  statusMemory.textContent = 'Memory: -';
  statusTime.textContent = 'Time: -';
  statusExit.textContent = 'Exit Code: -';
  
});

// Helper for Base64 decoding
function decodeBase64(base64String) {
  try {
      // Use atob for decoding, handle null/undefined safely
      return atob(base64String || '');
  } catch (e) {
      // If decoding fails, return the original string
      return base64String;
  }
}

  let testStatus = 'fail'; 

//Runs to compile the code
async function runCode() {
  const sourceCode = editor.getValue();
  const languageId = languageSelector.value;

  outputContent.textContent = '// Executing all test cases...';
  outputContent.className = 'output-content';
  loader.style.display = 'block';
  testcaseStat.innerHTML = '';

  let allPassed = true;

  for (let i = 0; i < testcases.length; i++) {
    const tc = testcases[i];
    const input = tc.input.trim();
    const expectedOutput = tc.output.trim();

    const response = await fetch('https://judge0-ce.p.rapidapi.com/submissions?base64_encoded=true&wait=true', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-RapidAPI-Key': '246a785fafmshc35cc4536659135p1049a1jsnaf9893acd1ba',
        'X-RapidAPI-Host': 'judge0-ce.p.rapidapi.com'
      },
      body: JSON.stringify({
        source_code: btoa(sourceCode),
        language_id: parseInt(languageId),
        stdin: btoa(input),
        cpu_time_limit: 5,
        memory_limit: 128000
      })
    });

    const data = await response.json();
    const stdout = decodeBase64(data.stdout || '').trim();

    const pass = stdout === expectedOutput;
    allPassed = allPassed && pass;

    const statusHTML = `<div class="${pass ? 'test-success' : 'test-fail'} test-stat">
      <b>Test ${i + 1}:</b> ${pass ? '✅ Passed' : '❌ Failed'} <br>
      <b>Input:</b> ${input} <br>
      <b>Expected:</b> ${expectedOutput} <br>
      <b>Got:</b> ${stdout}
    </div>`;

    testcaseStat.innerHTML += statusHTML + '<br>';
  }

  loader.style.display = 'none';
  testStatus = allPassed ? 'Success' : 'Failed';
  outputContent.textContent = `🎯 All test cases executed.\nOverall Result: ${testStatus}`;
  outputContent.className = allPassed ? 'output-content success' : 'output-content error';
}


function processResponse(data){
  statusMemory.textContent = `Memory: ${data.memory ? (data.memory/1024).toFixed(2) + 'MB' : '-'}`;
  statusTime.textContent = `Time: ${data.time ? (data.time) + 's' : '-'}`;
  statusExit.textContent = `Exit Code: ${data.status ? (data.status.id) : '-'}`;

  const compileOutput = decodeBase64(data.compile_output);
  const stdErr = decodeBase64(data.stderr);
  const stdOut = decodeBase64(data.stdout);

  if(compileOutput){
    outputContent.textContent = `⚡ Compilation Error:\n${compileOutput}`;
    outputContent.className = 'output-content error'; 
  }else if (stdErr) {
    outputContent.textContent = `🔥 Runtime Error:\n${stdErr}`;
    outputContent.className = 'output-content error';
  } else if (data.message) {
      outputContent.textContent = `❗️ Message:\n${decodeBase64(data.message)}`;
      outputContent.className = 'output-content error';
  } else if (stdOut !== null && stdOut !== undefined) {
      outputContent.textContent = stdOut;
      outputContent.className = 'output-content success';
  } else {
      outputContent.textContent = '🤔 Unknown response from server.';
      outputContent.className = 'output-content error';
  }



 if (stdOut.trim() === testcaseOutput.trim()) {
  testStatus = 'Succcess'; 
  testcaseStat.innerHTML = `<p class="test-success test-stat"><i class="fas fa-check-circle"></i> Test case Passed</p>`;
} else {
  testcaseStat.innerHTML = `<p class="test-fail test-stat"><i class="fas fa-times-circle"></i> One or more Test case Failed</p>`;
  testStatus = 'Failed'; 
}

}

  const overlay = document.getElementById('overlay-disp');
  const hint = document.getElementById('hint-disp');

  function showHint(){
    overlay.style.display = 'block';
    hint.style.display = 'flex';
  }

  function closeHint(){
    overlay.style.display = 'none';
    hint.style.display = 'none';
  }

  const sol = document.getElementById('sol-disp');
  const chat = document.getElementById('ai-chat');

  function showSolution(){
    const status = confirm('Are you sure. Viewing solution makes the submit fail');
    if(status){
      overlay.style.display = 'block';
      sol.style.display = 'flex';
      document.getElementById('viewed_solution').value = "1";
    }
  }

  function closeSolution(){
    overlay.style.display = 'none';
    sol.style.display = 'none';
  }


  function openChat(){
    chat.style.display = 'flex';
    overlay.style.display = 'block';
  }

  function closeChat(){
    chat.style.display = 'none';
    overlay.style.display = 'none';
  }

 async function sendMessage() {
  const inputBox = document.getElementById("userInput");
  const userText = inputBox.value.trim();
  const messages = document.getElementById("messages");

  if (!userText) return;

  // Show user's message
  const userBubble = document.createElement("div");
  userBubble.className = "chat-bubble chat-user";
  userBubble.innerText = userText;
  messages.appendChild(userBubble);
  messages.scrollTop = messages.scrollHeight;
  inputBox.value = "";

  // Add AI thinking bubble (temporary)
  const aiBubble = document.createElement("div");
  aiBubble.className = "chat-bubble chat-ai";
  aiBubble.innerHTML = `<span style = "font-weight:bold;">^_^</span> Typing...`;
  messages.appendChild(aiBubble);
  messages.scrollTop = messages.scrollHeight;

  try {
    const res = await fetch("gemini_chat.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ prompt: userText }),
    });

    const data = await res.json();
    const reply = data.reply || "No response from AI.";

    // Typing effect
    aiBubble.innerText = "";
    let i = 0;
    const typeSpeed = 20; // milliseconds between characters

    function typeNextChar() {
      if (i < reply.length) {
        aiBubble.innerText += reply.charAt(i);
        i++;
        messages.scrollTop = messages.scrollHeight;
        setTimeout(typeNextChar, typeSpeed);
      }
    }

    typeNextChar();

  } catch (error) {
    aiBubble.innerText = "⚠️ Error: Could not reach the AI service.";
  }
}

//submit form

document.getElementById("submit-btn").addEventListener("click", async () => {
  const code = editor.getValue();
  const languageId = document.getElementById("language-selector").value;
  const questionId = document.getElementById('ques_id').value;
  const usedSolution = document.getElementById("viewed_solution").value;
  let languageName = 'c++';

  switch (languageId) {
   case '50':
      languageName = 'c';      // C
    case '54':
      languageName = 'C++'; 
      break;;    // C++
    case '62':
      languageName = 'Java'; 
      break;     // Java
    case '71':
     languageName = 'Python'; 
      break;;    // Python
    case '60':
      languageName = 'Golang'; 
      break;               // Golang
    case '72':
      languageName = 'Ruby'; 
      break;
    default:
      break;
  }

  const formData = new FormData();
  formData.append("code", code);
  formData.append("language_id", languageName);
  formData.append("question_id", questionId);
  formData.append("viewed_solution", usedSolution);
  formData.append("status", testStatus);

  const res = await fetch("submit.php", {
    method: "POST",
    body: formData,
  });

  const result = await res.json();
 showSubmissionNotice(result.message , 4000);
});




function showSubmissionNotice(message = "✅ Submission Successful!", timeout = 3000) {
  const notice = document.createElement("div");
  notice.textContent = message;



  // Inline styles
  Object.assign(notice.style, {
    position: "fixed",
    top: "0",
    left: "0",
    width: "100%",
    backgroundColor: "#16a34a", // green for success
    color: "white",
    textAlign: "center",
    padding: "0.8rem",
    fontSize: "1rem",
    fontFamily: "'Poppins', sans-serif",
    zIndex: 9999,
    boxShadow: "0 2px 6px rgba(0,0,0,0.2)",
    opacity: "0",
    transition: "opacity 0.3s ease"
  });

  document.body.appendChild(notice);

  // Fade in
  setTimeout(() => {
    notice.style.opacity = "1";
  }, 10);

  // Fade out and remove
  setTimeout(() => {
    notice.style.opacity = "0";
    setTimeout(() => document.body.removeChild(notice), 500);
  }, timeout);
}


