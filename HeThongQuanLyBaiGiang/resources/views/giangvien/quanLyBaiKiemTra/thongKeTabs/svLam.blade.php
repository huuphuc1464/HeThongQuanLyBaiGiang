<div class="card mb-4">
    <div class="card-header bg-primary text-white">Danh sách sinh viên đã làm bài</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover">
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
                <tr>
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