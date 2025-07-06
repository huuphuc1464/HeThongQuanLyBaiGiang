@extends('layouts.teacherLayout')

@section('title', 'Giảng viên - Sửa bài kiểm tra')
@section('tenTrang', 'Sửa bài kiểm tra')

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
        flex-wrap: wrap;
        gap: 15px;
    }

    .answer-row .flex-fill {
        flex: 1;
        min-width: 200px;
    }

    #step2 {
        display: none;
    }

    .invalid-feedback {
        display: none;
    }

    .was-validated .invalid-feedback,
    .is-invalid .invalid-feedback {
        display: block;
    }

</style>
@endsection

@section('content')
<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">Sửa bài kiểm tra: {{ $baiKiemTra->TenBaiKiemTra }}</h4>
        </div>
        <div class="card-body">
            @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('errorSystem'))
            <div class="alert alert-danger">{{ session('errorSystem') }}</div>
            @endif
            @if ($soLuongSinhVien > 0)
            <div class="alert alert-warning">
                Không thể sửa bài kiểm tra vì đã có {{ $soLuongSinhVien }} sinh viên làm bài.
                <a href="{{ route('giangvien.bai-kiem-tra.danh-sach') }}" class="btn btn-secondary mt-2">Quay lại</a>
            </div>
            @else
            <form id="editQuizForm" action="{{ route('giangvien.bai-kiem-tra.sua', $baiKiemTra->MaBaiKiemTra) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')
                <!-- Bước 1: Thông tin bài kiểm tra -->
                <div id="step1">
                    <div class="mb-3">
                        <label for="quizName" class="form-label">Tên bài kiểm tra <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('quizName') is-invalid @enderror" id="quizName" name="quizName" value="{{ old('quizName', $baiKiemTra->TenBaiKiemTra) }}" required>
                        @error('quizName')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="classSelect" class="form-label">Lớp học phần <span class="text-danger">*</span></label>
                        <select class="form-select @error('classId') is-invalid @enderror" id="classSelect" name="classId" required>
                            <option value="">Chọn lớp học phần</option>
                            @foreach ($lopHocPhan as $lop)
                            <option value="{{ $lop->MaLopHocPhan }}" {{ old('classId', $baiKiemTra->MaLopHocPhan) ==
                                $lop->MaLopHocPhan ? 'selected' : '' }}>
                                {{ $lop->TenLopHocPhan }}
                            </option>
                            @endforeach
                        </select>
                        @error('classId')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="startTime" class="form-label">Thời gian bắt đầu <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control @error('startTime') is-invalid @enderror" id="startTime" name="startTime" value="{{ old('startTime', \Carbon\Carbon::parse($baiKiemTra->ThoiGianBatDau)->format('Y-m-d\TH:i')) }}" required>
                        @error('startTime')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="endTime" class="form-label">Thời gian kết thúc <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control @error('endTime') is-invalid @enderror" id="endTime" name="endTime" value="{{ old('endTime', \Carbon\Carbon::parse($baiKiemTra->ThoiGianKetThuc)->format('Y-m-d\TH:i')) }}" required>
                        @error('endTime')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $baiKiemTra->MoTa) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="thoiGianLamBai" class="form-label">Thời gian làm bài (phút) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('thoiGianLamBai') is-invalid @enderror" id="thoiGianLamBai" name="thoiGianLamBai" value="{{ old('thoiGianLamBai', $baiKiemTra->ThoiGianLamBai) }}" min="15" max="180" required>
                        <div class="form-text">Thời gian làm bài từ 15 đến 180 phút</div>
                        @error('thoiGianLamBai')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="choPhepXemKetQua" class="form-label">Cho phép xem kết quả <span class="text-danger">*</span></label>
                        <select class="form-select @error('choPhepXemKetQua') is-invalid @enderror" id="choPhepXemKetQua" name="choPhepXemKetQua" required>
                            <option value="1" {{ old('choPhepXemKetQua', $baiKiemTra->ChoPhepXemKetQua) ? 'selected' :
                                '' }}>Có</option>
                            <option value="0" {{ old('choPhepXemKetQua', $baiKiemTra->ChoPhepXemKetQua) ? '' :
                                'selected' }}>Không</option>
                        </select>
                        @error('choPhepXemKetQua')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="1" {{ old('status', $baiKiemTra->TrangThai) == 1 ? 'selected' : '' }}>Hiện
                            </option>
                            <option value="0" {{ old('status', $baiKiemTra->TrangThai) == 0 ? 'selected' : '' }}>Ẩn
                            </option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="button" class="btn btn-primary" id="nextStepBtn">Tiếp tục</button>
                    <a href="{{ route('giangvien.bai-kiem-tra.danh-sach') }}" class="btn btn-secondary">Hủy</a>
                </div>

                <!-- Bước 2: Sửa câu hỏi -->
                <div id="step2">
                    <div class="mb-3">
                        <label class="form-label">Danh sách câu hỏi (<span id="questionCount">{{ $cauHois->count()
                                }}</span> câu hỏi)</label>
                        <div id="questionsContainer">
                            @foreach ($cauHois as $index => $cauHoi)
                            <div class="question-group mb-3">
                                <button type="button" class="btn btn-danger btn-sm remove-btn" onclick="removeQuestion(this)">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <h6 class="mb-2">Câu {{ $index + 1 }}</h6>
                                <div class="mb-3">
                                    <label class="form-label">Nội dung câu hỏi <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('questions.' . $index . '.cauHoi') is-invalid @enderror" name="questions[{{ $index }}][cauHoi]" required rows="3">{{ old('questions.' . $index . '.cauHoi', $cauHoi->CauHoi) }}</textarea>
                                    @error('questions.' . $index . '.cauHoi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <div class="answer-row">
                                        <div class="flex-fill me-2">
                                            <label class="form-label">Đáp án A <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('questions.' . $index . '.dapAnA') is-invalid @enderror" name="questions[{{ $index }}][dapAnA]" value="{{ old('questions.' . $index . '.dapAnA', $cauHoi->DapAnA) }}" required>
                                            @error('questions.' . $index . '.dapAnA')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="flex-fill">
                                            <label class="form-label">Đáp án B <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('questions.' . $index . '.dapAnB') is-invalid @enderror" name="questions[{{ $index }}][dapAnB]" value="{{ old('questions.' . $index . '.dapAnB', $cauHoi->DapAnB) }}" required>
                                            @error('questions.' . $index . '.dapAnB')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="answer-row">
                                        <div class="flex-fill me-2">
                                            <label class="form-label">Đáp án C <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('questions.' . $index . '.dapAnC') is-invalid @enderror" name="questions[{{ $index }}][dapAnC]" value="{{ old('questions.' . $index . '.dapAnC', $cauHoi->DapAnC) }}" required>
                                            @error('questions.' . $index . '.dapAnC')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="flex-fill">
                                            <label class="form-label">Đáp án D <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('questions.' . $index . '.dapAnD') is-invalid @enderror" name="questions[{{ $index }}][dapAnD]" value="{{ old('questions.' . $index . '.dapAnD', $cauHoi->DapAnD) }}" required>
                                            @error('questions.' . $index . '.dapAnD')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Đáp án đúng <span class="text-danger">*</span></label>
                                    <select class="form-select @error('questions.' . $index . '.dapAnDung') is-invalid @enderror" name="questions[{{ $index }}][dapAnDung]" required>
                                        <option value="">Chọn đáp án đúng</option>
                                        <option value="A" {{ old('questions.' . $index . '.dapAnDung' , $cauHoi->
                                            DapAnDung) == 'A' ? 'selected' : '' }}>A</option>
                                        <option value="B" {{ old('questions.' . $index . '.dapAnDung' , $cauHoi->
                                            DapAnDung) == 'B' ? 'selected' : '' }}>B</option>
                                        <option value="C" {{ old('questions.' . $index . '.dapAnDung' , $cauHoi->
                                            DapAnDung) == 'C' ? 'selected' : '' }}>C</option>
                                        <option value="D" {{ old('questions.' . $index . '.dapAnDung' , $cauHoi->
                                            DapAnDung) == 'D' ? 'selected' : '' }}>D</option>
                                    </select>
                                    @error('questions.' . $index . '.dapAnDung')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-secondary mb-3" id="addQuestionBtn">
                            <i class="fas fa-plus"></i> Thêm câu hỏi
                        </button>
                        <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#uploadExcelModal">
                            <i class="fas fa-file-excel"></i> Import câu hỏi
                        </button>
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-outline-primary me-2" id="prevStepBtn">Quay lại</button>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                        <a href="{{ route('giangvien.bai-kiem-tra.danh-sach') }}" class="btn btn-secondary">Hủy</a>
                    </div>
                </div>
            </form>
            @endif
        </div>
    </div>
</div>

{{-- Modal import  --}}
<div class="modal fade" id="uploadExcelModal" tabindex="-1" aria-labelledby="uploadExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadExcelModalLabel">Import câu hỏi từ file excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <form id="excelUploadForm">
                    <input type="file" name="excel_file" id="excelFileInput" accept=".xlsx" class="form-control" required />
                </form>
                <div id="uploadStatus" class="mt-2 text-danger" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" id="uploadExcelBtn" class="btn btn-success">Gửi</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Xử lý form validation
    (function() {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();

    // Chuyển bước từ 1 sang 2
    document.getElementById('nextStepBtn').addEventListener('click', function() {
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

    // Quay lại bước 1 từ bước 2
    document.getElementById('prevStepBtn').addEventListener('click', function() {
        document.getElementById('step2').style.display = 'none';
        document.getElementById('step1').style.display = 'block';
    });

    // Thêm câu hỏi mới
    document.getElementById('addQuestionBtn').addEventListener('click', function() {
        addQuestion();
    });

    // Hàm thêm câu hỏi
    function addQuestion(data = {}) {
        const container = document.getElementById('questionsContainer');
        const questionIndex = container.querySelectorAll('.question-group').length;

        const html = `
    <div class="question-group mb-3">
        <button type="button" class="btn btn-danger btn-sm remove-btn" onclick="removeQuestion(this)">
            <i class="fas fa-trash"></i>
        </button>
        <h6 class="mb-2">Câu ${questionIndex + 1}</h6>

        <div class="mb-3">
            <label class="form-label">Nội dung câu hỏi</label>
            <textarea class="form-control" name="questions[${questionIndex}][cauHoi]" rows="3" required>${data.cauHoi || ''}</textarea>
            <div class="invalid-feedback">Vui lòng nhập nội dung câu hỏi.</div>
        </div>

        <div class="mb-3">
            <div class="answer-row">
                <div class="flex-fill me-2">
                    <label class="form-label">Đáp án A</label>
                    <input type="text" class="form-control" name="questions[${questionIndex}][dapAnA]" required value="${data.dapAnA || ''}">
                    <div class="invalid-feedback">Vui lòng nhập đáp án A.</div>
                </div>
                <div class="flex-fill">
                    <label class="form-label">Đáp án B</label>
                    <input type="text" class="form-control" name="questions[${questionIndex}][dapAnB]" required value="${data.dapAnB || ''}">
                    <div class="invalid-feedback">Vui lòng nhập đáp án B.</div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <div class="answer-row">
                <div class="flex-fill me-2">
                    <label class="form-label">Đáp án C</label>
                    <input type="text" class="form-control" name="questions[${questionIndex}][dapAnC]" required value="${data.dapAnC || ''}">
                    <div class="invalid-feedback">Vui lòng nhập đáp án C.</div>
                </div>
                <div class="flex-fill">
                    <label class="form-label">Đáp án D</label>
                    <input type="text" class="form-control" name="questions[${questionIndex}][dapAnD]" required value="${data.dapAnD || ''}">
                    <div class="invalid-feedback">Vui lòng nhập đáp án D.</div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Đáp án đúng</label>
            <select class="form-select" name="questions[${questionIndex}][dapAnDung]" required>
                <option value="">Chọn đáp án đúng</option>
                <option value="A" ${data.dapAnDung==='A' ? 'selected' : '' }>A</option>
                <option value="B" ${data.dapAnDung==='B' ? 'selected' : '' }>B</option>
                <option value="C" ${data.dapAnDung==='C' ? 'selected' : '' }>C</option>
                <option value="D" ${data.dapAnDung==='D' ? 'selected' : '' }>D</option>
            </select>
            <div class="invalid-feedback">Vui lòng chọn đáp án đúng.</div>
        </div>
    </div>
    `;

        container.insertAdjacentHTML('beforeend', html);
        reindexQuestions();
    }

    // Xóa câu hỏi
    function removeQuestion(button) {
        const question = button.closest('.question-group');
        question.remove();
        reindexQuestions();
    }

    // Cập nhật lại thứ tự và name[] sau mỗi lần thêm/xoá
    function reindexQuestions() {
        const questions = document.querySelectorAll('#questionsContainer .question-group');
        document.getElementById('questionCount').textContent = questions.length;

        questions.forEach((q, index) => {
            q.querySelector('h6').textContent = `Câu ${index + 1}`;
            q.querySelector('textarea').name = `questions[${index}][cauHoi]`;

            const inputs = q.querySelectorAll('input');
            if (inputs.length >= 4) {
                inputs[0].name = `questions[${index}][dapAnA]`;
                inputs[1].name = `questions[${index}][dapAnB]`;
                inputs[2].name = `questions[${index}][dapAnC]`;
                inputs[3].name = `questions[${index}][dapAnD]`;
            }

            const select = q.querySelector('select');
            if (select) {
                select.name = `questions[${index}][dapAnDung]`;
            }
        });
    }

    document.getElementById('uploadExcelBtn').addEventListener('click', function() {
        const input = document.getElementById('excelFileInput');
        const statusDiv = document.getElementById('uploadStatus');

        // Reset thông báo
        statusDiv.style.display = 'none';
        statusDiv.textContent = '';

        if (!input.files.length) {
            statusDiv.textContent = 'Vui lòng chọn file Excel!';
            statusDiv.style.display = 'block';
            return;
        }

        const formData = new FormData();
        formData.append('excel_file', input.files[0]);

        fetch('../import-cau-hoi', {
                method: 'POST'
                , headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
                , body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    data.questions.forEach(q => addQuestion(q));
                    document.getElementById('excelUploadForm').reset();

                    const modal = bootstrap.Modal.getInstance(document.getElementById('uploadExcelModal'));
                    modal.hide();
                } else {
                    statusDiv.textContent = data.message || 'Đã xảy ra lỗi khi đọc file';
                    statusDiv.style.display = 'block';
                }
            })
            .catch(error => {
                console.error(error);
                statusDiv.textContent = 'Lỗi kết nối đến máy chủ.';
                statusDiv.style.display = 'block';
            });
    });

</script>
@endsection
