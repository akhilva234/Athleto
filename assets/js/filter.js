document.querySelectorAll('.dropdown-checkbox .dropdown-btn').forEach(button => {
    button.addEventListener('click', function (e) {
        e.stopPropagation(); 
        closeAllDropdowns(); 
        this.parentElement.classList.toggle('show');
    });
});

// Close dropdowns when clicking outside
window.addEventListener('click', function () {
    closeAllDropdowns();
});

function closeAllDropdowns() {
    document.querySelectorAll('.dropdown-checkbox').forEach(dd => dd.classList.remove('show'));
}
