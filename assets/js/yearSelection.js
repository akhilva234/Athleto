
document.getElementById('degree-select').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const degreeName = selectedOption.getAttribute('data-name');
    const yearSelect = document.getElementById('year-select');

    // Reset year options
    yearSelect.innerHTML = '<option value="">-- Select Year --</option>';

    // UG → 4 years, PG → 2 years
    if (degreeName === 'undergraduate') {
        for (let i = 1; i <= 4; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = i;
            yearSelect.appendChild(option);
        }
    } else if (degreeName === 'postgraduate') {
        for (let i = 1; i <= 2; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = i;
            yearSelect.appendChild(option);
        }
    } else {
        yearSelect.innerHTML = '<option value="">-- Select Year --</option>';
    }
});
