const view=document.body.dataset.view;
function addEvent() {
    const closeButton = document.querySelector('.cancel-btn');
    const addButton = document.querySelector('.add-btn');
    const form = document.querySelector('.eventmodal');
    const blurOverlay = document.querySelector('.whole-blur-container');

    if (!closeButton || !addButton || !form || !blurOverlay) {
        console.error("Missing required DOM elements.");
        return;
    }

    addButton.addEventListener('click', () => {
        form.style.display = 'grid';
        blurOverlay.style.display = 'block';
    });

    closeButton.addEventListener('click', () => {
        form.style.display = 'none';
        blurOverlay.style.display = 'none';
    });
}

function addDept() {
    const closeButton = document.querySelector('.cancel-btn');
    const addButton = document.querySelector('.add-btn');
    const form = document.querySelector('.deptmodal');
    const blurOverlay = document.querySelector('.whole-blur-container');

    if (!closeButton || !addButton || !form || !blurOverlay) {
        console.error("Missing required DOM elements.");
        return;
    }

    addButton.addEventListener('click', () => {
        form.style.display = 'grid';
        blurOverlay.style.display = 'block';
    });

    closeButton.addEventListener('click', () => {
        form.style.display = 'none';
        blurOverlay.style.display = 'none';
    });
}

if(view==='departments'){
console.log(view);
document.addEventListener('DOMContentLoaded', addDept);
}

if(view==='events'){
console.log(view);
document.addEventListener('DOMContentLoaded', addEvent);
}
