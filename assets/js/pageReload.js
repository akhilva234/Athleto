
    window.addEventListener("pageshow", function (event) {
       // if browser loaded the page from bfcache (after back button)
       if (event.persisted) {
           window.location.reload(); // force reload, session check will redirect
       }
    });
