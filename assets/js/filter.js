import { renderParticipantsTable,renderAthletesTable,renderResultsTable,renderRelayTable,relayResultsTable } from "./rendertable.js";

const currentView = document.body.dataset.view || 'participants';
const currenUser=document.body.dataset.user;
document.querySelectorAll('.dropdown-checkbox').forEach(dropdown => {
    const button = dropdown.querySelector('.dropdown-btn');
    const searchInput = dropdown.querySelector('.dropdown-search');
    const labels = dropdown.querySelectorAll('label');

    button.addEventListener('click', function (e) {
        e.stopPropagation();
        closeAllDropdowns();
        dropdown.classList.toggle('show');
    });

    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const query = this.value.toLowerCase();
            labels.forEach(label => {
                const text = label.textContent.toLowerCase();
                label.style.display = text.includes(query) ? 'block' : 'none';
            });
        });

        searchInput.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    }
});

window.addEventListener('click', function (e) {
    if (!e.target.closest('.dropdown-checkbox')) {
        closeAllDropdowns();
    }
});

function closeAllDropdowns() {
    document.querySelectorAll('.dropdown-checkbox').forEach(dd => dd.classList.remove('show'));
}

function getCheckedValues(classname) {
    return Array.from(document.querySelectorAll('.' + classname + ':checked'))
        .map(cb => cb.value);
}


export async function loadAthletes() {
        const chestNoInput = document.querySelector('.search-box input');
        const chestNo = chestNoInput ? chestNoInput.value.trim() : '';

        const params = new URLSearchParams({
            dept: getCheckedValues('dept-checkbox').join(','),
            event: getCheckedValues('event-checkbox').join(','),
            category: getCheckedValues('cat-checkbox').join(','),
            year: getCheckedValues('year-checkbox').join(','),
            chest_no: chestNo,
            view:currentView
        });

    const response = await fetch('../common_pages/filter_athletes.php?' + params.toString());
    const data = await response.json();

    const response2=await fetch('../common_pages/filter_results.php?'+params.toString());

    console.log(currentView);
    if(currentView==='participants'){
        renderParticipantsTable(data,currenUser);
        
    }
    else if(currentView==='athletes'){
        renderAthletesTable(data,currenUser);
    }
    else if(currentView==='relays'){
        renderRelayTable(data,currenUser);
    }
    else if(currentView==='results'){
        const data2= await response2.json();
        renderResultsTable(data2,currenUser);
    }
    else if(currentView==='relayResults'){
        const data2= await response2.json();
        console.log("relay results");
        relayResultsTable(data2,currenUser);
    }
    
}
const chestNoInput = document.querySelector('.search-box input');
if (chestNoInput) {
    chestNoInput.addEventListener('input', loadAthletes);
}


document.querySelectorAll('.dropdown-checkbox input[type="checkbox"]').forEach(cb => {
    cb.addEventListener('change', loadAthletes);
});

// Auto-load filtered data on page load
window.addEventListener('DOMContentLoaded', () => {
    loadAthletes();
});


/*function renderTable(data) {
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
*/

