<div class="card mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span>Danh sách sinh viên đã làm bài</span>
        <div class="d-flex align-items-center">
            <label class="me-2 mb-0">Sắp xếp:</label>
            <select id="sortDiemSvLam" class="form-select form-select-sm w-auto me-3">
                <option value="default">Mặc định</option>
                <option value="desc">Điểm giảm dần</option>
                <option value="asc">Điểm tăng dần</option>
            </select>
            <a href="#" id="btnExportExcelSvLam" class="btn btn-light btn-sm" title="Tải Excel">
                <i class="fas fa-file-excel"></i> Tải Excel
            </a>
        </div>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover" id="tableSvLam">
            <thead class="table-light">
                <tr>
                    <th>STT</th>
                    <th>MSSV</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Thời gian nộp</th>
                    <th>Số câu đúng</th>
                    <th>Điểm</th>
                </tr>
            </thead>
            <tbody>
                @foreach($danhSachSinhVienLam as $i => $sv)
                <tr data-stt="{{ $i }}">
                    <td>{{ $i+1 }}</td>
                    <td>{{ $sv['MSSV'] }}</td>
                    <td>{{ $sv['HoTen'] }}</td>
                    <td>{{ $sv['Email'] }}</td>
                    <td>{{ $sv['NgayNop'] }}</td>
                    <td>{{ $sv['TongCauDung'] }}/{{ $sv['TongSoCauHoi'] }}</td>
                    <td>{{ $sv['Diem'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sortSelect = document.getElementById('sortDiemSvLam');
        const table = document.getElementById('tableSvLam');
        sortSelect.addEventListener('change', function () {
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            if (sortSelect.value === 'default') {
                rows.sort((a, b) => parseInt(a.getAttribute('data-stt')) - parseInt(b.getAttribute('data-stt')));
            } else {
                rows.sort((a, b) => {
                    const diemA = parseFloat(a.children[6].textContent.trim());
                    const diemB = parseFloat(b.children[6].textContent.trim());
                    if (isNaN(diemA)) return 1;
                    if (isNaN(diemB)) return -1;
                    return sortSelect.value === 'asc' ? diemA - diemB : diemB - diemA;
                });
            }
            rows.forEach((row, idx) => {
                row.children[0].textContent = idx + 1;
                tbody.appendChild(row);
            });
        });
        // Nút tải Excel (cần backend route export)
        document.getElementById('btnExportExcelSvLam').addEventListener('click', function (e) {
            e.preventDefault();
            window.location.href = "{{ route('giangvien.bai-kiem-tra.export-sv-lam', ['id' => $baiKiemTra->MaBaiKiemTra]) }}";
        });
    });
</script>