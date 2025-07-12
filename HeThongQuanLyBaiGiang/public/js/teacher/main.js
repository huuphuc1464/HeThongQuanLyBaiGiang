function time() {
    var today = new Date();
    var weekday = new Array(7);
    weekday[0] = "Chủ Nhật";
    weekday[1] = "Thứ Hai";
    weekday[2] = "Thứ Ba";
    weekday[3] = "Thứ Tư";
    weekday[4] = "Thứ Năm";
    weekday[5] = "Thứ Sáu";
    weekday[6] = "Thứ Bảy";
    var day = weekday[today.getDay()];
    var dd = today.getDate();
    var mm = today.getMonth() + 1;
    var yyyy = today.getFullYear();
    var h = today.getHours();
    var m = today.getMinutes();
    var s = today.getSeconds();
    m = checkTime(m);
    s = checkTime(s);
    nowTime = h + " giờ " + m + " phút " + s + " giây";
    if (dd < 10) {
        dd = '0' + dd
    }
    if (mm < 10) {
        mm = '0' + mm
    }
    today = day + ', ' + dd + '/' + mm + '/' + yyyy;
    tmp = '<span class="date"> ' + today + ' - ' + nowTime +
        '</span>';
    document.getElementById("clock").innerHTML = tmp;
    clocktime = setTimeout("time()", "1000", "Javascript");

    function checkTime(i) {
        if (i < 10) {
            i = "0" + i;
        }
        return i;
    }
}

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