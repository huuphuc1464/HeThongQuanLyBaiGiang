<div class="card mb-4">
    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
        <span>Thống kê thời gian nộp bài</span>
        <div class="d-flex align-items-center">
            <label class="me-2 mb-0">Khoảng thời gian:</label>
            <input id="binSizeInput" type="number" class="form-control form-control-sm w-auto me-3" min="1" value="5"
                step="1" style="width:100px;display:inline-block;" />
            <label class="me-2 mb-0">Loại biểu đồ:</label>
            <select id="chartTypeSelect" class="form-select form-select-sm w-auto me-3">
                <option value="bar" selected>Histogram</option>
                <option value="scatter">Bubble (Điểm & Thời gian nộp)</option>
            </select>
            <button id="btnShowChartTime" class="btn btn-light btn-sm" type="button">
                <i class="fas fa-chart-bar"></i> Hiện biểu đồ
            </button>
        </div>
    </div>
    <div class="card-body">
        @php
        $ngayNops = collect($danhSachSinhVienLam ?? [])->pluck('NgayNop')->filter();
        $ngaySomNhat = $ngayNops->min();
        $ngayMuonNhat = $ngayNops->max();
        $ngayTB = $ngayNops->count() ? date('d/m/Y H:i:s', $ngayNops->map(fn($n) => strtotime($n))->avg()) : null;
        @endphp
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="card-title">Nộp sớm nhất</h6>
                        <p class="fw-bold">{{ $ngaySomNhat ? date('d/m/Y H:i:s', strtotime($ngaySomNhat)) : '-' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="card-title">Nộp muộn nhất</h6>
                        <p class="fw-bold">{{ $ngayMuonNhat ? date('d/m/Y H:i:s', strtotime($ngayMuonNhat)) : '-' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="card-title">Thời gian nộp trung bình</h6>
                        <p class="fw-bold">{{ $ngayTB ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div id="chartTimeContainer" style="display:none;">
            <canvas id="chartTimeNop" height="120"></canvas>
        </div>
        <div class="d-flex justify-content-end mb-2">
            <label class="me-2">Sắp xếp:</label>
            <select id="sortTimeNop" class="form-select form-select-sm w-auto">
                <option value="asc">Thời gian nộp tăng dần</option>
                <option value="desc">Thời gian nộp giảm dần</option>
            </select>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="tableTimeNop">
                <thead class="table-light">
                    <tr>
                        <th>STT</th>
                        <th>MSSV</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Thời gian nộp</th>
                    </tr>
                </thead>
                <tbody id="tbodyTimeNop">
                    @foreach($danhSachSinhVienLam as $i => $sv)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $sv['MSSV'] }}</td>
                        <td>{{ $sv['HoTen'] }}</td>
                        <td>{{ $sv['Email'] }}</td>
                        <td>{{ $sv['NgayNop'] ? date('d/m/Y H:i:s', strtotime($sv['NgayNop'])) : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    window.danhSachSinhVienLam = {!! json_encode($danhSachSinhVienLam)!!};
    document.addEventListener('DOMContentLoaded', function () {
        // Histogram & Line chart
        const btnShowChartTime = document.getElementById('btnShowChartTime');
        const chartTimeContainer = document.getElementById('chartTimeContainer');
        const binSizeSelect = document.getElementById('binSizeInput');
        const chartTypeSelect = document.getElementById('chartTypeSelect');
        let chartTimeInstance = null;
        let lastBinSize = parseInt(document.getElementById('binSizeInput').value);
        let lastChartType = chartTypeSelect.value;
        function drawChart(binSizeMinute, chartType) {
            // Lấy dữ liệu từ bảng hiện tại
            const times = Array.from(document.querySelectorAll('#tbodyTimeNop tr td:last-child'))
                .map(td => td.textContent.trim())
                .filter(val => val && val !== '-');
            if (times.length === 0) return;
            // Chuyển sang timestamp
            const timestamps = times.map(t => new Date(t.replace(/(\d{2})\/(\d{2})\/(\d{4})/, '$2/$1/$3')).getTime());
            // Sắp xếp tăng dần
            timestamps.sort((a, b) => a - b);
            const ctx = document.getElementById('chartTimeNop').getContext('2d');
            if (chartTimeInstance) chartTimeInstance.destroy();
            if (chartType === 'bar') {
                // Histogram
                const binSize = binSizeMinute * 60 * 1000;
                const min = Math.min(...timestamps);
                const max = Math.max(...timestamps);
                const binCount = Math.ceil((max - min) / binSize) + 1;
                const bins = Array(binCount).fill(0);
                const labels = [];
                for (let i = 0; i < binCount; i++) {
                    const start = new Date(min + i * binSize);
                    const end = new Date(min + (i + 1) * binSize);
                    const label =
                        start.getDate().toString().padStart(2, '0') + '/' +
                        (start.getMonth() + 1).toString().padStart(2, '0') + ' ' +
                        start.getHours().toString().padStart(2, '0') + ':' + start.getMinutes().toString().padStart(2, '0') +
                        ' - ' +
                        end.getDate().toString().padStart(2, '0') + '/' +
                        (end.getMonth() + 1).toString().padStart(2, '0') + ' ' +
                        end.getHours().toString().padStart(2, '0') + ':' + end.getMinutes().toString().padStart(2, '0');
                    labels.push(label);
                }
                timestamps.forEach(ts => {
                    const idx = Math.floor((ts - min) / binSize);
                    bins[idx]++;
                });
                chartTimeInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Số sinh viên nộp',
                            data: bins,
                            backgroundColor: 'rgba(108, 117, 125, 0.7)'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            title: { display: true, text: `Histogram thời gian nộp bài (mỗi ${binSizeMinute} phút)` }
                        },
                        scales: {
                            x: { title: { display: true, text: 'Khoảng thời gian' } },
                            y: { title: { display: true, text: 'Số sinh viên' }, beginAtZero: true }
                        }
                    }
                });
            } else if (chartType === 'scatter') {
                // Bubble: điểm số theo thời gian nộp, kích thước theo số lượng SV
                // Nhóm các lần nộp theo thời gian và điểm số
                const group = {};
                window.danhSachSinhVienLam.forEach(sv => {
                    if (!sv.NgayNop || sv.NgayNop === '-' || sv.Diem === null || sv.Diem === undefined) return;
                    const ts = new Date(sv.NgayNop.replace(/(\d{2})\/(\d{2})\/(\d{4})/, '$2/$1/$3')).getTime();
                    const key = ts + '_' + sv.Diem;
                    if (!group[key]) group[key] = { x: ts, y: sv.Diem, r: 0, MSSVs: [] };
                    group[key].r += 5; // mỗi SV tăng bán kính 5px
                    group[key].MSSVs.push(sv.MSSV);
                });
                const bubbleData = Object.values(group);
                if (bubbleData.length === 0) return;
                const minX = Math.min(...bubbleData.map(d => d.x));
                const maxX = Math.max(...bubbleData.map(d => d.x));
                chartTimeInstance = new Chart(ctx, {
                    type: 'bubble',
                    data: {
                        datasets: [{
                            label: 'Điểm số theo thời gian nộp',
                            data: bubbleData,
                            backgroundColor: 'rgba(0,123,255,0.7)'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            title: { display: true, text: 'Biểu đồ bubble: Điểm số & Thời gian nộp bài' },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const d = context.raw;
                                        const date = new Date(d.x);
                                        const dateStr = date.getDate().toString().padStart(2, '0') + '/' +
                                            (date.getMonth() + 1).toString().padStart(2, '0') + ' ' +
                                            date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');
                                        return `Điểm: ${d.y} | Nộp: ${dateStr} | Số SV: ${d.MSSVs.length}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                type: 'linear',
                                title: { display: true, text: 'Thời gian nộp' },
                                min: minX,
                                max: maxX,
                                ticks: {
                                    callback: function (value) {
                                        const date = new Date(value);
                                        return date.getDate().toString().padStart(2, '0') + '/' +
                                            (date.getMonth() + 1).toString().padStart(2, '0') + ' ' +
                                            date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');
                                    },
                                    autoSkip: true,
                                    maxTicksLimit: 10
                                }
                            },
                            y: {
                                title: { display: true, text: 'Điểm số' },
                                beginAtZero: true,
                                min: 0,
                                max: 10
                            }
                        }
                    }
                });
            }
        }
        btnShowChartTime.addEventListener('click', function () {
            if (chartTimeContainer.style.display === 'none') {
                chartTimeContainer.style.display = '';
                drawChart(parseInt(binSizeSelect.value), chartTypeSelect.value);
                lastBinSize = parseInt(binSizeSelect.value);
                lastChartType = chartTypeSelect.value;
            } else {
                chartTimeContainer.style.display = 'none';
            }
        });
        document.getElementById('binSizeInput').addEventListener('input', function () {
            if (chartTimeContainer.style.display !== 'none' && chartTypeSelect.value === 'bar') {
                let val = parseInt(this.value);
                if (isNaN(val) || val < 1) val = 1;
                drawChart(val, 'bar');
                lastBinSize = val;
            }
        });
        chartTypeSelect.addEventListener('change', function () {
            if (chartTimeContainer.style.display !== 'none') {
                let binVal = parseInt(document.getElementById('binSizeInput').value);
                if (isNaN(binVal) || binVal < 1) binVal = 1;
                drawChart(binVal, chartTypeSelect.value);
                lastChartType = chartTypeSelect.value;
            }
        });
        // Sắp xếp bảng
        const sortSelect = document.getElementById('sortTimeNop');
        sortSelect.addEventListener('change', function () {
            const tbody = document.getElementById('tbodyTimeNop');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            rows.sort((a, b) => {
                const tA = a.children[4].textContent.trim();
                const tB = b.children[4].textContent.trim();
                if (!tA || tA === '-') return 1;
                if (!tB || tB === '-') return -1;
                const dA = new Date(tA.replace(/(\d{2})\/(\d{2})\/(\d{4})/, '$2/$1/$3')).getTime();
                const dB = new Date(tB.replace(/(\d{2})\/(\d{2})\/(\d{4})/, '$2/$1/$3')).getTime();
                return sortSelect.value === 'asc' ? dA - dB : dB - dA;
            });
            rows.forEach((row, idx) => {
                row.children[0].textContent = idx + 1;
                tbody.appendChild(row);
            });
            // Vẽ lại biểu đồ nếu đang mở
            if (chartTimeContainer.style.display !== 'none') {
                let binVal = parseInt(document.getElementById('binSizeInput').value);
                if (isNaN(binVal) || binVal < 1) binVal = 1;
                drawChart(binVal, chartTypeSelect.value);
            }
        });
    });
</script>