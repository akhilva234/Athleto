document.addEventListener('DOMContentLoaded',()=>{

    const athlete=document.querySelector('.athlete-id');
    const event=document.querySelector('.event-id');
   
    document.querySelectorAll('.delete-btn').forEach(btn=>{

        btn.addEventListener('click',()=>{

            athlete.value=btn.getAttribute('data-athlete-id');
            event.value=btn.getAttribute('data-event-id');
             const row = document.getElementById(`row-${athlete.value}-${event.value}`);

            if (confirm("Are you sure you want to delete this participation?")){
                
                 fetch(`delete_participation.php?athlete_id=${athlete.value}&event_id=${event.value}`)
            .then(res=>res.json())
            .then(data=>{
                if(data.success){
                     row.style.transition = "opacity 0.5s ease";
                        row.style.opacity = 0;
                        setTimeout(() => row.remove(), 500);
                }else {
                        alert("Failed to delete. Try again.");
                    }
            })
             .catch(err => alert('Error loading data'));
            }
        });
    });
});