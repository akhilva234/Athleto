import { resultEntry } from "./infoFetch.js";
import { deleteEntry } from "./delete.js";
import { deleteWhole } from "./delete_whole.js";
import { relayResultEntry } from "./relayInfoFetch.js";

async function getCourseYearAtMeet(currentCourseYear, meetYear,athlete) {
   const response = await fetch(`../common_pages/getMaxYear.php?athleteId=${athlete}`);
   const data=await response.json();
   let maxMeetYear=data.max_meet_year;

    const difference=maxMeetYear-meetYear;
    const Year=currentCourseYear-difference;
    return Year;
}



export async function renderParticipantsTable(data,results, currenUser) {
    const tableBody = document.querySelector('.participants-table tbody');
    const tableHead = document.querySelector('.participants-table thead');
    const currentYear = new Date().getFullYear();
    tableBody.innerHTML = '';

    if (!data || data.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="8" style="text-align:center;">No athletes found</td></tr>';
        tableHead.innerHTML = '';
        return;
    }

    // Build table header
    let headerHTML = `<tr>
        <th>SI.NO</th>
        <th>Chest Number</th>
        <th>Name</th>
        <th>Category</th>
        <th>Event</th>
        <th>Course</th>
        <th>Year</th>`;

    if (currenUser !== 'captain') {
        const hasActiveMeet = data.some(a => a.meet_year >= currentYear);
        if (hasActiveMeet) {
            headerHTML += `<th class="print-exclude">Result Entry</th>`;
        }
    }
    headerHTML += `</tr>`;
    tableHead.innerHTML = headerHTML;

    // Build table body
    let count = 1;
    for (const athlete of data) {
        const row = document.createElement('tr');
        row.id = `row-${athlete.athlete_id}-${athlete.event_id}`;

        const courseYear = await getCourseYearAtMeet(athlete.year, athlete.meet_year, athlete.athlete_id);

        const resultArray = Array.isArray(results) ? results : [];

         const resultExists = resultArray.some(r =>r.athlete_id === athlete.athlete_id && r.event_id === athlete.event_id);

        let rowHTML = `
            <td>${count++}</td>
            <td><span class="chest-no-tr">${athlete.athlete_id}</span></td>
            <td>${athlete.first_name} ${athlete.last_name}</td>
            <td>${athlete.category_name}</td>
            <td>${athlete.event_name}</td>
            <td>${athlete.dept_name}</td>
            <td>${courseYear}</td>`;

        // Show button only for non-captains and current/future meet year
        if (currenUser !== 'captain' && athlete.meet_year >= currentYear) {
             const disabled = resultExists ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : '';
            rowHTML += `<td class="print-exclude">
                <button class="result-entry-btn" data-athlete-id="${athlete.athlete_id}" data-event-id="${athlete.event_id}"  ${disabled}>
                    Enter Result
                </button>
            </td>`;
        }

        row.innerHTML = rowHTML;
        tableBody.appendChild(row);
    }

    // Initialize button events
    resultEntry();
}

export async function renderAthletesTable(data, currentUser) {
    const table = document.querySelector('.athletes-table');
    if (!table) return;

    const tableHead = table.querySelector('thead');
    const tableBody = table.querySelector('tbody');
    if (!tableBody || !tableHead) return;

    console.log(currentUser);

    tableBody.innerHTML = '';
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

    // Build table header dynamically
    let headerHTML = `
        <tr>
            <th>SI.NO</th>
            <th>Chest No</th>
            <th>Name</th>
            <th>Category</th>
            <th>Course</th>
            <th>Year</th>`;
     if (currentUser !== 'captain') headerHTML += ` <th>Update</th>
     <th>Delete</th>`;

    headerHTML += `</tr>`;
    tableHead.innerHTML = headerHTML;

    // Build table body
    let count = 1;
    uniqueAthletes.forEach(async athlete => {
        const row = document.createElement('tr');
        row.id = `row-${athlete.athlete_id}`;

        const courseYear = await getCourseYearAtMeet(athlete.year, athlete.meet_year,athlete.athlete_id);

        row.innerHTML = `
            <td>${count++}</td>
            <td><span class="chest-no-tr">${athlete.athlete_id}</span></td>
            <td>${athlete.first_name} ${athlete.last_name}</td>
            <td>${athlete.category_name}</td>
            <td>${athlete.dept_name}</td>
            <td>${courseYear}</td>`
            
         if (currentUser !== 'captain') {
                row.innerHTML += `
                <td><button class="update-btn" data-athlete-id="${athlete.athlete_id}">Update</button></td>
                <td><button class="delete-btn" data-athlete-id="${athlete.athlete_id}">Delete</button></td>`;
            }

        tableBody.appendChild(row);
    });

    if (document.querySelector('.athletes-table')) {
        deleteWhole();
    }
}

