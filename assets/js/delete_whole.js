
let deleteEntry=false;
export function deleteWhole(){

    if(deleteEntry) return;
    deleteEntry=true;

    const tableBody = document.querySelector('.participants-table tbody');
    if (!tableBody) return; // Page doesn't have participants table

    tableBody.addEventListener('click',function(e){
        if(e.target.classList.contains('delete-btn')){

            const btn=e.target;
            const athlete=btn.dataset.athleteId;
            const row = document.getElementById(`row-${athlete}`);
            //console.log(row);

           if(confirm("Are you sure want to delete this athlete.All the participation and result info will be deleted" )){

                fetch(`../common_pages/delete_athletes.php?athleteid=${athlete}`)
                .then(res => res.json())
                .then(data => {

                    if(data.success){
                        if(row){
                             row.style.transition = "opacity 0.5s ease";
                            row.style.opacity = 0;
                            setTimeout(() => {row.remove(); }
                               ,500);

                        }
                    }else{
                        alert("Failed to delete athletes.");
                    }
                })
                 .catch(err => alert('Error loading data'));
            }
        }

    });

}

deleteWhole();