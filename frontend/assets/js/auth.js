// assets/js/auth.js
$(document).ready(function() {
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        $.post('../backend/public/index.php?path=login', $(this).serialize(), function(data) {
            if (data.status) {
                // Redirect based on role, assume pengurus for now
                window.location.href = 'pages/dashboard_pengurus.html';
            } else {
                $('#message').text(data.message);
            }
        }, 'json');
    });
});
