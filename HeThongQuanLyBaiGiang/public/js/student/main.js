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

document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(location.search);
    const search = urlParams.get('search');
    if (!search) return;

    // Bỏ dấu tiếng Việt
    const normalize = str =>
        str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();

    // Từ khóa và chuẩn hóa
    const keywords = search.trim().split(/\s+/).map(normalize).filter(Boolean);
    if (!keywords.length) return;

    // Hàm highlight từng đoạn văn bản (bên trong HTML)
    function highlightTextNodes(root) {
        const treeWalker = document.createTreeWalker(
            root,
            NodeFilter.SHOW_TEXT,
            {
                acceptNode: node => {
                    // Loại trừ nếu node toàn khoảng trắng hoặc trống
                    if (!node.nodeValue.trim()) return NodeFilter.FILTER_REJECT;
                    return NodeFilter.FILTER_ACCEPT;
                }
            }
        );

        const textNodes = [];
        while (treeWalker.nextNode()) {
            textNodes.push(treeWalker.currentNode);
        }

        textNodes.forEach(textNode => {
            const originalText = textNode.nodeValue;
            const normalizedText = normalize(originalText);

            let replacedHTML = originalText;
            let replaced = false;

            keywords.forEach(keyword => {
                const index = normalizedText.indexOf(keyword);
                if (index !== -1) {
                    const regex = new RegExp(originalText.substr(index, keyword.length), 'gi');
                    replacedHTML = replacedHTML.replace(regex, match => {
                        replaced = true;
                        return `<mark>${match}</mark>`;
                    });
                }
            });

            if (replaced) {
                const span = document.createElement('span');
                span.innerHTML = replacedHTML;
                textNode.replaceWith(span);
            }
        });
    }

    // Áp dụng cho tất cả phần tử có class highlight-content và highlight-target
    const allTargets = document.querySelectorAll('.highlight-content, .highlight-target');
    allTargets.forEach(el => highlightTextNodes(el));
});