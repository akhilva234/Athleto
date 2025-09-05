function infoPass() {
    const table = document.querySelector('.result-table tbody');

    table.addEventListener('click', async function (e) {
  
        if (e.target.classList.contains('dwnld-btn')) {
            const btn = e.target;
            const resultId = btn.getAttribute('data-result-id');
            const athleteId = btn.getAttribute('data-athlete-id');

            await downloadSingleCertificate(resultId,athleteId);
        }
        if (e.target.classList.contains('team-btn')) {
            const btn = e.target;
            const teamId = btn.getAttribute('data-team-id');

            try {
                const response = await fetch(`../common_pages/relay_certificates.php?team_id=${teamId}`);
                if (!response.ok) {
                    alert("Failed to fetch team members");
                    return;
                }

                const athletes = await response.json();
                if (!athletes.length) {
                    alert("No athletes found for this team");
                    return;
                }

                // Loop over each athlete and download their certificate
                for (const athlete of athletes) {
                    await downloadRelayCertificate(athlete.result_id, athlete.athlete_id);
                }
            } catch (err) {
                console.error(err);
                alert("Error fetching relay team certificates");
            }
        }
    });
}

async function downloadSingleCertificate(resultId,athleteId) {
    try {
        const response = await fetch(`../common_pages/certificate.php?result_id=${resultId}`);
        if (!response.ok) {
            alert("Download failed");
            return;
        }

        const blob = await response.blob();
        if (blob.type !== "image/png") {
            const text = await blob.text();
            console.error("Server response:", text);
            return;
        }

        const url = URL.createObjectURL(blob);

        const a = document.createElement("a");
        a.href = url;
        a.download = `${athleteId}_certificate.png`; // filename
        a.click();

        URL.revokeObjectURL(url);
    } catch (err) {
        console.error(err);
    }
}


async function downloadRelayCertificate(resultId, athleteId) {
    try {
        const response = await fetch(`../common_pages/certificate.php?result_id=${resultId}&athlete_id=${athleteId}`);

        if (!response.ok) throw new Error("Failed to fetch certificate");

        const blob = await response.blob();

        const contentDisposition = response.headers.get("Content-Disposition");
        let filename = "certificate.png";
        if (contentDisposition && contentDisposition.includes("filename=")) {
            filename = contentDisposition.split("filename=")[1].replace(/"/g, "");
        }

        // Create link
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(url);

    } catch (err) {
        console.error("Download failed:", err);
    }
}


infoPass();
