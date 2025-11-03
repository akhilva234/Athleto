document.addEventListener("DOMContentLoaded", () => {
    const dropdown = document.getElementById('yearDropdown');
    const button = dropdown.querySelector('.dropdown-btn');
    const radios = dropdown.querySelectorAll('.year-radio');
    const content = dropdown.querySelector('.dropdown-content');

    const championshipContainer = document.querySelector('body'); // tables are already in body

    // Toggle dropdown
    button.addEventListener('click', (e) => {
        e.stopPropagation();
        content.classList.toggle('show');
    });

    // Close dropdown when clicking outside
    window.addEventListener('click', () => content.classList.remove('show'));

    // Function to fetch and reload championship data
    async function loadChampionship(year) {
        try {
            const res = await fetch(`../common_pages/get_championship_filter.php?year=${year}`);
            const data = await res.json();

            // Team Championship
            const teamTable = document.querySelector('.department-table tbody');
            if (teamTable) {
                teamTable.innerHTML = '';
                if (data.deptChampions.length === 0) {
                    teamTable.innerHTML = `<tr>
                        <td colspan="3" style="text-align:center; font-weight:bold; color:#555;">No Team championship found.</td>
                    </tr>`;
                } else {
                    data.deptChampions.forEach((d, i) => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `<td>${i + 1}</td>
                                        <td>${d.dept_name}</td>
                                        <td>${d.total_points}</td>`;
                        teamTable.appendChild(tr);
                    });
                }
            }

            // Category Championship
            const maleTable = document.querySelectorAll('.category-table')[0].querySelector('tbody');
            const femaleTable = document.querySelectorAll('.category-table')[1].querySelector('tbody');

            function renderCategory(table, arr, emptyMsg) {
                table.innerHTML = '';
                if (arr.length === 0) {
                    table.innerHTML = `<tr>
                        <td colspan="5" style="text-align:center; font-weight:bold; color:#555;">${emptyMsg}</td>
                    </tr>`;
                } else {
                    arr.forEach((c, i) => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `<td>${i + 1}</td>
                                        <td>${c.category_name}</td>
                                        <td>${c.athlete_id}</td>
                                        <td>${c.first_name} ${c.last_name}</td>
                                        <td>${c.dept_name}</td>
                                        <td>${c.total_points}</td>`;
                        table.appendChild(tr);
                    });
                }
            }

            renderCategory(maleTable, data.maleChampions, 'No Men championship found.');
            renderCategory(femaleTable, data.femaleChampions, 'No Women championship found.');

            // Individual Championship
            const indivTable = document.querySelector('.athletes-table:not(.category-table):not(.department-table) tbody');
            indivTable.innerHTML = '';
            if (data.topAthlete.length === 0) {
                indivTable.innerHTML = `<tr>
                    <td colspan="5" style="text-align:center; font-weight:bold; color:#555;">No Individual championship found.</td>
                </tr>`;
            } else {
                data.topAthlete.forEach((a, i) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `<td>${i + 1}</td>
                                    <td>${a.athlete_id}</td>
                                    <td>${a.first_name} ${a.last_name}</td>
                                    <td>${a.dept_name}</td>
                                    <td>${a.total_points}</td>`;
                    indivTable.appendChild(tr);
                });
            }

        } catch (err) {
            console.error('Error loading championship data:', err);
        }
    }

    // Load default selected year on page load
    const defaultYear = document.querySelector('.year-radio:checked')?.value;
    if (defaultYear) loadChampionship(defaultYear);

    // Event listener for year change
    radios.forEach(radio => {
        radio.addEventListener('change', () => {
            loadChampionship(radio.value);
            content.classList.remove('show'); // close dropdown
        });
    });
});
