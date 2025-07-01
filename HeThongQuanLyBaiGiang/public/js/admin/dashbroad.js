document.addEventListener('DOMContentLoaded', function () {
    var ctx = document.getElementById('heThongBarChart');
    var selectYear = document.getElementById('selectYear');
    var chartInstance = null;

    // Hàm vẽ hoặc cập nhật chart
    function renderChart(stats, year) {
        var data = {
            labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
            datasets: [
                {
                    label: 'Bài giảng',
                    data: stats.baiGiang,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    fill: false,
                    tension: 0.2
                },
                {
                    label: 'Bài kiểm tra',
                    data: stats.baiKiemTra,
                    borderColor: 'rgba(255, 206, 86, 1)',
                    backgroundColor: 'rgba(255, 206, 86, 0.1)',
                    fill: false,
                    tension: 0.2
                },
                {
                    label: 'Thông báo',
                    data: stats.thongBao,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.1)',
                    fill: false,
                    tension: 0.2
                }
            ]
        };
        var config = {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: {
                        display: true,
                        text: 'Thống kê hoạt động hệ thống theo tháng - ' + year
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        precision: 0
                    }
                }
            }
        };
        if (chartInstance) {
            chartInstance.data = data;
            chartInstance.options = config.options;
            chartInstance.update();
        } else {
            chartInstance = new Chart(ctx, config);
        }
    }

    // Lấy danh sách năm và render dropdown
    fetch('/admin/dashboard/years')
        .then(res => res.json())
        .then(years => {
            if (!years.length) return;
            selectYear.innerHTML = years.map(y => `<option value="${y}">${y}</option>`).join('');
            // Mặc định chọn năm đầu tiên (mới nhất)
            fetchStatsAndRender(years[0]);
        });

    // Khi đổi năm
    selectYear.addEventListener('change', function () {
        fetchStatsAndRender(this.value);
    });

    // Hàm fetch dữ liệu và render chart
    function fetchStatsAndRender(year) {
        fetch(`/admin/dashboard/stats-by-year/${year}`)
            .then(res => res.json())
            .then(stats => {
                renderChart(stats, year);
            });
    }
});
