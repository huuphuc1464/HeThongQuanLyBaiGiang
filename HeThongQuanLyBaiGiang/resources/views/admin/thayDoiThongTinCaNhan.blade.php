@extends('layouts.adminLayout')
@section('title','Quản trị viên - Thay đổi thông tin cá nhân')

@section('content')
@include('components.thayDoiThongTinCaNhan')
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('./css/doiThongTinCaNhan.css') }}">
@endsection

@section('scripts')
<script src="{{ asset('./js/doiThongTinCaNhan.js') }}"></script>
@endsection
