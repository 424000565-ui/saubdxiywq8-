<script>
// Live search — filters table rows by student name or email
const searchInput = document.getElementById('liveSearch');
const tableRows   = document.querySelectorAll('#registrantsTable tbody tr');

searchInput.addEventListener('input', function () {
    const query = this.value.trim().toLowerCase();

    tableRows.forEach(function (row) {
        const studentCell = row.querySelector('td:nth-child(2)');
        const text = studentCell ? studentCell.textContent.toLowerCase() : '';
        row.style.display = text.includes(query) ? '' : 'none';
    });
});
</script>
