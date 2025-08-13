document.addEventListener('DOMContentLoaded', function() {
    const selects = document.querySelectorAll('.action-select');

    selects.forEach(select => {
        select.addEventListener('change', function() {
            const selectedPage = this.value;
            if (selectedPage) {
                window.location.href = selectedPage;
            }
        });
    });
});
