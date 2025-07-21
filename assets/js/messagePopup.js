document.addEventListener('DOMContentLoaded',()=>{

    const msg=document.querySelector('.success-msg');
    const blurOverlay=document.querySelector('.whole-blur-container');

    if(msg){

        setTimeout(()=>{
            blurOverlay.style.display='block';
        },10);
        setTimeout(()=>{
            blurOverlay.style.display='none';
            msg.style.display='none';
            msg.innerHTML='';
        },3000);
    }
});