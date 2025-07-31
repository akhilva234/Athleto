document.addEventListener("DOMContentLoaded", () => {
    const searchInputs = document.querySelectorAll(".event-search");

    searchInputs.forEach(input => {
        const noEventsMessage = input.nextElementSibling;
        const eventBox = input.nextElementSibling.nextElementSibling;

        input.addEventListener("input", () => {
            const keyword = input.value.toLowerCase();
            const targetClass = input.dataset.target;
            let found = false;
            const checkboxes = document.querySelectorAll(`.${targetClass}`);

            checkboxes.forEach(cb => {
                const label = cb.closest("label");
                const text = label.textContent.toLowerCase();
                const isMatch = text.includes(keyword);
                label.style.display = isMatch ? "block" : "none";

                if (isMatch) found = true;
            });

            if (noEventsMessage) {
                noEventsMessage.style.display = found ? "none" : "block";
            }
              if (eventBox) {
                eventBox.style.display = found ? "block" : "none";
            }

            if (targetClass === "individual-event") {
                adjustScrollableHeight("#individual-events-box", "label");
            } else if (targetClass === "relay-events") {
                adjustScrollableHeight("#relay-events-box", "label");
            }
        });
    });
});

function adjustScrollableHeight(containerSelector, itemSelector) {
    const container = document.querySelector(containerSelector);
    const items = container.querySelectorAll(itemSelector);
    const visibleItems = Array.from(items).filter(item => item.offsetParent !== null);

    const itemHeight = items.length > 0 ? items[0].offsetHeight : 24;
    const padding = 10;
    const minHeight = 10;
    const maxHeight = 100;

    const contentHeight = visibleItems.length * itemHeight + padding;
    const newHeight = Math.max(Math.min(contentHeight, maxHeight), minHeight);

    container.style.maxHeight = `${newHeight}px`;

    if (visibleItems.length > 0) {
        visibleItems[0].scrollIntoView({ behavior: "smooth", block: "nearest" });
    }

    const messageBox = container.previousElementSibling;
    if (messageBox && messageBox.classList.contains('no-events-message')) {
        messageBox.style.display = visibleItems.length === 0 ? "block" : "none";
    }
}
