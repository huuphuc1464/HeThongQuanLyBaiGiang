<div class="card mb-4">
    <div class="card-header bg-warning text-dark">Danh sách sinh viên chưa làm bài</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>STT</th>
                    <th>MSSV</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                @foreach($danhSachSinhVienChuaLam as $i => $sv)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $sv['MSSV'] }}</td>
                    <td>{{ $sv['HoTen'] }}</td>
                    <td>{{ $sv['Email'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>