@extends('layouts.studentLayout')

@section('title', 'Ví dụ sử dụng TinyMCE')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Ví dụ sử dụng TinyMCE cho bình luận</h5>
                </div>
                <div class="card-body">

                    <!-- Form bình luận với TinyMCE -->
                    <form id="commentForm">
                        <div class="mb-3">
                            <label for="commentContent" class="form-label">Viết bình luận của bạn:</label>
                            <textarea id="commentContent" name="content" class="form-control tinymce-editor"
                                placeholder="Viết bình luận của bạn..." rows="4"></textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                <span id="charCount">0</span>/1000 ký tự
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Gửi bình luận
                            </button>
                        </div>
                    </form>

                    <hr>

                    <!-- Bình luận mẫu -->
                    <div class="comment-example">
                        <h6>Bình luận mẫu với định dạng:</h6>
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <img src="/AnhDaiDien/default-avatar.png" alt="Avatar" class="rounded-circle me-2"
                                        style="width: 32px; height: 32px;">
                                    <div>
                                        <strong>Nguyễn Văn A</strong>
                                        <small class="text-muted ms-2">2 giờ trước</small>
                                    </div>
                                </div>
                                <div class="comment-content">
                                    <p>Đây là một <strong>bình luận mẫu</strong> với các định dạng:</p>
                                    <ul>
                                        <li><em>In nghiêng</em></li>
                                        <li><strong>In đậm</strong></li>
                                        <li>Danh sách có dấu chấm</li>
                                    </ul>
                                    <p>Bạn có thể sử dụng các công cụ định dạng ở trên để tạo bình luận đẹp hơn!</p>
                                </div>
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-outline-success me-2">
                                        <i class="fas fa-thumbs-up"></i> 5
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger me-2">
                                        <i class="fas fa-thumbs-down"></i> 1
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-reply"></i> Trả lời
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Khởi tạo TinyMCE cho bình luận
        if (typeof TinyMCEHelper !== 'undefined') {
            TinyMCEHelper.initCommentTinyMCE();
        }

        // Xử lý form submit
        document.getElementById('commentForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const content = TinyMCEHelper.getTinyMCEContent('#commentContent');
            if (!content.trim()) {
                alert('Vui lòng nhập nội dung bình luận');
                return;
            }

            // Gửi bình luận (có thể thay bằng AJAX)
            console.log('Nội dung bình luận:', content);
            alert('Bình luận đã được gửi!');

            // Xóa nội dung
            TinyMCEHelper.clearTinyMCE('#commentContent');
        });

        // Đếm ký tự
        if (typeof tinymce !== 'undefined') {
            tinymce.get('#commentContent').on('keyup', function () {
                const content = this.getContent({ format: 'text' });
                document.getElementById('charCount').textContent = content.length;
            });
        }
    });
</script>
@endsection