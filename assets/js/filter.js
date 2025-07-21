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

function getCheckedValues(classname){

    return Array.from(document.querySelector('.'+classname+':checked'))
    .map(cb=>cb.value);
}

async function loadAthletes(){

    const params=new URLSearchParams({
        dept:getCheckedValues('dept-checkbox').join(','),
        event:getCheckedValues('event-checkbox').join(','),
        category:getCheckedValues('cat-checkbox').join(',')
    });
    const response=await fetch('filter_athletes.php?'+params.toString());
    const data=await response.json();
    renderTable(data);
}

document.querySelectorAll('.dropdown-checkbox').forEach(btn=>{

    btn.addEventListener('change',loadAthletes);
})

