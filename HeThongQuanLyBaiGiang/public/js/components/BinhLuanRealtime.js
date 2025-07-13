// Component Vue.js cho bình luận realtime
console.log('object');
const BinhLuanRealtime = {
    template: `
    <div class="binh-luan-container">
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
                        <span class="text-muted">{{ binhLuans.length }} bình luận</span>
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
                               <img
                                    :src="getAvatarUrl(binhLuan.nguoi_gui)"
                                    :alt="binhLuan.nguoi_gui ? binhLuan.nguoi_gui.HoTen : 'Người dùng'"
                                    class="rounded-circle me-2"
                                    style="width: 40px; height: 40px; object-fit: cover;"
                                    >
                                <div>
                                    <h6 class="mb-0">{{ binhLuan.nguoi_gui ? binhLuan.nguoi_gui.HoTen : 'Người dùng' }}</h6>
                                    <small class="text-muted">{{ formatThoiGian(binhLuan.created_at) }}</small>
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
                                <i class="fas fa-thumbs-up"></i> {{ binhLuan.SoUpvote }}
                            </button>
                            <button @click="vote(binhLuan.MaBinhLuan, 'downvote')" class="btn btn-sm me-3"
                                :class="binhLuan.daDownvote ? 'btn-danger' : 'btn-outline-danger'">
                                <i class="fas fa-thumbs-down"></i> {{ binhLuan.SoDownvote }}
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
                        <div v-if="binhLuan.binhLuanCon && binhLuan.binhLuanCon.length > 0" class="binh-luan-con mt-3">
                            <div v-for="traLoi in binhLuan.binhLuanCon" :key="traLoi.MaBinhLuan"
                                class="tra-loi-item ms-4 mb-3">
                                <div class="card border-light">
                                    <div class="card-body py-2">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="d-flex align-items-center">
                                                <img :src="getAvatarUrl(traLoi.nguoi_gui)"
                                                    :alt="traLoi.nguoi_gui ? traLoi.nguoi_gui.HoTen : 'Người dùng'" class="rounded-circle me-2"
                                                    style="width: 30px; height: 30px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0 small">{{ traLoi.nguoi_gui ? traLoi.nguoi_gui.HoTen : 'Người dùng' }}</h6>
                                                    <small class="text-muted">{{ formatThoiGian(traLoi.created_at) }}</small>
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
                                                <i class="fas fa-thumbs-up"></i> {{ traLoi.SoUpvote }}
                                            </button>
                                            <button @click="vote(traLoi.MaBinhLuan, 'downvote')" class="btn btn-sm"
                                                :class="traLoi.daDownvote ? 'btn-danger' : 'btn-outline-danger'">
                                                <i class="fas fa-thumbs-down"></i> {{ traLoi.SoDownvote }}
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
    `,
    data() {
        return {
            binhLuans: [],
            noiDung: '',
            noiDungTraLoi: '',
            binhLuanDangTraLoi: null,
            binhLuanDangChinhSua: null,
            noiDungChinhSua: '',
            lyDoChinhSua: '',
            sapXep: 'moi_nhat',
            trangHienTai: 1,
            dangTai: false,
            tinymceConfig: {
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
                language_url: '/js/tinymce/langs/vi.js'
            }
        }
    },

    mounted() {
        console.log('1');
        this.taiBinhLuans();
        this.khoiTaoEcho();
        console.log('[DEBUG] Khi mount, binhLuanDangChinhSua:', this.binhLuanDangChinhSua);
    },

    methods: {
        async taiBinhLuans() {
            this.dangTai = true;
            try {
                const response = await fetch(`/binh-luan/danh-sach?MaBai=${this.maBai}&SapXep=${this.sapXep}&page=${this.trangHienTai}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();
                console.log('[DEBUG] Kết quả API bình luận:', data);

                if (data.success) {
                    this.binhLuans = data.binhLuans.data;
                    this.trangHienTai = data.binhLuans.current_page;

                    // Debug: Kiểm tra dữ liệu người dùng
                    if (this.binhLuans.length > 0) {
                        console.log('[DEBUG] Bình luận đầu tiên:', this.binhLuans[0]);
                        console.log('[DEBUG] NguoiGui của bình luận đầu tiên:', this.binhLuans[0].nguoi_gui);
                    }
                }
            } catch (error) {
                console.error('[DEBUG] Lỗi khi tải bình luận:', error);
            } finally {
                this.dangTai = false;
            }
        },

        khoiTaoEcho() {
            // Khởi tạo Echo cho realtime (cần cài đặt Laravel Echo)
            if (window.Echo) {
                window.Echo.channel(`binh-luan-bai-${this.maBai}`)
                    .listen('BinhLuanMoi', (e) => {
                        this.themBinhLuanMoi(e.binhLuan);
                    })
                    .listen('BinhLuanDeleted', (e) => {
                        this.xoaBinhLuanRealtime(e.maBinhLuan);
                    });
                window.Echo.channel('binh-luan-vote')
                    .listen('BinhLuanVoted', (e) => {
                        this.capNhatVoteRealtime(e.maBinhLuan, e.soUpvote, e.soDownvote);
                    });
            }
        },

        capNhatVoteRealtime(maBinhLuan, soUpvote, soDownvote) {
            // Cập nhật số upvote/downvote cho bình luận chính
            let binhLuan = this.binhLuans.find(bl => bl.MaBinhLuan === maBinhLuan);
            if (binhLuan) {
                binhLuan.SoUpvote = soUpvote;
                binhLuan.SoDownvote = soDownvote;
                return;
            }
            // Nếu không phải bình luận chính, tìm trong bình luận con
            for (let bl of this.binhLuans) {
                if (bl.binhLuanCon) {
                    const binhLuanCon = bl.binhLuanCon.find(bc => bc.MaBinhLuan === maBinhLuan);
                    if (binhLuanCon) {
                        binhLuanCon.SoUpvote = soUpvote;
                        binhLuanCon.SoDownvote = soDownvote;
                        break;
                    }
                }
            }
        },

        themBinhLuanMoi(binhLuan) {
            // Thêm bình luận mới vào đầu danh sách
            this.binhLuans.unshift(binhLuan);
        },

        xoaBinhLuanRealtime(maBinhLuan) {
            // Xóa bình luận khỏi danh sách khi nhận được sự kiện realtime
            this.binhLuans = this.binhLuans.filter(bl => bl.MaBinhLuan !== maBinhLuan);

            //  xóa bình luận con nếu có
            for (let bl of this.binhLuans) {
                if (bl.binhLuanCon) {
                    bl.binhLuanCon = bl.binhLuanCon.filter(bc => bc.MaBinhLuan !== maBinhLuan);
                }
            }
        },

        async guiBinhLuan() {
            // Lấy nội dung thực tế từ TinyMCE 
            let noiDungThucTe = this.noiDung;
            if (window.tinymce && window.tinymce.get('noiDung')) {
                noiDungThucTe = window.tinymce.get('noiDung').getContent({ format: 'html' }).trim();
            }
            if (!noiDungThucTe) {
                alert('Vui lòng nhập nội dung bình luận');
                return;
            }
            try {
                const response = await fetch('/binh-luan/gui-binh-luan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        MaBai: this.maBai,
                        NoiDung: noiDungThucTe
                    })
                });
                const data = await response.json();
                if (data.success) {
                    if (data.binhLuan) {
                        this.binhLuans.unshift(data.binhLuan);
                    } else {
                        await this.taiBinhLuans();
                    }
                    this.noiDung = '';
                    if (window.tinymce.get('noiDung')) {
                        window.tinymce.get('noiDung').setContent('');
                    }
                    this.hienThongBao('Đã gửi bình luận thành công', 'success');
                }
            } catch (error) {
                console.error('Lỗi khi gửi bình luận:', error);
                this.hienThongBao('Có lỗi xảy ra khi gửi bình luận', 'error');
            }
        },

        async traLoiBinhLuan() {
            // Lấy nội dung thực tế từ TinyMCE 
            let noiDungThucTe = this.noiDungTraLoi;
            if (window.tinymce && window.tinymce.get('noiDungTraLoi')) {
                noiDungThucTe = window.tinymce.get('noiDungTraLoi').getContent({ format: 'html' }).trim();
            }
            if (!noiDungThucTe) {
                alert('Vui lòng nhập nội dung phản hồi');
                return;
            }
            try {
                const response = await fetch('/binh-luan/tra-loi-binh-luan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        MaBinhLuan: this.binhLuanDangTraLoi.MaBinhLuan,
                        MaBai: this.maBai,
                        NoiDung: noiDungThucTe
                    })
                });
                const data = await response.json();
                if (data.success) {
                    if (data.binhLuan) {
                        const binhLuanCha = this.binhLuans.find(bl => bl.MaBinhLuan === this.binhLuanDangTraLoi.MaBinhLuan);
                        if (binhLuanCha) {
                            if (!binhLuanCha.binhLuanCon) {
                                binhLuanCha.binhLuanCon = [];
                            }
                            binhLuanCha.binhLuanCon.push(data.binhLuan);
                        }
                    } else {
                        await this.taiBinhLuans();
                    }
                    this.noiDungTraLoi = '';
                    this.binhLuanDangTraLoi = null;
                    if (window.tinymce.get('noiDungTraLoi')) {
                        window.tinymce.get('noiDungTraLoi').setContent('');
                    }
                    this.hienThongBao('Đã gửi phản hồi thành công', 'success');
                }
            } catch (error) {
                console.error('Lỗi khi gửi phản hồi:', error);
                this.hienThongBao('Có lỗi xảy ra khi gửi phản hồi', 'error');
            }
        },

        async chinhSuaBinhLuan() {
            if (!this.noiDungChinhSua.trim()) {
                alert('Vui lòng nhập nội dung bình luận');
                return;
            }

            try {
                const response = await fetch('/binh-luan/cap-nhat', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        MaBinhLuan: this.binhLuanDangChinhSua.MaBinhLuan,
                        NoiDung: this.noiDungChinhSua,
                        LyDoChinhSua: this.lyDoChinhSua
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Cập nhật bình luận trong danh sách
                    const index = this.binhLuans.findIndex(bl => bl.MaBinhLuan === this.binhLuanDangChinhSua.MaBinhLuan);
                    if (index !== -1) {
                        this.binhLuans[index] = data.binhLuan;
                    }

                    this.noiDungChinhSua = '';
                    this.lyDoChinhSua = '';
                    this.binhLuanDangChinhSua = null;
                    this.hienThongBao('Đã cập nhật bình luận thành công', 'success');
                }
            } catch (error) {
                console.error('Lỗi khi cập nhật bình luận:', error);
                this.hienThongBao('Có lỗi xảy ra khi cập nhật bình luận', 'error');
            }
        },

        async xoaBinhLuan(maBinhLuan) {
            if (!confirm('Bạn có chắc chắn muốn xóa bình luận này?')) {
                return;
            }

            try {
                const response = await fetch(`/binh-luan/xoa/${maBinhLuan}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Xóa bình luận khỏi danh sách ngay lập tức
                    this.xoaBinhLuanRealtime(maBinhLuan);
                    this.hienThongBao('Đã xóa bình luận thành công', 'success');
                } else {
                    this.hienThongBao(data.message || 'Có lỗi xảy ra khi xóa bình luận', 'error');
                }
            } catch (error) {
                console.error('Lỗi khi xóa bình luận:', error);
                this.hienThongBao('Có lỗi xảy ra khi xóa bình luận', 'error');
            }
        },

        async vote(maBinhLuan, loaiVote) {
            try {
                const response = await fetch('/binh-luan/vote', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        MaBinhLuan: maBinhLuan,
                        LoaiVote: loaiVote
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Tìm và cập nhật bình luận chính
                    let binhLuan = this.binhLuans.find(bl => bl.MaBinhLuan === maBinhLuan);
                    if (binhLuan) {
                        binhLuan.SoUpvote = data.soUpvote;
                        binhLuan.SoDownvote = data.soDownvote;
                        binhLuan.daUpvote = data.daUpvote || false;
                        binhLuan.daDownvote = data.daDownvote || false;
                    } else {
                        // Tìm trong bình luận con
                        for (let bl of this.binhLuans) {
                            if (bl.binhLuanCon) {
                                const binhLuanCon = bl.binhLuanCon.find(bc => bc.MaBinhLuan === maBinhLuan);
                                if (binhLuanCon) {
                                    binhLuanCon.SoUpvote = data.soUpvote;
                                    binhLuanCon.SoDownvote = data.soDownvote;
                                    binhLuanCon.daUpvote = data.daUpvote || false;
                                    binhLuanCon.daDownvote = data.daDownvote || false;
                                    break;
                                }
                            }
                        }
                    }

                    // Không hiển thị thông báo khi vote thành công
                    // this.hienThongBao(data.message, 'success');
                }
            } catch (error) {
                console.error('Lỗi khi vote:', error);
                this.hienThongBao('Có lỗi xảy ra khi vote', 'error');
            }
        },

        moFormTraLoi(binhLuan) {
            this.binhLuanDangTraLoi = binhLuan;
            this.noiDungTraLoi = '';
            this.$nextTick(() => {
                if (window.tinymce.get('noiDungTraLoi')) {
                    window.tinymce.get('noiDungTraLoi').remove();
                }
                window.tinymce.init({
                    selector: '#noiDungTraLoi',
                    ...this.tinymceConfig
                });
            });
        },

        moFormChinhSua(binhLuan) {
            console.log('[DEBUG] Gọi moFormChinhSua với:', binhLuan);
            this.binhLuanDangChinhSua = binhLuan;
            this.noiDungChinhSua = binhLuan.NoiDung;
            this.lyDoChinhSua = '';
            this.$nextTick(() => {
                if (window.tinymce.get('noiDungChinhSua')) {
                    window.tinymce.get('noiDungChinhSua').setContent(binhLuan.NoiDung);
                }
            });
        },

        huyTraLoi() {
            if (window.tinymce.get('noiDungTraLoi')) {
                window.tinymce.get('noiDungTraLoi').remove();
            }
            this.binhLuanDangTraLoi = null;
            this.noiDungTraLoi = '';
        },

        huyChinhSua() {
            console.log('[DEBUG] Gọi huyChinhSua');
            this.binhLuanDangChinhSua = null;
            this.noiDungChinhSua = '';
            this.lyDoChinhSua = '';
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) backdrop.remove();
        },

        thayDoiSapXep() {
            this.trangHienTai = 1;
            this.taiBinhLuans();
        },

        hienThongBao(message, type) {
            // Hiển thị thông báo (có thể sử dụng toast hoặc alert)
            if (type === 'error') {
                alert('Lỗi: ' + message);
            }
        },

        formatThoiGian(thoiGian) {
            const date = new Date(thoiGian);
            const now = new Date();
            const diff = now - date;

            const minutes = Math.floor(diff / 60000);
            const hours = Math.floor(diff / 3600000);
            const days = Math.floor(diff / 86400000);

            if (minutes < 1) return 'Vừa xong';
            if (minutes < 60) return `${minutes} phút trước`;
            if (hours < 24) return `${hours} giờ trước`;
            if (days < 7) return `${days} ngày trước`;

            return date.toLocaleDateString('vi-VN');
        },

        kiemTraQuyenChinhSua(binhLuan) {
            return binhLuan.MaNguoiGui === this.maNguoiDung;
        },

        getAvatarUrl(nguoi) {
            if (!nguoi || !nguoi.AnhDaiDien) {
                return '/AnhDaiDien/default-avatar.png';
            }
            if (nguoi.AnhDaiDien.startsWith('http')) {
                return nguoi.AnhDaiDien;
            }
            return window.location.origin.replace(/\/$/, '') + '/' + nguoi.AnhDaiDien.replace(/^\//, '');
        }
    },

    props: {
        maBai: {
            type: Number,
            required: true
        },
        maNguoiDung: {
            type: Number,
            required: true
        }
    }
};

// Export component
if (typeof module !== 'undefined' && module.exports) {
    module.exports = BinhLuanRealtime;
} else {
    window.BinhLuanRealtime = BinhLuanRealtime;
}