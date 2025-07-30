import { relayCatCheck,setupCheckboxLimit } from "./limitCheck.js";

document.addEventListener('DOMContentLoaded',()=>{

      const individualEvents = document.querySelectorAll(".individual-event");
     const categorySelect = document.getElementById("category-check");

    /* function updateRelatCat(){
         const selectedCategory = parseInt(categorySelect.value);
         
     }
        
            fetch(`../common_pages/add_auth.php?athleteid=${athlete}`)
            .then(res=>res.json())
            .then(data=>{
                window.allowedCategoriesByEvent = data.allowedCategoriesByEvent;
                setupCheckboxLimit();
            })
            .catch(err => console.error('Error loading athlete data:', err));  
*/
           function updateEventCount(){

            const checkedCount = Array.from(individualEvents).filter(cb => cb.checked).length;
             individualEvents.forEach(cb => {
            cb.disabled = !cb.checked && checkedCount >= 3;
            });
    } 
        individualEvents.forEach(cb => {
    cb.addEventListener("change", updateEventCount);
  });
  updateEventCount();
});



