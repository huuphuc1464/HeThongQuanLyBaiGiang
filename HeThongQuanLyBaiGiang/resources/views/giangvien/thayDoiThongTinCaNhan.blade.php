@extends('layouts.teacherLayout')
@section('title','Giảng viên - Thay đổi thông tin cá nhân')
@section('tenTrang', 'Thay đổi thông tin cá nhân')

@section('content')
@include('components.thayDoiThongTinCaNhan')
@endsection

@section('style')
<link rel="stylesheet" href="{{ asset('./css/doiThongTinCaNhan.css') }}">
@endsection

@section('scripts')
<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('thumbimage').src = e.target.result;
                document.getElementById('thumbimage').style.display = 'block';
                document.querySelector('.removeimg').style.display = 'block';
                document.querySelector('.filename').textContent = input.files[0].name;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('.Choicefile')?.addEventListener('click', () => {
            document.getElementById('uploadfile').click();
        });

        document.querySelector('.removeimg')?.addEventListener('click', () => {
            document.getElementById('uploadfile').value = '';
            document.getElementById('thumbimage').style.display = 'none';
            document.querySelector('.removeimg').style.display = 'none';
            document.querySelector('.filename').textContent = '';
        });
    });

</script>


@endsection
