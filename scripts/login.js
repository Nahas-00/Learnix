import { toastMsg } from "../utils/toast.js";

document.addEventListener("DOMContentLoaded",()=>{
  if(window.toastMsgData){
    const {title,msg,role} = window.toastMsgData;

    toastMsg(title, msg);

   if(title.toLowerCase()== 'success'){
      if(role == 'user'){
         setTimeout(() => {
              document.getElementById("redirect-overlay").style.display = "flex";

              setTimeout(() => {
                window.location.href = '../user/dashboard.php';
              }, 1500);
            }, 1000); 

      }else if(role == 'admin'){
           setTimeout(() => {
              document.getElementById("redirect-overlay").style.display = "flex";

              setTimeout(() => {
                window.location.href = '../admin/dashboard.php';
              }, 1500);
            }, 1000);
      }

    }
  }
});