export async function renderResultsTable(data, currenUser) {
    const tableBody = document.querySelector('.result-table tbody');
    const tableHead = document.querySelector('.result-table thead');
    const currentYear = new Date().getFullYear();
    tableBody.innerHTML = '';

    if (!data || data.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="10">No results found</td></tr>';
        tableHead.innerHTML = '';
        return;
    }

    // Build table header
    let headerHTML = `<tr>
        <th>SI.NO</th>
        <th>Chest Number</th>
        <th>Name</th>
        <th>Category</th>
        <th>Event</th>
        <th>Position</th>
        <th>Course</th>
        <th>Year</th>
        <th class="print-exclude">Verified by</th>
        <th class="print-exclude">Time</th>`;

    if (currenUser !== 'captain') {
        // Only add Certificate column if meet year is current or future
        const hasCertificateColumn = data.some(a => a.meet_year >= currentYear);
        if (hasCertificateColumn) headerHTML += `<th class="print-exclude">Certificate</th>`;
    }

    headerHTML += `</tr>`;
    tableHead.innerHTML = headerHTML;

    // Build table body
    let count = 1;
    for (const athlete of data) {
        const row = document.createElement('tr');
        row.id = `row-${athlete.athlete_id}-${athlete.event_id}`;

        const courseYear = await getCourseYearAtMeet(athlete.year, athlete.meet_year, athlete.athlete_id);

        let rowHTML = `
            <td>${count++}</td>
            <td><span class="chest-no-tr">${athlete.athlete_id}</span></td>
            <td>${athlete.first_name} ${athlete.last_name}</td>
            <td>${athlete.category_name}</td>
            <td>${athlete.event_name}</td>
            <td>${athlete.position}</td>
            <td>${athlete.dept_name}</td>
            <td>${courseYear}</td>
            <td class="print-exclude">${athlete.username}</td>
            <td class="print-exclude">${athlete.recorded_at}</td>`;

        // Add certificate download button only if meet year >= current year
        if (currenUser !== 'captain' && athlete.meet_year >= currentYear) {
            rowHTML += `<td class="print-exclude">
                <button class="result-entry-btn dwnld-btn" data-result-id="${athlete.result_id}" data-athlete-id="${athlete.athlete_id}">
                    Download
                </button>
            </td>`;
        }

        row.innerHTML = rowHTML;
        tableBody.appendChild(row);
    }
}


export function renderRelayTable(data, results, currenUser) {
    const tableBody = document.querySelector('.participants-table tbody');
    const tableHead = document.querySelector('.participants-table thead');
    const currentYear = new Date().getFullYear();
    tableBody.innerHTML = '';

    if (!data || data.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="7" style="text-align:center;">No athletes found</td></tr>';
        tableHead.innerHTML = '';
        return;
    }

    // Build table header
    let headerHTML = `<tr>
        <th>SI No.</th>
        <th>Team Id</th>
        <th>Event Name</th>
        <th>Category</th>
        <th>Course</th>
        <th>Team Members</th>`;

    if (currenUser !== 'captain') {
        // Only show Result column if any meet is current or future
        const hasActiveMeet = data.some(a => a.meet_year >= currentYear);
        if (hasActiveMeet) headerHTML += `<th class="print-exclude">Result</th>`;
    }

    headerHTML += `</tr>`;
    tableHead.innerHTML = headerHTML;

    // Build table body
    let count = 1;
    data.forEach(team => {
        const row = document.createElement('tr');
        row.id = `row-${team.team_id}-${team.event_id}`;
         const resultArray = Array.isArray(results) ? results : [];

        const resultExists = resultArray.some(r => r.team_id === team.team_id && r.event_id === team.event_id);
        console.log(resultExists);

        let rowHTML = `
            <td>${count++}</td>
            <td><span class="chest-no-tr">${team.team_id}</span></td>
            <td>${team.event_name}</td>
            <td>${team.category_name}</td>
            <td>${team.dept_name}</td>
            <td>${team.team_members}</td>`;

        // Add Result Entry button only if non-captain and meet year >= current year
        if (currenUser !== 'captain' && team.meet_year >= currentYear) {
            const disabled = resultExists ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : '';
            rowHTML += `<td class="print-exclude">
                <button class="result-entry-btn" ${disabled}
                    data-team-id="${team.team_id}" data-event-id="${team.event_id}">
                    Enter Result
                </button>
            </td>`;
        }

        row.innerHTML = rowHTML;
        tableBody.appendChild(row);
    });

    relayResultEntry();
}


export async function relayResultsTable(data, currenUser) {
    const tableBody = document.querySelector('.result-table tbody');
    const tableHead = document.querySelector('.result-table thead');
    const currentYear = new Date().getFullYear();
    tableBody.innerHTML = '';

    if (!data || data.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="10">No results found</td></tr>';
        tableHead.innerHTML = '';
        return;
    }

    // Build table header
    let headerHTML = `<tr>
        <th>SI.NO</th>
        <th>Chest Number</th>
        <th>Athletes</th>
        <th>Category</th>
        <th>Event</th>
        <th>Position</th>
        <th>Course</th>
        <th class="print-exclude">Verified by</th>
        <th class="print-exclude">Time</th>`;

    if (currenUser !== 'captain') {
        const hasCertificateColumn = data.some(a => a.meet_year >= currentYear);
        if (hasCertificateColumn) {
            headerHTML += `<th class="print-exclude">Certificate</th>`;
        }
    }

    headerHTML += `</tr>`;
    tableHead.innerHTML = headerHTML;

    // Build table body
    let count = 1;
    for (const athlete of data) {
        const row = document.createElement('tr');
        row.id = `row-${athlete.team_id}-${athlete.event_id}`;

        const courseYear = await getCourseYearAtMeet(athlete.year, athlete.meet_year, athlete.athlete_id);
         

        let rowHTML = `
            <td>${count++}</td>
            <td><span class="chest-no-tr">${athlete.team_id}</span></td>
            <td>${athlete.team_members}</td>
            <td>${athlete.category_name}</td>
            <td>${athlete.event_name}</td>
            <td>${athlete.position}</td>
            <td>${athlete.dept_name}</td>
            <td class="print-exclude">${athlete.username}</td>
            <td class="print-exclude">${athlete.recorded_at}</td>`;

        // Certificate download button only if meet year >= current year
        if (currenUser !== 'captain' && athlete.meet_year >= currentYear) {
            rowHTML += `<td class="print-exclude">
                <button class="result-entry-btn team-btn" data-result-id="${athlete.result_id}" data-team-id="${athlete.team_id}">
                    Download
                </button>
            </td>`;
        }

        row.innerHTML = rowHTML;
        tableBody.appendChild(row);
    }
}
