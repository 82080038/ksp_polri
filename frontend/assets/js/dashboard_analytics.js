// assets/js/dashboard_analytics.js
$(document).ready(function() {
    // Initialize all charts
    loadStats();
    loadSimpananChart();
    loadPinjamanCharts();
    loadAngsuranChart();
    loadShuChart();
    loadFinancialChart();
    loadRecentActivities();
});

// Format rupiah
function formatRupiah(angka) {
    return 'Rp ' + parseFloat(angka || 0).toLocaleString('id-ID');
}

// Load main stats
function loadStats() {
    $.get('../backend/public/index.php?path=dashboard/stats', function(data) {
        if (data.status) {
            $('#totalAnggota').text(data.data.total_anggota.toLocaleString('id-ID'));
            $('#totalSimpanan').text(formatRupiah(data.data.total_simpanan));
            $('#pinjamanAktif').text(data.data.pinjaman_aktif.toLocaleString('id-ID') + ' (' + formatRupiah(data.data.nominal_pinjaman) + ')');
            $('#tunggakan').text(data.data.tunggakan.toLocaleString('id-ID'));
            $('#shuTahunIni').text(formatRupiah(data.data.shu_tahun_ini));
        }
    }, 'json');
}

// Simpanan Trend Chart
function loadSimpananChart() {
    $.get('../backend/public/index.php?path=dashboard/simpananTrend', function(data) {
        if (data.status) {
            const ctx = document.getElementById('simpananChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.data.labels,
                    datasets: data.data.datasets.map(ds => ({
                        label: ds.label,
                        data: ds.data,
                        borderColor: ds.color,
                        backgroundColor: ds.color + '40',
                        fill: true,
                        tension: 0.4
                    }))
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return formatRupiah(value);
                                }
                            }
                        }
                    }
                }
            });
        }
    }, 'json');
}

// Pinjaman Charts
function loadPinjamanCharts() {
    $.get('../backend/public/index.php?path=dashboard/pinjamanStats', function(data) {
        if (data.status) {
            // Status Pie Chart
            const ctxStatus = document.getElementById('pinjamanStatusChart').getContext('2d');
            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: data.data.status_distribution.map(s => s.status),
                    datasets: [{
                        data: data.data.status_distribution.map(s => s.count),
                        backgroundColor: data.data.status_distribution.map(s => s.color)
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });

            // Monthly Trend Bar Chart
            const ctxTrend = document.getElementById('pinjamanTrendChart').getContext('2d');
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            new Chart(ctxTrend, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Nominal Pinjaman',
                        data: data.data.monthly_trend,
                        backgroundColor: '#2196F3'
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return formatRupiah(value);
                                }
                            }
                        }
                    }
                }
            });
        }
    }, 'json');
}

// Angsuran Chart
function loadAngsuranChart() {
    $.get('../backend/public/index.php?path=dashboard/angsuranStats', function(data) {
        if (data.status) {
            const ctx = document.getElementById('angsuranChart').getContext('2d');
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Pembayaran Aktual',
                        data: data.data.pembayaran_per_bulan,
                        backgroundColor: '#4CAF50'
                    }, {
                        label: 'Target',
                        data: Array(12).fill(data.data.target_per_bulan),
                        backgroundColor: '#FFC107',
                        type: 'line',
                        borderColor: '#FFC107',
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return formatRupiah(value);
                                }
                            }
                        }
                    }
                }
            });
        }
    }, 'json');
}

// SHU History Chart
function loadShuChart() {
    $.get('../backend/public/index.php?path=dashboard/shuHistory', function(data) {
        if (data.status) {
            const ctx = document.getElementById('shuChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.data.labels,
                    datasets: [{
                        label: 'SHU',
                        data: data.data.data,
                        borderColor: '#9C27B0',
                        backgroundColor: '#9C27B020',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return formatRupiah(value);
                                }
                            }
                        }
                    }
                }
            });
        }
    }, 'json');
}

// Financial Overview Chart
function loadFinancialChart() {
    $.get('../backend/public/index.php?path=dashboard/financialOverview', function(data) {
        if (data.status) {
            const ctx = document.getElementById('financialChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Aset', 'Kewajiban', 'Modal', 'Ekuitas'],
                    datasets: [{
                        label: 'Nilai',
                        data: [
                            data.data.aset,
                            data.data.kewajiban,
                            data.data.modal,
                            data.data.ekuitas
                        ],
                        backgroundColor: ['#4CAF50', '#F44336', '#2196F3', '#FF9800']
                    }]
                },
                options: {
                    responsive: true,
                    indexAxis: 'y',
                    scales: {
                        x: {
                            ticks: {
                                callback: function(value) {
                                    return formatRupiah(value);
                                }
                            }
                        }
                    }
                }
            });
        }
    }, 'json');
}

// Recent Activities
function loadRecentActivities() {
    $.get('../backend/public/index.php?path=dashboard/recentActivities', function(data) {
        if (data.status) {
            let html = '';
            data.data.forEach(act => {
                const date = new Date(act.created_at).toLocaleDateString('id-ID');
                html += `
                    <div class="activity-item">
                        <div class="activity-info">
                            <span class="activity-type">${act.type}</span>
                            <span class="activity-name">${act.nama || act.nama_perusahaan || '-'}</span>
                            ${act.jumlah ? '<span class="activity-amount">' + formatRupiah(act.jumlah) + '</span>' : ''}
                        </div>
                        <span class="activity-date">${date}</span>
                    </div>
                `;
            });
            $('#recentActivities').html(html);
        }
    }, 'json');
}
