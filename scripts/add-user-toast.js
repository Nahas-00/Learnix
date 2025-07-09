import { toastMsg } from "../utils/toast.js";


document.addEventListener("DOMContentLoaded" , ()=>{

  if(window.toastMsgData){
    const {title , msg} = window.toastMsgData;
    toastMsg(title, msg);

  }



});