import { toastMsg } from "../utils/toast.js";

document.addEventListener("DOMContentLoaded",()=>{
  if(window.toastMsgData){
    const {title,msg,role} = window.toastMsgData;

    toastMsg(title, msg);

   if(title.toLowerCase()== 'success'){
      if(role == 'user'){
        window.location.href = '../user/dashboard.php';
      }else if(role == 'admin'){
        window.location.href = '../admin/dashboard.php';
      }

    }
  }
});