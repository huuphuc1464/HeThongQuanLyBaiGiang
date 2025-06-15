<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <h3 class="tile-title">Đổi mật khẩu</h3>
            <div class="tile-body">
                <form action="{{ route('doi-mat-khau.submit') }}" method="POST" class="row">
                    @csrf
                    <div class="form-group col-12">
                        <input autocomplete="off" class=" form-control" type="password" placeholder="Nhập mật khẩu hiện tại" name="oldPassword" id="oldPassword" required>
                        <span toggle="#oldPassword" class="far fa-fw fa-eye field-icon click-eye"></span>
                        <span class="icon-lock-key">
                            <i class="fas fa-key"></i>
                        </span>
                    </div>
                    <div class="form-group col-12">
                        <input autocomplete="off" class=" form-control" type="password" placeholder="Nhập mật khẩu mới" name="newPassword" id="newPassword" required>
                        <span toggle="#newPassword" class="far fa-fw fa-eye field-icon click-eye"></span>
                        <span class="icon-lock-key">
                            <i class="fas fa-unlock-alt"></i>
                        </span>
                    </div>
                    <div class="form-group col-12">
                        <input autocomplete="off" class=" form-control" type="password" placeholder="Nhập xác nhận mật khẩu mới" name="newPassword_confirmation" id="newPassword_confirmation" required>
                        <span toggle="#newPassword_confirmation" class="far fa-fw fa-eye field-icon click-eye"></span>
                        <span class="icon-lock-key">
                            <i class="fas fa-lock"></i>
                        </span>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-success" type="submit">Lưu lại</button>
                        <a class="btn btn-danger" href="{{ redirect()->back()->getTargetUrl() }}">Hủy bỏ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
