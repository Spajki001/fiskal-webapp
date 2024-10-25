document.addEventListener('DOMContentLoaded', function () {
    const yearForm = document.getElementById('yearForm');

    if (yearForm) {
        yearForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const activeYear = document.getElementById('activeYear').value;

            // Send the activeYear to the server using fetch
            fetch('set_active_year.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ activeYear: activeYear })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'zakljucci.php';
                } else {
                    alert('Failed to set active year');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }
});