function userfetch(){
    document.querySelector('.users-table tbody').addEventListener('click',function(e){
        if(e.target.classList.contains('update-btn')){
            const btn=e.target;
            const user=btn.dataset.userId;

            fetch(`../admin/userinfo.php?user_id=${user}`)
            .then(res=>res.json())
            .then(data=>{
                
            })
            
        }
    })
}