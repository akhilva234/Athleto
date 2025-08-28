function userfetch() {
    const tableBody = document.querySelector('.users-table tbody');

    if (!tableBody) return; // safeguard if table is missing

    tableBody.addEventListener('click', function (e) {
        if (e.target.classList.contains('update-btn')) {
            const btn = e.target;
            const userId = btn.dataset.userId;

            fetch(`../admin/userinfo.php?user_id=${userId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        alert("Error: " + data.error);
                        return;
                    }

                    const modal = document.getElementById('editUserModal');
                    modal.innerHTML = data.html;
                    modal.style.display = 'block';
                })
                .catch(err => {
                    console.error("Fetch failed:", err);
                    alert("Error loading user data. Please try again.");
                });
        }
    });
}

userfetch();
