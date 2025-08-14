export function relayResultEntry() {
    const form = document.querySelector('.modal');
    const blurContainer = document.querySelector('.whole-blur-container');
    const closeButton = document.querySelector('.cancel-btn');
    const teamId = document.querySelector('.team-id');
    const eventId = document.querySelector('.event-id');
     const table = document.querySelector('.participants-table tbody');

     if (!form || !blurContainer || !closeButton || !teamId || !eventId || !table) return;

    table.addEventListener('click', function (e) {
        if (e.target.classList.contains('result-entry-btn')) {
             console.log("function called..");
            const btn = e.target;
            teamId.value = btn.getAttribute('data-team-id');
            eventId.value = btn.getAttribute('data-event-id');

            fetch(`../common_pages/relayInfoFetch.php?team_id=${teamId.value}&event_id=${eventId.value}`)
                .then(res => res.json())
                .then(data => {
                    document.querySelector('.team-name').value = data.team_name;
                    document.querySelector('.event-name').value = data.event_name;
                    form.style.display = 'grid';
                    blurContainer.style.display = 'block';
                })
                .catch(err => alert('Error loading data'));
        }
    });

    closeButton.addEventListener('click', () => {
        form.style.display = 'none';
        blurContainer.style.display = 'none';
    });
}
relayResultEntry();
