export function setupCheckboxLimit() {
  const individualEvents = document.querySelectorAll(".individual-event");

  function checkBoxUpdate() {
    const checkedCount = Array.from(individualEvents).filter(cb => cb.checked).length;
    console.log("Checked count:", checkedCount);

    individualEvents.forEach(cb => {
      cb.disabled = !cb.checked && checkedCount >= 3;
    });
  }

  individualEvents.forEach(cb => {
    cb.addEventListener("change", checkBoxUpdate);
  });

  checkBoxUpdate();
}

export function relayCatCheck(){

   console.log("relayCatCheck called!");

    const categorySelect=document.getElementById('category-check');
    const relayCheckBoxes=document.querySelectorAll('.relay-events');

    function updateRelayCheckboes(){

        const selectedCategory=parseInt(categorySelect.value);
        relayCheckBoxes.forEach(cb=>{
            const eventId=cb.dataset.eventId;
            const allowedCats = allowedCategoriesByEvent[eventId] || [];
            console.log(allowedCats);

            if(allowedCats.includes(selectedCategory)){
                cb.disabled=false;
            }else if(!cb.checked){
                cb.disabled=true;
            }
        });

    }

    updateRelayCheckboes();
    categorySelect.addEventListener("change",updateRelayCheckboes);
}