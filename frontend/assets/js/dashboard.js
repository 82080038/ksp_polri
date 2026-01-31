// assets/js/dashboard.js
$(document).ready(function() {
    $('#anggotaLink').click(function(e) {
        e.preventDefault();
        loadAnggota();
    });

    $('#simpananLink').click(function(e) {
        e.preventDefault();
        $('#content').html(`
            <h3>Simpanan</h3>
            <form id="simpananForm">
                Anggota ID: <input type="number" name="anggota_id" required><br>
                Jenis: <select name="jenis">
                    <option value="WAJIB">Wajib</option>
                    <option value="SUKARELA">Sukarela</option>
                    <option value="POKOK">Pokok</option>
                </select><br>
                Jumlah: <input type="number" name="jumlah" required><br>
                <button type="submit">Tambah Simpanan</button>
            </form>
        `);
        $('#simpananForm').on('submit', function(e) {
            e.preventDefault();
            $.post('../backend/public/index.php?path=simpanan/create', $(this).serialize(), function(data) {
                alert(data.message);
            }, 'json');
        });
    });

    // Add similar for pinjaman, shu, rat
});

function loadAnggota() {
    $.get('../backend/public/index.php?path=anggota/list', function(data) {
        if (data.status) {
            let html = '<h3>Data Anggota</h3><table border="1"><tr><th>NRP</th><th>Nama</th><th>Pangkat</th><th>Satuan</th></tr>';
            data.data.forEach(a => {
                html += `<tr><td>${a.nrp}</td><td>${a.nama}</td><td>${a.pangkat}</td><td>${a.satuan}</td></tr>`;
            });
            html += '</table>';
            $('#content').html(html);
        }
    }, 'json');
}
