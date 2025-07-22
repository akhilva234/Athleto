import { loadAthletes } from "./filter.js";

let deleteEntryInitialized = false;
export function deleteEntry() {

     if (deleteEntryInitialized) return;
    deleteEntryInitialized = true;
    const athlete = document.querySelector('.athlete-id');
    const event = document.querySelector('.event-id');

    document.querySelector('.participants-table tbody').addEventListener('click', function (e) {
        if (e.target.classList.contains('delete-btn')) {
            const btn = e.target;

            athlete.value = btn.getAttribute('data-athlete-id');
            event.value = btn.getAttribute('data-event-id');
            const row = document.getElementById(`row-${athlete.value}-${event.value}`);

            if (confirm("Are you sure you want to delete this participation?")) {
                fetch(`delete_participation.php?athlete_id=${athlete.value}&event_id=${event.value}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            if (row) {
                                row.style.transition = "opacity 0.5s ease";
                                row.style.opacity = 0;
                                setTimeout(() => {
                                    row.remove();
                                }
                               ,500);
                                loadAthletes();
                            }
                        } else {
                            alert("Failed to delete. Try again.");
                        }
                    })
                    .catch(err => alert('Error loading data'));
            }
        }
    });
}

deleteEntry();
