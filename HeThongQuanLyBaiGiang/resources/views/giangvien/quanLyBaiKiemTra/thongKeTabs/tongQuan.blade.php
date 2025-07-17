<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Tổng số sinh viên đã làm</h5>
                <p class="display-6">{{ $tongSinhVienLam }} / {{ $tongSinhVienLop }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Tỷ lệ hoàn thành</h5>
                <p class="display-6">{{ $tyLeHoanThanh }}%</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Điểm TB</h5>
                <p class="display-6">{{ $diemTrungBinh }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Điểm cao nhất</h5>
                <p class="display-6">{{ $diemCaoNhat }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Điểm thấp nhất</h5>
                <p class="display-6">{{ $diemThapNhat }}</p>
            </div>
        </div>
    </div>
</div>