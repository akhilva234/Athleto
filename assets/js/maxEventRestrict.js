document.addEventListener('DOMContentLoaded', () => {
    const individualEvents = document.querySelectorAll(".individual-event");
    const categorySelect = document.getElementById("category-check");
    const relayCheckBoxes = document.querySelectorAll("input.relay-events");


     
    fetch(`../common_pages/add_auth.php`)
        .then(res => res.json())
        .then(data => {
            window.allowedCategoriesByEvent = data.allowedCategoriesByEvent;
            updateRelayCat();
        })
        .catch(err => console.error('Error loading athlete data:', err));
    
    function updateRelayCat() {
        const selectedValue = categorySelect.value;

        console.log(`selected value ${selectedValue}`);
        // If no category selected, enable ALL relay checkboxes
        /*if (!selectedValue.trim()) {
            relayCheckBoxes.forEach(cb => {
                cb.disabled = false;
            });
            return;
        }*/

        const selectedCategory = parseInt(selectedValue);

        relayCheckBoxes.forEach(cb => {
            const eventId = cb.dataset.eventId;

            // Convert to array of integers safely
            const raw = window.allowedCategoriesByEvent?.[eventId] || [];
            const allowedCats = Array.isArray(raw)
                ? raw.map(x => parseInt(x)) // normalize to numbers
                : raw.toString().split(',').map(x => parseInt(x.trim()));

            const isAllowed = allowedCats.includes(selectedCategory);

            console.log(`Event ${eventId}, allowed:`, allowedCats, "selected:", selectedCategory, "allowed?", isAllowed);

            // Allow if selected category is allowed or already checked
            cb.disabled = !isAllowed && !cb.checked;
        });
    }

    function updateEventCount() {
        const checkedCount = Array.from(individualEvents).filter(cb => cb.checked).length;

        individualEvents.forEach(cb => {
            cb.disabled = !cb.checked && checkedCount >= 3;
        });
    }

    // Event listeners
    individualEvents.forEach(cb => cb.addEventListener("change", updateEventCount));
    categorySelect.addEventListener("change", updateRelayCat);

    // Initial state
    updateEventCount();
});
