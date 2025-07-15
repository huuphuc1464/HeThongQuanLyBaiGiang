<div class="card mb-4">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <span>Phân bố điểm</span>
        <button id="btnShowChartPhanBo" class="btn btn-light btn-sm" type="button">
            <i class="fas fa-chart-bar"></i> Hiện biểu đồ
        </button>
    </div>
    <div class="card-body table-responsive">
        <div id="chartPhanBoContainer" style="display:none;">
            <canvas id="chartPhanBoDiem" height="120"></canvas>
        </div>
        <table class="table table-bordered table-hover mt-3">
            <thead class="table-light">
                <tr>
                    <th>Điểm</th>
                    <th>Số sinh viên đạt</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 0; $i <= 10; $i++) <tr>
                    <td>{{ $i }}</td>
                    <td>
                        {{
                        collect($danhSachSinhVienLam ?? [])->filter(function($sv) use ($i) {
                        return round($sv['Diem']) == $i;
                        })->count()
                        }}
                    </td>
                    </tr>
                    @endfor
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        console.log('DOMContentLoaded loaded');
        const btnShowChartPhanBo = document.getElementById('btnShowChartPhanBo');
        const chartPhanBoContainer = document.getElementById('chartPhanBoContainer');
        let chartPhanBoInstance = null;
        if (btnShowChartPhanBo) {
            btnShowChartPhanBo.addEventListener('click', function () {
                console.log('btnShowChartPhanBo clicked');
                if (chartPhanBoContainer.style.display === 'none') {
                    chartPhanBoContainer.style.display = '';
                    if (!chartPhanBoInstance) {
                        fetch(`/api/bai-kiem-tra/{{ $baiKiemTra->MaBaiKiemTra }}/thong-ke/phan-bo-diem`)
                            .then(res => res.json())
                            .then(data => {
                                const labels = Object.keys(data);
                                const values = Object.values(data);
                                const ctx = document.getElementById('chartPhanBoDiem').getContext('2d');
                                chartPhanBoInstance = new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: labels,
                                        datasets: [{
                                            label: 'Số sinh viên',
                                            data: values,
                                            backgroundColor: 'rgba(23, 162, 184, 0.7)'
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        plugins: {
                                            legend: { display: false },
                                            title: { display: true, text: 'Phân bố điểm số sinh viên' }
                                        },
                                        scales: {
                                            x: { title: { display: true, text: 'Điểm' } },
                                            y: { title: { display: true, text: 'Số sinh viên' }, beginAtZero: true }
                                        }
                                    }
                                });
                            });
                    }
                } else {
                    chartPhanBoContainer.style.display = 'none';
                }
            });
        } else {
            console.log('Không tìm thấy nút btnShowChartPhanBo');
        }
    });
</script>