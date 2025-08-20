let notifications = document.querySelector('.notification');

export function toastMsg(title='Success',msg='This is success message'){
  let type= title.toLowerCase();
  let icon;
  switch(type){
    case 'success':icon='fa-solid fa-circle-check';
      break;
     case 'warning':icon='fa-solid fa-triangle-exclamation';
      break;
     case 'Error':icon='fa-solid fa-circle-exclamation';
      break;
  }
  
  let text = msg;
  createToast(type , icon, title, text);
}


function createToast(type, icon, title, text){
  let notifiToast = document.createElement('div');
  notifiToast.innerHTML= `<div id="toast" class="${type}">
          <i class="${icon}"></i>
          <div id="toast-title">${title}
          <div id="toast-content">${text} </div>
          </div>
          <i class="fa-solid fa-xmark"></i>
      </div>`;

      notifications.appendChild(notifiToast);

      const closeBtn = notifiToast.querySelector('.fa-xmark');
      closeBtn.addEventListener('click', () => notifiToast.remove());


      setTimeout(() => {
        notifiToast.remove();
      }, 5000);
}