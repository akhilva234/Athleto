
document.addEventListener("DOMContentLoaded", function () {
    const individualCheckboxes = document.querySelectorAll(".individual-event");

    function updateCheckboxStates() {
        const checkedCount = Array.from(individualCheckboxes)
                                  .filter(cb => cb.checked).length;

        console.log(checkedCount);
        individualCheckboxes.forEach(cb => {
            // Disable only if not checked and 3 are already selected
            if (!cb.checked) {
                cb.disabled = checkedCount >= 3;
            } else {
                cb.disabled = false;
            }
        });
    }

    // Attach event listeners to each checkbox
    individualCheckboxes.forEach(cb => {
        cb.addEventListener("change", updateCheckboxStates);
    });

    // Call once on load in case 3 are already pre-checked
     updateCheckboxStates();
});

