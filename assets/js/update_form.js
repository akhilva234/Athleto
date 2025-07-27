import { setupCheckboxLimit,relayCatCheck} from "./limitCheck.js";

function updateWhole(){

    document.querySelector('.athletes-table tbody').addEventListener('click',function(e){
        if(e.target.classList.contains('update-btn')){

            const btn=e.target;
            const athlete=btn.dataset.athleteId;

            fetch(`../common_pages/fetch_whole_info.php?athleteid=${athlete}`)
            .then(res=>res.json())
            .then(data=>{
                document.getElementById('editAthleteContent').innerHTML = data.html;
                document.getElementById('editAthleteModal').style.display = 'block';
                window.allowedCategoriesByEvent = data.allowedCategoriesByEvent;
                setupCheckboxLimit();
                relayCatCheck();
            })
            .catch(err => console.error('Error loading athlete data:', err));
        }

    });
}

updateWhole();