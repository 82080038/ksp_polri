// assets/js/anggota_dashboard.js
$(document).ready(function() {
    $('#saldoLink').click(function(e) {
        e.preventDefault();
        $.get('../backend/public/index.php?path=simpanan/saldo&anggota_id=1', function(data) { // Assume anggota_id=1 for demo
            if (data.status) {
                let html = '<h3>Saldo Simpanan</h3>';
                html += `Pokok: ${data.data.POKOK}<br>`;
                html += `Wajib: ${data.data.WAJIB}<br>`;
                html += `Sukarela: ${data.data.SUKARELA}<br>`;
                html += `<b>Total: ${data.data.TOTAL}</b>`;
                $('#content').html(html);
            }
        }, 'json');
    });

    $('#pinjamanLink').click(function(e) {
        e.preventDefault();
        $.get('../backend/public/index.php?path=pinjaman/list', function(data) {
            if (data.status) {
                let html = '<h3>Pinjaman Saya</h3><table border="1"><tr><th>Nama</th><th>Jumlah</th><th>Status</th></tr>';
                data.data.forEach(p => {
                    html += `<tr><td>${p.nama}</td><td>${p.jumlah}</td><td>${p.status}</td></tr>`;
                });
                html += '</table>';
                $('#content').html(html);
            }
        }, 'json');
    });

    $('#shuLink').click(function(e) {
        e.preventDefault();
        $.get('../backend/public/index.php?path=shu/anggota&tahun=2024', function(data) { // Assume tahun
            if (data.status) {
                $('#content').html(`<h3>SHU</h3>Jumlah: ${data.data}`);
            }
        }, 'json');
    });
});
