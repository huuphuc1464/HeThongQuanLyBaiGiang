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


// ===== Dropdown Menu Khoa =====
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
