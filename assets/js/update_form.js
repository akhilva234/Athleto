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
                  const modalContent = document.getElementById('editAthleteContent');

                 const scripts = modalContent.querySelectorAll("script");
                scripts.forEach((oldScript) => {
                    const newScript = document.createElement("script");
                    if (oldScript.src) {
                    newScript.src = oldScript.src;
                    } else {
                    newScript.textContent = oldScript.textContent;
                    }
                    document.body.appendChild(newScript);
                    oldScript.remove();
                });

                setupCheckboxLimit();
                relayCatCheck();
            })
            .catch(err => console.error('Error loading athlete data:', err));
        }

    });
}

updateWhole();