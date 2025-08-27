let deleteEntryInitialized = false;

function deleteUser(){
    if (deleteEntryInitialized) return;
    deleteEntryInitialized = true;

    const tableBody = document.querySelector('.users-table tbody');
    if (!tableBody) return; 

    tableBody.addEventListener('click',function(e){
        if(e.target.classList.contains('delete-btn')){
            const btn=e.target
            const user=btn.dataset.userId
              const row = document.getElementById(`row-${user}`);

            if(confirm('Are you sure want to delete this user?')){
                fetch(`../admin/delete_user.php?user_id=${user}`)
                .then(res=>res.json())
                .then(data=>{
                    if(data.success){
                        row.style.transition = "opacity 0.5s ease";
                            row.style.opacity = 0;
                            setTimeout(() => {
                                row.remove();
                            }, 500);

                    }else{
                        alert("Failed to delete users. Try again");
                    }
                         
                })
                .catch(()=>"Error loading data")
            }
        }
    })
}

deleteUser();