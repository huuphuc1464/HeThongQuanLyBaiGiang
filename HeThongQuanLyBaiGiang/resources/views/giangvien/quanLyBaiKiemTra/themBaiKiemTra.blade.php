@extends('layouts.teacherLayout')

@section('title', 'Giảng viên - Thêm bài kiểm tra')
@section('tenTrang', 'Thêm bài kiểm tra')

@section('styles')
<style>
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .question-group {
        border: 1px solid #dee2e6;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 5px;
        position: relative;
    }

    .question-group .remove-btn {
        position: absolute;
        top: 5px;
        right: 5px;
    }

    .answer-row {
        display: flex;
        gap: 15px;
    }

    #step2 {
        display: none;
    }
</style>
@endsection

@section('content')
<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">Thêm Bài Kiểm Tra</h4>
        </div>
        <div class="card-body">
            @if (session('errorSystem'))
            <div class="alert alert-danger">{{ session('errorSystem') }}</div>
            @endif
            <form id="addQuizForm" action="{{ route('giangvien.bai-kiem-tra.them') }}" method="POST" novalidate>
                @csrf
                <!-- Bước 1: Thông tin bài kiểm tra -->
                <div id="step1">
                    <div class="mb-3">
                        <label for="quizName" class="form-label">Tên bài kiểm tra</label>
                        <input type="text" class="form-control @error('quizName') is-invalid @enderror" id="quizName"
                            name="quizName" value="{{ old('quizName') }}" required>
                        @error('quizName')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="classSelect" class="form-label">Lớp học phần</label>
                        <select class="form-select @error('classId') is-invalid @enderror" id="classSelect"
                            name="classId" required>
                            <option value="">Chọn lớp học phần</option>
                            @foreach ($lopHocPhan as $lop)
                            <option value="{{ $lop->MaLopHocPhan }}" {{ old('classId')==$lop->MaLopHocPhan ? 'selected'
                                : '' }}>
                                {{ $lop->TenLopHocPhan }}
                            </option>
                            @endforeach
                        </select>
                        @error('classId')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="startTime" class="form-label">Thời gian bắt đầu</label>
                        <input type="datetime-local" class="form-control @error('startTime') is-invalid @enderror"
                            id="startTime" name="startTime" value="{{ old('startTime') }}" required>
                        @error('startTime')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="endTime" class="form-label">Thời gian kết thúc</label>
                        <input type="datetime-local" class="form-control @error('endTime') is-invalid @enderror"
                            id="endTime" name="endTime" value="{{ old('endTime') }}" required>
                        @error('endTime')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                            name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="thoiGianLamBai" class="form-label">Thời gian làm bài (phút) <span
                                class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('thoiGianLamBai') is-invalid @enderror"
                            id="thoiGianLamBai" name="thoiGianLamBai" value="{{ old('thoiGianLamBai', 60) }}" min="15"
                            max="180" required>
                        <div class="form-text">Thời gian làm bài từ 15 đến 180 phút</div>
                        @error('thoiGianLamBai')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="choPhepXemKetQua" class="form-label">Cho phép xem kết quả <span
                                class="text-danger">*</span></label>
                        <select class="form-select @error('choPhepXemKetQua') is-invalid @enderror"
                            id="choPhepXemKetQua" name="choPhepXemKetQua" required>
                            <option value="1" {{ old('choPhepXemKetQua', '1' )=='1' ? 'selected' : '' }}>Có</option>
                            <option value="0" {{ old('choPhepXemKetQua')=='0' ? 'selected' : '' }}>Không</option>
                        </select>
                        @error('choPhepXemKetQua')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status"
                            required>
                            <option value="1" {{ old('status', '1' )=='1' ? 'selected' : '' }}>Hiện</option>
                            <option value="0" {{ old('status')=='0' ? 'selected' : '' }}>Ẩn</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="button" class="btn btn-primary" id="nextStepBtn">Tiếp tục</button>
                </div>

                <!-- Bước 2: Thêm câu hỏi -->
                <div id="step2">
                    <div class="mb-3">
                        <label class="form-label">Danh sách câu hỏi (<span id="questionCount">1</span> câu hỏi)</label>
                        <div id="questionsContainer">
                            <div class="question-group mb-3">
                                <button type="button" class="btn btn-danger btn-sm remove-btn"
                                    onclick="removeQuestion(this)">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <h6 class="mb-2">Câu 1</h6>
                                <div class="mb-3">
                                    <label class="form-label">Nội dung câu hỏi</label>
                                    <textarea class="form-control @error('questions.0.cauHoi') is-invalid @enderror"
                                        name="questions[0][cauHoi]" required>{{ old('questions.0.cauHoi') }}</textarea>
                                    @error('questions.0.cauHoi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <div class="answer-row">
                                        <div class="flex-fill me-2">
                                            <label class="form-label">Đáp án A</label>
                                            <input type="text"
                                                class="form-control @error('questions.0.dapAnA') is-invalid @enderror"
                                                name="questions[0][dapAnA]" value="{{ old('questions.0.dapAnA') }}"
                                                required>
                                            @error('questions.0.dapAnA')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="flex-fill">
                                            <label class="form-label">Đáp án B</label>
                                            <input type="text"
                                                class="form-control @error('questions.0.dapAnB') is-invalid @enderror"
                                                name="questions[0][dapAnB]" value="{{ old('questions.0.dapAnB') }}"
                                                required>
                                            @error('questions.0.dapAnB')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="answer-row">
                                        <div class="flex-fill me-2">
                                            <label class="form-label">Đáp án C</label>
                                            <input type="text"
                                                class="form-control @error('questions.0.dapAnC') is-invalid @enderror"
                                                name="questions[0][dapAnC]" value="{{ old('questions.0.dapAnC') }}"
                                                required>
                                            @error('questions.0.dapAnC')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="flex-fill">
                                            <label class="form-label">Đáp án D</label>
                                            <input type="text"
                                                class="form-control @error('questions.0.dapAnD') is-invalid @enderror"
                                                name="questions[0][dapAnD]" value="{{ old('questions.0.dapAnD') }}"
                                                required>
                                            @error('questions.0.dapAnD')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Đáp án đúng</label>
                                    <select class="form-select @error('questions.0.dapAnDung') is-invalid @enderror"
                                        name="questions[0][dapAnDung]" required>
                                        <option value="">Chọn đáp án đúng</option>
                                        <option value="A" {{ old('questions.0.dapAnDung')=='A' ? 'selected' : '' }}>A
                                        </option>
                                        <option value="B" {{ old('questions.0.dapAnDung')=='B' ? 'selected' : '' }}>B
                                        </option>
                                        <option value="C" {{ old('questions.0.dapAnDung')=='C' ? 'selected' : '' }}>C
                                        </option>
                                        <option value="D" {{ old('questions.0.dapAnDung')=='D' ? 'selected' : '' }}>D
                                        </option>
                                    </select>
                                    @error('questions.0.dapAnDung')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary mb-3" id="addQuestionBtn">
                            <i class="fas fa-plus"></i> Thêm câu hỏi
                        </button>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Lưu</button>
                        <a href="{{ route('giangvien.bai-kiem-tra.danh-sach') }}" class="btn btn-secondary">Hủy</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Xử lý form validation
    (function () {
        'use strict';
        var forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();

    // Chuyển bước từ 1 sang 2
    document.getElementById('nextStepBtn').addEventListener('click', function () {
        const step1Fields = document.querySelectorAll('#step1 [required]');
        let isValid = true;
        step1Fields.forEach(field => {
            if (!field.value) {
                isValid = false;
                field.classList.add('is-invalid');
            } else {
                field.classList.remove('is-invalid');
            }
        });
        if (isValid) {
            document.getElementById('step1').style.display = 'none';
            document.getElementById('step2').style.display = 'block';
        }
    });

    // Thêm câu hỏi mới
    let questionCount = 1;
    document.getElementById('addQuestionBtn').addEventListener('click', function () {
        questionCount++;
        document.getElementById('questionCount').textContent = questionCount;
        const container = document.getElementById('questionsContainer');
        const newQuestion = document.createElement('div');
        newQuestion.className = 'question-group mb-3';
        newQuestion.innerHTML = `
            <button type="button" class="btn btn-danger btn-sm remove-btn" onclick="removeQuestion(this)">
                <i class="fas fa-trash"></i>
            </button>
            <h6 class="mb-2">Câu ${questionCount}</h6>
            <div class="mb-3">
                <label class="form-label">Nội dung câu hỏi</label>
                <textarea class="form-control" name="questions[${questionCount - 1}][cauHoi]" required></textarea>
                <div class="invalid-feedback">Vui lòng nhập nội dung câu hỏi.</div>
            </div>
            <div class="mb-3">
                <div class="answer-row">
                    <div class="flex-fill me-2">
                        <label class="form-label">Đáp án A</label>
                        <input type="text" class="form-control" name="questions[${questionCount - 1}][dapAnA]" required>
                        <div class="invalid-feedback">Vui lòng nhập đáp án A.</div>
                    </div>
                    <div class="flex-fill">
                        <label class="form-label">Đáp án B</label>
                        <input type="text" class="form-control" name="questions[${questionCount - 1}][dapAnB]" required>
                        <div class="invalid-feedback">Vui lòng nhập đáp án B.</div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <div class="answer-row">
                    <div class="flex-fill me-2">
                        <label class="form-label">Đáp án C</label>
                        <input type="text" class="form-control" name="questions[${questionCount - 1}][dapAnC]" required>
                        <div class="invalid-feedback">Vui lòng nhập đáp án C.</div>
                    </div>
                    <div class="flex-fill">
                        <label class="form-label">Đáp án D</label>
                        <input type="text" class="form-control" name="questions[${questionCount - 1}][dapAnD]" required>
                        <div class="invalid-feedback">Vui lòng nhập đáp án D.</div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Đáp án đúng</label>
                <select class="form-select" name="questions[${questionCount - 1}][dapAnDung]" required>
                    <option value="">Chọn đáp án đúng</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
                <div class="invalid-feedback">Vui lòng chọn đáp án đúng.</div>
            </div>
        `;
        container.appendChild(newQuestion);
    });

    // Xóa câu hỏi
    function removeQuestion(button) {
        button.parentElement.remove();
        questionCount--;
        document.getElementById('questionCount').textContent = questionCount;
        const questions = document.querySelectorAll('#questionsContainer .question-group');
        questions.forEach((q, index) => {
            q.querySelector('h6').textContent = `Câu ${index + 1}`;
            q.querySelector('textarea').name = `questions[${index}][cauHoi]`;
            q.querySelectorAll('input')[0].name = `questions[${index}][dapAnA]`;
            q.querySelectorAll('input')[1].name = `questions[${index}][dapAnB]`;
            q.querySelectorAll('input')[2].name = `questions[${index}][dapAnC]`;
            q.querySelectorAll('input')[3].name = `questions[${index}][dapAnD]`;
            q.querySelector('select').name = `questions[${index}][dapAnDung]`;
        });
    }
</script>
@endsection