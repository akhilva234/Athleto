import { resultEntry } from "./infoFetch.js";
import { deleteEntry } from "./delete.js";

export function renderParticipantsTable(data) {
    const tableBody = document.querySelector('.participants-table tbody');
    tableBody.innerHTML = '';

    if (!data || data.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="9">No athletes found</td></tr>';
        return;
    }

    let count = 1;
    data.forEach(athlete => {
        const row = document.createElement('tr');
        row.id = `row-${athlete.athlete_id}-${athlete.event_id}`; 
        row.innerHTML = `
            <td>${count++}</td>
            <td><span class="chest-no-tr">${athlete.athlete_id}</span></td>
            <td>${athlete.first_name} ${athlete.last_name}</td>
            <td>${athlete.category_name}</td>
            <td>${athlete.event_name}</td>
            <td>${athlete.dept_name}</td>
            <td>${athlete.year}</td>
            <td><button class="result-entry-btn" data-athlete-id="${athlete.athlete_id}" data-event-id="${athlete.event_id}">
                    Enter Result</button></td>
            <td><button class="delete-btn" data-athlete-id="${athlete.athlete_id}" data-event-id="${athlete.event_id}">
                    Delete</button></td>
        `;
        tableBody.appendChild(row);
    });

    resultEntry();
    deleteEntry();

}

export function renderAthletesTable(data){

    
}