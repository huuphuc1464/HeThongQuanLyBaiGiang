<div class="card mb-4">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <span>Tỷ lệ đúng/sai từng câu hỏi</span>
        <button id="btnShowChart" class="btn btn-light btn-sm" type="button">
            <i class="fas fa-chart-bar"></i> Hiện biểu đồ
        </button>
    </div>
    <div class="card-body table-responsive">
        <div id="chartContainer" style="display:none;">
            <canvas id="chartCauHoi" height="120"></canvas>
        </div>
        <table class="table table-bordered table-hover mt-3">
            <thead class="table-light">
                <tr>
                    <th>Câu hỏi</th>
                    <th>Số SV trả lời đúng</th>
                    <th>Số SV trả lời sai</th>
                    <th>Tỷ lệ đúng (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($thongKeCauHoi as $cauHoi)
                <tr>
                    <td>{{ $cauHoi['CauHoi'] }}</td>
                    <td>{{ $cauHoi['SoDung'] }}</td>
                    <td>{{ $cauHoi['SoSai'] }}</td>
                    <td>{{ $cauHoi['TyLeDung'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const btnShowChart = document.getElementById('btnShowChart');
    const chartContainer = document.getElementById('chartContainer');
    let chartInstance = null;
    btnShowChart.addEventListener('click', function () {
        if (chartContainer.style.display === 'none') {
            chartContainer.style.display = '';
            if (!chartInstance) {
                fetch(`/api/bai-kiem-tra/{{ $baiKiemTra->MaBaiKiemTra }}/thong-ke/cau-hoi`)
                    .then(res => res.json())
                    .then(data => {
                        const labels = data.map((item, idx) => 'Câu ' + (idx + 1));
                        const fullLabels = data.map(item => item.cauHoi);
                        const soDung = data.map(item => item.soDung);
                        const soSai = data.map(item => item.soSai);
                        const ctx = document.getElementById('chartCauHoi').getContext('2d');
                        chartInstance = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [
                                    {
                                        label: 'Số đúng',
                                        data: soDung,
                                        backgroundColor: 'rgba(40, 167, 69, 0.7)'
                                    },
                                    {
                                        label: 'Số sai',
                                        data: soSai,
                                        backgroundColor: 'rgba(220, 53, 69, 0.7)'
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: { position: 'top' },
                                    title: { display: true, text: 'Biểu đồ đúng/sai từng câu hỏi' },
                                    tooltip: {
                                        callbacks: {
                                            title: function (context) {
                                                const idx = context[0].dataIndex;
                                                return labels[idx] + ': ' + fullLabels[idx];
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: { title: { display: true, text: 'Câu hỏi' } },
                                    y: { title: { display: true, text: 'Số lượng' }, beginAtZero: true }
                                }
                            }
                        });
                    });
            }
        } else {
            chartContainer.style.display = 'none';
        }
    });
</script>
@endsection