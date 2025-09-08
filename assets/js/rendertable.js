import { resultEntry } from "./infoFetch.js";
import { deleteEntry } from "./delete.js";
import { deleteWhole } from "./delete_whole.js";
import { relayResultEntry } from "./relayInfoFetch.js";

export function renderParticipantsTable(data,currenUser) {
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

    let rowHTML = `
        <td>${count++}</td>
        <td><span class="chest-no-tr">${athlete.athlete_id}</span></td>
        <td>${athlete.first_name} ${athlete.last_name}</td>
        <td>${athlete.category_name}</td>
        <td>${athlete.event_name}</td>
        <td>${athlete.dept_name}</td>
        <td>${athlete.year}</td>
    `;

    // Add the button column only if user is NOT a captain
    if (currenUser !== 'captain') {
        rowHTML += `
            <td>
                <button class="result-entry-btn" data-athlete-id="${athlete.athlete_id}" data-event-id="${athlete.event_id}">
                    Enter Result
                </button>
            </td>
        `;
    }

    row.innerHTML = rowHTML;
    tableBody.appendChild(row);
});


    resultEntry();
    //deleteEntry();

}
export function renderAthletesTable(data) {
    const tableBody = document.querySelector('.athletes-table tbody');
    if (!tableBody) return;

    tableBody.innerHTML = '';

    // Deduplicate by athlete_id
    const seen = new Set();
    const uniqueAthletes = data.filter(a => {
        if (seen.has(a.athlete_id)) return false;
        seen.add(a.athlete_id);
        return true;
    });

    if (uniqueAthletes.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="9">No athletes found</td></tr>';
        return;
    }

    let count = 1;
    uniqueAthletes.forEach(athlete => {
        const row = document.createElement('tr');
        row.id = `row-${athlete.athlete_id}`;
        row.innerHTML = `
            <td>${count++}</td>
            <td><span class="chest-no-tr">${athlete.athlete_id}</span></td>
            <td>${athlete.first_name} ${athlete.last_name}</td>
            <td>${athlete.category_name}</td>
            <td>${athlete.dept_name}</td>
            <td>${athlete.year}</td>
            <td><button class="update-btn" data-athlete-id="${athlete.athlete_id}">Update</button></td>
            <td><button class="delete-btn" data-athlete-id="${athlete.athlete_id}">Delete</button></td>
        `;
        tableBody.appendChild(row);
    });

    if (document.querySelector('.athletes-table')) {
        deleteWhole();
    }
}

export function renderResultsTable(data,currenUser){

     const tableBody = document.querySelector('.result-table tbody');
    tableBody.innerHTML = '';

    if (!data || data.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="9">No results found</td></tr>';
        return;
    }

    let count = 1;
    data.forEach(athlete => {
        const row = document.createElement('tr');
        row.id = `row-${athlete.athlete_id}-${athlete.event_id}`; 
        let rowHTML= `
            <td>${count++}</td>
            <td><span class="chest-no-tr">${athlete.athlete_id}</span></td>
            <td>${athlete.first_name} ${athlete.last_name}</td>
            <td>${athlete.category_name}</td>
            <td>${athlete.event_name}</td>
            <td>${athlete.position}</td>
            <td>${athlete.dept_name}</td>
            <td>${athlete.year}</td>
            <td>${athlete.username}</td>
            <td>${athlete.recorded_at}</td>`;

            if(currenUser!=='captain'){
                rowHTML+=`<td><button class="result-entry-btn dwnld-btn" data-result-id="${athlete.result_id}"
                      data-athlete-id="${athlete.athlete_id}">
                        Download</button></td> `;
            }
            row.innerHTML=rowHTML;
        tableBody.appendChild(row);
    });

}

export function renderRelayTable(data,currenUser){
     const tableBody = document.querySelector('.participants-table tbody');
    tableBody.innerHTML = '';

    if (!data || data.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="9">No athletes found</td></tr>';
        return;
    }

    let count = 1;
    data.forEach(athlete => {
        const row = document.createElement('tr');
        row.id = `row-${athlete.team_id}-${athlete.event_id}`; 
        let rowHTML= `
            <td>${count++}</td>
            <td><span class="chest-no-tr">${athlete.team_id}</span></td>
            <td>${athlete.event_name}</td>
            <td>${athlete.category_name}</td>
            <td>${athlete.dept_name}</td>
            <td>${athlete.team_members}</td>`;

              if(currenUser!=='captain'){
           rowHTML+=`<td><button class="result-entry-btn" data-team-id="${athlete.team_id}" data-event-id="${athlete.event_id}">
                    Enter Result</button></td>`;
    }
        row.innerHTML=rowHTML;
        tableBody.appendChild(row);
    });

    relayResultEntry();
}

export function relayResultsTable(data,currenUser){

     const tableBody = document.querySelector('.result-table tbody');
    tableBody.innerHTML = '';

    if (!data || data.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="9">No results found</td></tr>';
        return;
    }

    let count = 1;
    data.forEach(athlete => {
        const row = document.createElement('tr');
        row.id = `row-${athlete.team_id}-${athlete.event_id}`; 
        let rowHTML = `
            <td>${count++}</td>
            <td><span class="chest-no-tr">${athlete.team_id}</span></td>
            <td>${athlete.dept_name}</td>
             <td>${athlete.team_members}</td>
            <td>${athlete.category_name}</td>
            <td>${athlete.event_name}</td>
            <td>${athlete.position}</td>
            <td>${athlete.username}</td>
            <td>${athlete.recorded_at}</td>
            <td><button class="result-entry-btn team-btn" data-result-id="${athlete.result_id}"
                      data-team-id="${athlete.team_id}">
                        Download</button></td>
        `;
        tableBody.appendChild(row);
    });

}
