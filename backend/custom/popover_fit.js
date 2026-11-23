/*
 * Drop-down boxes made with <details class="...-pop"> (report card scores, CBT cards, "Make it a
 * CBT", "Change exams"...) open next to their button. Near the sidebar or the right edge they
 * would be partly hidden, so when one opens it is moved back into view, and never wider than the
 * space between the sidebar and the edge of the window.
 */
(function () {
    function fit(d) {
        var panel = null;
        for (var i = 0; i < d.children.length; i++) {
            if (d.children[i].tagName !== 'SUMMARY') {
                panel = d.children[i];
                break;
            }
        }
        if (!panel) {
            return;
        }
        panel.style.transform = '';
        var side = document.querySelector('.main-sidebar');
        var sr = side ? side.getBoundingClientRect() : null;
        var minX = (sr && sr.right > 0 && sr.width > 0 ? sr.right : 0) + 8;
        var maxX = window.innerWidth - 8;
        panel.style.maxWidth = Math.max(220, maxX - minX) + 'px';
        var r = panel.getBoundingClientRect();
        var shift = 0;
        if (r.left < minX) {
            shift = minX - r.left;
        } else if (r.right > maxX) {
            shift = Math.max(minX - r.left, maxX - r.right);
        }
        if (shift) {
            panel.style.transform = 'translateX(' + Math.round(shift) + 'px)';
        }
    }

    // 'toggle' does not bubble: listen in the capture phase for every <details> on the page.
    document.addEventListener('toggle', function (e) {
        var d = e.target;
        if (d && d.tagName === 'DETAILS' && d.open && /(^|\s)[\w-]*-pop(\s|$)/.test(d.className)) {
            fit(d);
            // the boxes inside it (a box in a box) are measured again once it has moved
            var inner = d.querySelectorAll('details[open]');
            for (var i = 0; i < inner.length; i++) {
                fit(inner[i]);
            }
        }
    }, true);
})();
