<div id="binh-luan-realtime" class="binh-luan-container" data-ma-bai="{{ $bai->MaBai ?? $baiHoc->MaBai ?? '' }}"
    data-ma-nguoi-dung="{{ auth()->id() }}">>
    <!-- Form gửi bình luận mới -->
    <div class="binh-luan-form mb-4">
        <h5 class="mb-3">Viết bình luận</h5>
        <form @submit.prevent="guiBinhLuan">
            <div class="form-group">
                <textarea id="noiDung" v-model="noiDung" class="form-control tinymce-editor"
                    placeholder="Viết bình luận của bạn..." rows="4"></textarea>
            </div>
            <div class="form-group mt-3">
                <button type="submit" class="btn btn-primary" :disabled="dangTai">
                    <i class="fas fa-paper-plane"></i> Gửi bình luận
                </button>
            </div>
        </form>
    </div>

    <!-- Bộ lọc và sắp xếp -->
    <div class="binh-luan-filter mb-3">
        <div class="row">
            <div class="col-md-6">
                <label for="sapXep" class="form-label">Sắp xếp theo:</label>
                <select id="sapXep" v-model="sapXep" @change="thayDoiSapXep" class="form-select">
                    <option value="moi_nhat">Mới nhất</option>
                    <option value="cu_nhat">Cũ nhất</option>
                    <option value="nhieu_upvote">Nhiều upvote nhất</option>
                    <option value="it_upvote">Ít upvote nhất</option>
                </select>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end align-items-end h-100">
                    <span class="text-muted">@{{ binhLuans.length }} bình luận</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading -->
    <div v-if="dangTai" class="text-center py-4">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Đang tải...</span>
        </div>
    </div>

    <!-- Danh sách bình luận -->
    <div v-if="!dangTai && binhLuans.length === 0" class="text-center py-4">
        <p class="text-muted">Chưa có bình luận nào. Hãy là người đầu tiên bình luận!</p>
    </div>

    <div v-if="!dangTai && binhLuans.length > 0" class="binh-luan-list">
        <div v-for="binhLuan in binhLuans" :key="binhLuan.MaBinhLuan" class="binh-luan-item mb-4">
            <div class="card">
                <div class="card-body">
                    <!-- Header bình luận -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center">
                            <img :src="binhLuan.nguoiGui.AnhDaiDien || '/AnhDaiDien/default-avatar.png'"
                                :alt="binhLuan.nguoiGui.HoTen" class="rounded-circle me-2"
                                style="width: 40px; height: 40px; object-fit: cover;">
                            <div>
                                <h6 class="mb-0">@{{ binhLuan.nguoiGui.HoTen }}</h6>
                                <small class="text-muted">@{{ formatThoiGian(binhLuan.created_at) }}</small>
                                <span v-if="binhLuan.DaChinhSua" class="badge bg-secondary ms-2">Đã chỉnh sửa</span>
                            </div>
                        </div>

                        <!-- Menu tùy chọn -->
                        <div class="dropdown" v-if="kiemTraQuyenChinhSua(binhLuan)">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" @click.prevent="moFormChinhSua(binhLuan)">
                                        <i class="fas fa-edit"></i> Chỉnh sửa
                                    </a></li>
                                <li><a class="dropdown-item text-danger" href="#"
                                        @click.prevent="xoaBinhLuan(binhLuan.MaBinhLuan)">
                                        <i class="fas fa-trash"></i> Xóa
                                    </a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Nội dung bình luận -->
                    <div class="binh-luan-content mb-3">
                        <div v-html="binhLuan.NoiDung"></div>
                    </div>

                    <!-- Vote buttons -->
                    <div class="d-flex align-items-center mb-3">
                        <button @click="vote(binhLuan.MaBinhLuan, 'upvote')" class="btn btn-sm me-2"
                            :class="binhLuan.daUpvote ? 'btn-success' : 'btn-outline-success'">
                            <i class="fas fa-thumbs-up"></i> @{{ binhLuan.SoUpvote }}
                        </button>
                        <button @click="vote(binhLuan.MaBinhLuan, 'downvote')" class="btn btn-sm me-3"
                            :class="binhLuan.daDownvote ? 'btn-danger' : 'btn-outline-danger'">
                            <i class="fas fa-thumbs-down"></i> @{{ binhLuan.SoDownvote }}
                        </button>

                        <button @click="moFormTraLoi(binhLuan)" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-reply"></i> Trả lời
                        </button>
                    </div>

                    <!-- Form trả lời -->
                    <div v-if="binhLuanDangTraLoi && binhLuanDangTraLoi.MaBinhLuan === binhLuan.MaBinhLuan"
                        class="tra-loi-form mt-3">
                        <div class="card border-primary">
                            <div class="card-body">
                                <h6 class="card-title">Trả lời bình luận</h6>
                                <div class="form-group">
                                    <textarea id="noiDungTraLoi" v-model="noiDungTraLoi"
                                        class="form-control tinymce-editor" placeholder="Viết phản hồi của bạn..."
                                        rows="3"></textarea>
                                </div>
                                <div class="mt-3">
                                    <button @click="traLoiBinhLuan" class="btn btn-primary btn-sm me-2">
                                        <i class="fas fa-paper-plane"></i> Gửi phản hồi
                                    </button>
                                    <button @click="huyTraLoi" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-times"></i> Hủy
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bình luận con -->
                    <div v-if="binhLuan.binh_luan_con && binhLuan.binh_luan_con.length > 0" class="binh-luan-con mt-3">
                        <div v-for="traLoi in binhLuan.binh_luan_con" :key="traLoi.MaBinhLuan"
                            class="tra-loi-item ms-4 mb-3">
                            <div class="card border-light">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="d-flex align-items-center">
                                            <img :src="traLoi.nguoiGui.AnhDaiDien || '/AnhDaiDien/default-avatar.png'"
                                                :alt="traLoi.nguoiGui.HoTen" class="rounded-circle me-2"
                                                style="width: 30px; height: 30px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0 small">@{{ traLoi.nguoiGui.HoTen }}</h6>
                                                <small class="text-muted">@{{ formatThoiGian(traLoi.created_at)
                                                    }}</small>
                                            </div>
                                        </div>

                                        <div v-if="kiemTraQuyenChinhSua(traLoi)" class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item text-danger" href="#"
                                                        @click.prevent="xoaBinhLuan(traLoi.MaBinhLuan)">
                                                        <i class="fas fa-trash"></i> Xóa
                                                    </a></li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="mt-2">
                                        <div v-html="traLoi.NoiDung"></div>
                                    </div>

                                    <div class="d-flex align-items-center mt-2">
                                        <button @click="vote(traLoi.MaBinhLuan, 'upvote')" class="btn btn-sm me-2"
                                            :class="traLoi.daUpvote ? 'btn-success' : 'btn-outline-success'">
                                            <i class="fas fa-thumbs-up"></i> @{{ traLoi.SoUpvote }}
                                        </button>
                                        <button @click="vote(traLoi.MaBinhLuan, 'downvote')" class="btn btn-sm"
                                            :class="traLoi.daDownvote ? 'btn-danger' : 'btn-outline-danger'">
                                            <i class="fas fa-thumbs-down"></i> @{{ traLoi.SoDownvote }}
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

    <!-- Form chỉnh sửa bình luận -->
    <div v-if="binhLuanDangChinhSua" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chỉnh sửa bình luận</h5>
                    <button type="button" class="btn-close" @click="huyChinhSua"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="noiDungChinhSua" class="form-label">Nội dung:</label>
                        <textarea id="noiDungChinhSua" v-model="noiDungChinhSua" class="form-control tinymce-editor"
                            rows="4"></textarea>
                    </div>
                    <div class="form-group mt-3">
                        <label for="lyDoChinhSua" class="form-label">Lý do chỉnh sửa (tùy chọn):</label>
                        <input type="text" id="lyDoChinhSua" v-model="lyDoChinhSua" class="form-control"
                            placeholder="Ví dụ: Sửa lỗi chính tả, bổ sung thông tin...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="huyChinhSua">Hủy</button>
                    <button type="button" class="btn btn-primary" @click="chinhSuaBinhLuan">Lưu thay đổi</button>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    </div>
</div>

<style>
    .binh-luan-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .binh-luan-item {
        transition: all 0.3s ease;
    }

    .binh-luan-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .tra-loi-item {
        border-left: 3px solid #e9ecef;
        padding-left: 15px;
    }

    .tinymce-editor {
        min-height: 120px;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .modal.show {
        background-color: rgba(0, 0, 0, 0.5);
    }
</style>

<script>
    // Khởi tạo TinyMCE cho các textarea
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: '.tinymce-editor',
                height: 200,
                menubar: false,
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | blocks | ' +
                    'bold italic forecolor | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'removeformat | help',
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
                language: 'vi',
                setup: function (editor) {
                    editor.on('change', function () {
                        // Cập nhật v-model khi nội dung thay đổi
                        const textarea = editor.getElement();
                        if (textarea) {
                            const event = new Event('input', { bubbles: true });
                            textarea.dispatchEvent(event);
                        }
                    });
                }
            });
        }
    });
</script>