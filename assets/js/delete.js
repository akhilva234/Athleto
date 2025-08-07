import { loadAthletes } from "./filter.js";

let deleteEntryInitialized = false;

export function deleteEntry() {
    if (deleteEntryInitialized) return;
    deleteEntryInitialized = true;

    const tableBody = document.querySelector('.participants-table tbody');
    if (!tableBody) return; // Page doesn't have participants table

    tableBody.addEventListener('click', function (e) {
        if (e.target.classList.contains('delete-btn')) {
            const btn = e.target;

            const athleteId = btn.getAttribute('data-athlete-id');
            const eventId = btn.getAttribute('data-event-id');

            const athleteInput = document.querySelector('.athlete-id');
            const eventInput = document.querySelector('.event-id');
            const row = document.getElementById(`row-${athleteId}-${eventId}`);

            if (!athleteInput || !eventInput || !row) return; // Page not ready for delete

            athleteInput.value = athleteId;
            eventInput.value = eventId;

            if (confirm("Are you sure you want to delete this participation?")) {
                fetch(`delete_participation.php?athlete_id=${athleteId}&event_id=${eventId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            row.style.transition = "opacity 0.5s ease";
                            row.style.opacity = 0;
                            setTimeout(() => {
                                row.remove();
                            }, 500);

                            loadAthletes();
                        } else {
                            alert("Failed to delete. Try again.");
                        }
                    })
                    .catch(() => alert('Error loading data'));
            }
        }
    });
}

deleteEntry();
