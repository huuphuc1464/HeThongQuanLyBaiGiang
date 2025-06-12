// ===== Sidebar Toggle =====
const sidebar = document.getElementById('sidebar');
const mainContent = document.getElementById('mainContent');
const toggleSidebarBtn = document.getElementById('anHienSidebarBtn');

toggleSidebarBtn?.addEventListener('click', () => {
    sidebar?.classList.toggle('collapsed');
    mainContent?.classList.toggle('sidebar-collapsed');
});

function autoCollapseSidebar() {
    if (window.innerWidth < 992) {
        sidebar?.classList.add('collapsed');
        mainContent?.classList.add('sidebar-collapsed');
    } else {
        sidebar?.classList.remove('collapsed');
        mainContent?.classList.remove('sidebar-collapsed');
    }
}

window.addEventListener('resize', autoCollapseSidebar);
window.addEventListener('DOMContentLoaded', autoCollapseSidebar);


// ===== Dropdown Submenu =====
document.querySelectorAll('.dropdown-submenu > a').forEach(function (element) {
    element.addEventListener('click', function (e) {
        const nextMenu = this.nextElementSibling;
        if (nextMenu && nextMenu.classList.contains('dropdown-menu')) {
            e.preventDefault();
            e.stopPropagation();
            nextMenu.classList.toggle('show');
        }
    });
});

document.querySelectorAll('.dropdown').forEach(function (dropdown) {
    dropdown.addEventListener('hide.bs.dropdown', function () {
        dropdown.querySelectorAll('.dropdown-menu.show').forEach(function (submenu) {
            submenu.classList.remove('show');
        });
    });
});


// ===== Lớp học phần navbar =====
const classes = [
    { title: 'Lớp A', desc: 'Mô tả A' },
    { title: 'Lớp B', desc: 'Mô tả B' },
    { title: 'Lớp C', desc: 'Mô tả C' },
];

const lophocphanList = document.getElementById('classList');
if (lophocphanList) {
    classes.forEach(cls => {
        lophocphanList.innerHTML += `
        <a href="#" class="nav-item class-course-item d-flex text-decoration-none text-dark">
            <div class="icon-circle">
                <img src="https://placehold.co/25" alt="icon" width="25" height="25" class="rounded-circle" />
            </div>
            <div class="text-group">
                <p class="title mb-1">${cls.title}</p>
                <p class="desc mb-1">${cls.desc}</p>
            </div>
        </a>`;
    });
}


// ===== Lớp học phần main =====
const data = Array(4).fill({
    image: "https://placehold.co/400x200",
    className: "Tên lớp học phần",
    subject: "Tên học phần",
    description: "Mô tả",
    instructor: "Tên giảng viên",
    studentCount: 50
});

const container = document.getElementById("classCardContainer");
if (container) {
    data.forEach(item => {
        container.innerHTML += `
        <div class="col">
            <a href="#" class="text-decoration-none text-dark">
                <div class="card card-class position-relative">
                    <img class="card-img-top" src="${item.image}" alt="Class image">
                    <div class="card-body">
                        <h5 class="card-title mb-1">${item.className}</h5>
                        <h6 class="card-subtitle mb-1 text-muted">${item.subject}</h6>
                        <p class="card-text mb-2">${item.description}</p>
                    </div>
                    <div class="card-footer">
                        <div class="instructor"><img class="anh-giang-vien" src="https://placehold.co/16"><span class="ms-1">${item.instructor}</span></div>
                        <div class="students"><i class="fas fa-users"></i><span>${item.studentCount}</span></div>
                    </div>
                </div>
            </a>
        </div>`;
    });
}


// ===== Toggle danh sách lớp học phần =====
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('toggleClassListBtn');
    const classList = document.getElementById('classList');
    const toggleIcon = document.getElementById('toggleIcon');

    toggleBtn?.addEventListener('click', function () {
        const isHidden = classList.style.display === 'none';
        classList.style.display = isHidden ? 'block' : 'none';
        toggleIcon?.classList.toggle('fa-caret-down', isHidden);
        toggleIcon?.classList.toggle('fa-caret-right', !isHidden);
    });
});
