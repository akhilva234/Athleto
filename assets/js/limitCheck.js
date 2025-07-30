export function setupCheckboxLimit() {
  const individualEvents = document.querySelectorAll(".individual-event");

  function checkBoxUpdate() {
    const checkedCount = Array.from(individualEvents).filter(cb => cb.checked).length;
    console.log("Checked count:", checkedCount);

    individualEvents.forEach(cb => {
      cb.disabled = !cb.checked && checkedCount >= 3;
    });
  }

  individualEvents.forEach(cb => {
    cb.addEventListener("change", checkBoxUpdate);
  });

  // Initial state check
  checkBoxUpdate();
}

export function relayCatCheck() {
  //console.log("relayCatCheck called!");

  const categorySelect = document.getElementById("category-check");

  if (!categorySelect) {
    console.warn("Category select element not found!");
    return;
  }

  const relayCheckBoxes = document.querySelectorAll("input.relay-events");

  function updateRelayCheckboxes() {
    const selectedCategory = parseInt(categorySelect.value);
    if (isNaN(selectedCategory)) {
     // console.warn("Selected category is not a valid number.");
      return;
    }

    relayCheckBoxes.forEach(cb => {
      const eventId = cb.dataset.eventId;

      if (!eventId) {
       // console.warn("Missing data-event-id for checkbox", cb);
        return;
      }

      const allowedCats = window.allowedCategoriesByEvent?.[eventId] || [];
      const isAllowed = allowedCats.includes(selectedCategory.toString()) || allowedCats.includes(selectedCategory); // Ensure both int & string match

      if (isAllowed) {
        cb.disabled = false;
      } else if (!cb.checked) {
        cb.disabled = true;
      }

      //console.log(`Event ${eventId}:`, allowedCats, "| Selected:", selectedCategory, "| Allowed:", isAllowed);
    });
  }

  // Attach listener and call once initially
  categorySelect.addEventListener("change", updateRelayCheckboxes);
  updateRelayCheckboxes();
}
