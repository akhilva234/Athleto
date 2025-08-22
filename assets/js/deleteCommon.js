
let deleteEntryInitialized = false;

function deleteEvent(){
    
    if (deleteEntryInitialized) return;
    deleteEntryInitialized = true;

    const tableBody = document.querySelector('.events-table tbody');
    if(!tableBody) return;

    tableBody.addEventListener('click',function(e){
        if(e.target.classList.contains('delete-btn')){
            const btn = e.target;

            const eventId=btn.getAttribute('data-event-id');
            const row = document.getElementById(`row-${eventId}`);

            if (!row) return;

            if(confirm("Are you sure want to delete this event?")){
                fetch(`delete_event.php?event_id=${eventId}`)
                .then(res=> res.json())
                .then(data =>{
                    if (data.success) {
                         row.style.transition = "opacity 0.5s ease";
                        row.style.opacity = 0;  
                         setTimeout(() => {
                                row.remove();
                            }, 500);                   
                    }else{
                          alert("Failed to delete. Try again.");
                    }
                }).catch(() => alert('Error loading data'));
            }
        }

    });
}
function deleteDept(){
    
    if (deleteEntryInitialized) return;
    deleteEntryInitialized = true;

    const tableBody = document.querySelector('.departments-table tbody');
    if(!tableBody) return;

    tableBody.addEventListener('click',function(e){
        if(e.target.classList.contains('delete-btn')){
            const btn = e.target;

            const deptId=btn.getAttribute('data-dept-id');
            const row = document.getElementById(`row-${deptId}`);

            if (!row) return;

            if(confirm("Are you sure want to delete this department?")){
                fetch(`delete_dept.php?dept_id=${deptId}`)
                .then(res=> res.json())
                .then(data =>{
                    if (data.success) {
                         row.style.transition = "opacity 0.5s ease";
                        row.style.opacity = 0;  
                         setTimeout(() => {
                                row.remove();
                            }, 500);                   
                    }else{
                          alert("Failed to delete. Try again.");
                    }
                }).catch(() => alert('Error loading data'));
            }
        }

    });
}


if(view=='events'){
    console.log(view);
    deleteEvent();
}
if(view==='departments'){
     console.log(view);
    deleteDept();
}