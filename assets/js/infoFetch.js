document.addEventListener('DOMContentLoaded',function(){
    
    const form=document.querySelector('.modal');
    const blurContainer=document.querySelector('.whole-blur-container');
    const closeButton=document.querySelector('.cancel-btn');
    const athleteId=document.querySelector('.athlete-id');
    const eventId=document.querySelector('.event-id');

    document.querySelectorAll('.result-entry-btn').forEach(btn=>{

        btn.addEventListener('click',function(){

            athleteId.value=this.getAttribute('data-athlete-id');
            eventId.value=this.getAttribute('data-event-id');

            fetch(`fetch_names.php?athlete_id=${athleteId.value}&event_id=${eventId.value}`)
            .then(res=>res.json())
            .then(data =>{
                document.querySelector('.athlete-name').value=data.athlete_name;
                document.querySelector('.event-name').value=data.event_name;
                 form.style.display='grid';
                 blurContainer.style.display='block';
            })
            .catch(err => alert('Error loading data'));

           
        });
    });

    closeButton.addEventListener('click',()=>{
        form.style.display='none';
         blurContainer.style.display='none';
    });

});