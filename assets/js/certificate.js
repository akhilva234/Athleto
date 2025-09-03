function infoPass() {
    const table = document.querySelector('.result-table tbody');

    table.addEventListener('click', async function(e) {
        if (e.target.classList.contains('dwnld-btn')) {
            const btn = e.target;
            const resultId = btn.getAttribute('data-result-id');

            try {
                const response = await fetch(`../common_pages/certificate.php?result_id=${resultId}`);
                if (!response.ok) {
                    alert("Download failed");
                    return;
                }

                const blob = await response.blob();
                const url = URL.createObjectURL(blob);

                if (blob.type !== "image/png") {
                // The server probably sent an error message instead of image
                const text = await blob.text();
                console.error("Server response:", text);
                alert("Server error: " + text);
                return;
            }


                const a = document.createElement("a");
                a.href = url;
                a.download = "certificate.png"; // filename set here
                a.click();

                URL.revokeObjectURL(url);
            } catch (err) {
                console.error(err);
                alert("Error downloading certificate");
            }
        }
    });
}

infoPass();
