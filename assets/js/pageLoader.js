document.addEventListener('DOMContentLoaded', function() {
    const select = document.querySelector('.action-select');

    if (select) {
      select.addEventListener('change', function() {
        const selectedPage = this.value;
        if (selectedPage) {
          window.location.href = '?page=' + selectedPage;
        }
      });
    }
  });