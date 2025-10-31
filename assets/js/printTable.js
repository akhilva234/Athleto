// assets/js/printTable.js
document.addEventListener("DOMContentLoaded", function () {
    const printBtn = document.getElementById("print-btn");
    if (!printBtn) return;

    printBtn.addEventListener("click", function () {
        const tableContainer = document.querySelector(
            ".participants-table-container, .result-table-container"
        );

        if (!tableContainer || tableContainer.querySelectorAll("tbody tr").length === 0) {
            if (typeof toastr !== "undefined") {
                toastr.warning("No data available to print for the selected filter!");
            } else {
                alert("No data available to print for the selected filter!");
            }
            return;
        }

        // ====== Academic Year Logic (Auto Adjusts) ======
        const now = new Date();
        let startYear = now.getFullYear();
        let endYear = startYear + 1;

        // If before July (academic year logic)
        // if (now.getMonth() < 6) {
        //     startYear = startYear - 1;
        //     endYear = startYear + 1;
        // }

        const meetHeader = `MES College Marampally Athletic Meet`;

        // ====== Get Page Heading ======
        const pageHeading = document.querySelector("h2")?.innerText || "Athletic Meet Report";

        // ====== Collect Selected Filters (based on your PHP filter) ======
        const selectedDepartments = [...document.querySelectorAll(".dept-checkbox:checked")].map(
            (el) => el.parentElement.textContent.trim()
        );
        const selectedEvents = [...document.querySelectorAll(".event-checkbox:checked")].map(
            (el) => el.parentElement.textContent.trim()
        );
        const selectedCategories = [...document.querySelectorAll(".cat-checkbox:checked")].map(
            (el) => el.parentElement.textContent.trim()
        );
        const selectedYears = [...document.querySelectorAll(".year-checkbox:checked")].map(
            (el) => el.parentElement.textContent.trim()
        );
        const searchValue =
            document.querySelector(".search-box input")?.value.trim() || "All Chest Numbers";

        // ====== Build Subheader String ======
        let filterText = "";
        if (selectedDepartments.length)
            filterText += `<strong>Departments:</strong> ${selectedDepartments.join(", ")}<br>`;
        if (selectedEvents.length)
            filterText += `<strong>Events:</strong> ${selectedEvents.join(", ")}<br>`;
        if (selectedCategories.length)
            filterText += `<strong>Categories:</strong> ${selectedCategories.join(", ")}<br>`;
        if(selectedYears.length)
            filterText += `<strong>Years:</strong> ${selectedYears.join(", ")}<br>`;
        if (searchValue && searchValue !== "All Chest Numbers")
            filterText += `<strong>Chest No:</strong> ${searchValue}`;

        if (!filterText) {
            filterText = `<em>No specific filters applied</em>`;
        }

        // ====== Create Printable Header ======
        const headerDiv = document.createElement("div");
        headerDiv.id = "print-header";
        headerDiv.style.textAlign = "center";
        headerDiv.style.marginBottom = "15px";
        headerDiv.innerHTML = `
            <h2 style="margin:0; font-size:20px;">${meetHeader}</h2>
            <h3 style="margin:5px 0; font-weight:bold;">${pageHeading}</h3>
            <div style="margin-top:3px; font-size:14px; line-height:1.4;">${filterText}</div>
            <hr style="margin:10px 0;">
        `;

        // ====== Add header before table ======
        tableContainer.parentNode.insertBefore(headerDiv, tableContainer);

        // ====== Print Page ======
        window.print();

        // ====== Remove header after print ======
        headerDiv.remove();
    });

});

document.addEventListener("DOMContentLoaded", function () {
    const printBtn = document.getElementById("print-btn-championship");
    if (!printBtn) return;

    printBtn.addEventListener("click", function () {
        const tableContainer = document.querySelector(
            ".participants-table-container, .result-table-container"
        );

        if (!tableContainer || tableContainer.querySelectorAll("tbody tr").length === 0) {
            if (typeof toastr !== "undefined") {
                toastr.warning("No data available to print for the selected filter!");
            } else {
                alert("No data available to print for the selected filter!");
            }
            return;
        }

        // ====== Academic Year Logic (Auto Adjusts) ======
        const now = new Date();
        let startYear = now.getFullYear();
        let endYear = startYear + 1;

        // If before July (academic year logic)
        if (now.getMonth() < 6) {
            startYear = startYear - 1;
            endYear = startYear + 1;
        }

        const meetHeader = `MES College Marampally Athletic Meet (${startYear} - ${endYear})`;

        // ====== Get Page Heading ======
        const pageHeading = document.querySelector("h2")?.innerText || "Athletic Meet Report";

    
        // ====== Create Printable Header ======
        const headerDiv = document.createElement("div");
        headerDiv.id = "print-header";
        headerDiv.style.textAlign = "center";
        headerDiv.style.marginBottom = "15px";
        headerDiv.innerHTML = `
            <h2 style="margin:0; font-size:20px;">${meetHeader}</h2>
            <h3 style="margin:5px 0; font-weight:bold;">${pageHeading}</h3>
            <hr style="margin:10px 0;">
        `;

        // ====== Add header before table ======
       const rightBody = document.querySelector(".right-body");
        if (rightBody) {
            rightBody.insertBefore(headerDiv, rightBody.firstChild);
        }


        // ====== Print Page ======
        window.print();

        // ====== Remove header after print ======
        headerDiv.remove();
    });
    
});




