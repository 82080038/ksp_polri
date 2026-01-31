// assets/js/auth.js
$(document).ready(function () {
    $('#loginForm').on('submit', function (e) {
        e.preventDefault();

        const $btn = $('#loginBtn');
        const originalText = $btn.text();
        $btn.prop('disabled', true).text('Memproses...');

        $.post('../backend/public/index.php?path=login', $(this).serialize(), function (data) {
            if (data.status) {
                const role = data.data && data.data.role ? data.data.role : 'pengurus';
                const target = role === 'pengurus' ? 'pages/dashboard_pengurus.html' : 'pages/dashboard_anggota.html';
                window.location.href = target;
            } else {
                $('#message').text(data.message || 'Login gagal');
            }
        }, 'json').fail(function () {
            $('#message').text('Terjadi kesalahan sistem.');
        }).always(function () {
            $btn.prop('disabled', false).text(originalText);
        });
    });
});
