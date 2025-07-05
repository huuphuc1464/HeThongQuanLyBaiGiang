// Validation form
(function () {
    'use strict';
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                // Highlight câu hỏi chưa chọn
                const invalidQuestions = form.querySelectorAll('.invalid-feedback');
                invalidQuestions.forEach(feedback => {
                    feedback.parentElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                });
            }
            form.classList.add('was-validated');
        }, false);
    });
})();

// Timer
let time = parseInt('{{ $thoiGianConLai }}');
const timerElement = document.getElementById('timer');
const submitBtn = document.getElementById('submitBtn');
const confirmSubmitBtn = document.getElementById('confirmSubmit');
let isTimeUp = false;
let isSubmitting = false;

function disableAllInputs() {
    document.querySelectorAll('#examForm input, #examForm button').forEach(el => el.disabled = true);
}
function showLoading() {
    let loading = document.createElement('div');
    loading.id = 'loadingOverlay';
    loading.style.position = 'fixed';
    loading.style.top = 0;
    loading.style.left = 0;
    loading.style.width = '100vw';
    loading.style.height = '100vh';
    loading.style.background = 'rgba(255,255,255,0.7)';
    loading.style.display = 'flex';
    loading.style.alignItems = 'center';
    loading.style.justifyContent = 'center';
    loading.innerHTML = '<div class="spinner-border text-primary" style="width: 4rem; height: 4rem;"></div><span class="ms-3">Đang nộp bài...</span>';
    document.body.appendChild(loading);
}

const countdown = setInterval(() => {
    if (time <= 0 && !isSubmitting) {
        clearInterval(countdown);
        isTimeUp = true;
        timerElement.textContent = "00:00:00";
        timerElement.style.color = "#e74c3c";
        submitBtn.disabled = true;
        showTimeUpNotification();
        return;
    }
    if (time === 60) {
        alert('Chỉ còn 1 phút làm bài!');
    }
    let hours = Math.floor(time / 3600);
    let minutes = Math.floor((time % 3600) / 60);
    let seconds = time % 60;
    timerElement.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    if (time <= 300) {
        timerElement.style.color = "#e74c3c";
        if (time % 2 === 0) {
            timerElement.style.opacity = "0.5";
        } else {
            timerElement.style.opacity = "1";
        }
    }
    time--;
}, 1000);

function showTimeUpNotification() {
    if (isSubmitting) return;
    isSubmitting = true;
    const form = document.getElementById('examForm');
    const formData = new FormData(form);
    disableAllInputs();
    showLoading();
    alert('Thời gian làm bài đã hết! Bài làm của bạn sẽ được nộp với các đáp án hiện tại.');
    submitExamAndRedirect(window.csrfToken, formData);
}

function submitExamAndRedirect(csrfToken, formData) {
    console.log(isSubmitting);
    console.log(csrfToken);
    for (var pair of formData.entries()) {
        console.log(pair[0] + ', ' + pair[1]);
    }
    if (isSubmitting) {
        const form = document.getElementById('examForm');
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Phản hồi server không thành công: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                const loading = document.getElementById('loadingOverlay');
                if (loading) loading.remove();
                const successModalElement = document.getElementById('successModal');
                if (successModalElement) {
                    var successModal = new bootstrap.Modal(successModalElement);
                    // Ẩn confirmModal trước khi hiển thị successModal
                    var confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
                    if (confirmModal) {
                        confirmModal.hide();
                    }
                    document.querySelector('#successModal .modal-body').innerText = data.message || 'Nộp bài thành công!';
                    successModal.show();
                    setTimeout(function () {
                        window.location.href = data.redirect || "{{ route('danh-sach-bai-kiem-tra') }}";
                    }, 2000);
                } else {
                    alert(data.message || 'Nộp bài thành công!');
                    window.location.href = data.redirect || "{{ route('danh-sach-bai-kiem-tra') }}";
                }
            })
            .catch(error => {
                console.error('Lỗi:', error);
                alert('Có lỗi xảy ra khi nộp bài: ' + error.message);
                const loading = document.getElementById('loadingOverlay');
                if (loading) loading.remove();
                window.location.reload();
            });
    }
}

function beforeUnloadHandler(e) {
    if (!isTimeUp && !isSubmitting) {
        e.preventDefault();
        e.returnValue = '';
    }
}
window.addEventListener('beforeunload', beforeUnloadHandler);

submitBtn.addEventListener('click', function () {
    const form = document.getElementById('examForm');
    if (form.checkValidity()) {
        // Hiển thị modal xác nhận nếu tất cả câu hỏi đã được chọn
        var confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        confirmModal.show();
    } else {
        form.classList.add('was-validated');
        // Tìm câu hỏi đầu tiên chưa chọn và cuộn đến đó
        const firstInvalid = form.querySelector(':invalid');
        if (firstInvalid) {
            firstInvalid.closest('.question-container').scrollIntoView({ behavior: 'smooth', block: 'center' });
            alert('Vui lòng chọn đáp án cho tất cả câu hỏi trước khi nộp bài!');
        }

    }
});



document.addEventListener('visibilitychange', function () {
    if (document.hidden && !isTimeUp && !isSubmitting) {
        console.log('Sinh viên đã chuyển tab!');
    }
});

confirmSubmitBtn.addEventListener('click', function () {
    const form = document.getElementById('examForm');
    if (!isTimeUp && form.checkValidity()) {
        isSubmitting = true;
        const formData = new FormData(form);
        disableAllInputs();
        showLoading();
        window.removeEventListener('beforeunload', beforeUnloadHandler);
        submitExamAndRedirect(window.csrfToken, formData);
    }
});