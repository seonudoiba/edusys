/*
 * School branding on every printed / exported report.
 *  - DataTables Print: a letterhead (logo, school name, address, contacts) above the title, and a
 *    "Printed on" line below the table.
 *  - DataTables PDF: the same letterhead, page numbers and the print date in the footer.
 *  - DataTables Excel: the school name and address above the table.
 *  - Report pages printed with the browser (window.print): a letterhead at the top of the page.
 * Reads window.EDUSYS_BRAND = {name, address, phone, email, logo, color} (set by the layout).
 * Loaded after the DataTables Buttons plugins and before any table is created.
 */
(function ($, win) {
    'use strict';
    var B = win.EDUSYS_BRAND;
    if (!$ || !B || !B.name) { return; }
    var color = B.color || '#1f4e79';
    var logoData = null;

    function esc(t) { return $('<div>').text(t == null ? '' : String(t)).html(); }
    function contacts() { return [B.phone ? 'Tel: ' + B.phone : '', B.email || ''].filter(Boolean).join('   ·   '); }
    function printedOn() {
        var d = new Date();
        return 'Printed on ' + d.toLocaleDateString(undefined, { day: 'numeric', month: 'long', year: 'numeric' }) + ', ' + d.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' });
    }

    // the logo as a data URL, for pdfmake (same-origin upload)
    if (B.logo && win.fetch && win.FileReader) {
        fetch(B.logo, { credentials: 'same-origin' }).then(function (r) { return r.ok ? r.blob() : null; }).then(function (blob) {
            if (!blob || !/^image\/(png|jpe?g)$/.test(blob.type)) { return; }
            var fr = new FileReader();
            fr.onload = function () { logoData = fr.result; };
            fr.readAsDataURL(blob);
        }).catch(function () {});
    }

    function letterheadHtml() {
        return '<div class="edusys-letterhead">'
            + (B.logo ? '<img src="' + esc(B.logo) + '" alt="">' : '')
            + '<div class="lh-text"><div class="lh-name">' + esc(B.name) + '</div>'
            + (B.address ? '<div class="lh-line">' + esc(B.address) + '</div>' : '')
            + (contacts() ? '<div class="lh-line">' + esc(contacts()) + '</div>' : '')
            + '</div></div>';
    }
    var letterheadCss = '.edusys-letterhead{display:flex;align-items:center;gap:14px;padding:0 0 10px;margin:0 0 14px;border-bottom:3px solid ' + color + ';font-family:"Segoe UI",Roboto,Arial,sans-serif;}'
        + '.edusys-letterhead img{height:64px;width:auto;max-width:120px;object-fit:contain;}'
        + '.edusys-letterhead .lh-name{font-size:22px;font-weight:700;color:' + color + ';letter-spacing:.3px;line-height:1.2;}'
        + '.edusys-letterhead .lh-line{font-size:12px;color:#555;margin-top:2px;}'
        + '.edusys-printed{margin-top:14px;font-size:11px;color:#777;text-align:right;font-family:"Segoe UI",Roboto,Arial,sans-serif;}';

    function wrap(name, fn) {
        var ext = $.fn.dataTable && $.fn.dataTable.ext && $.fn.dataTable.ext.buttons;
        if (!ext || !ext[name] || !ext[name].action || ext[name].action.edusysBranded) { return; }
        var original = ext[name].action;
        var patched = function (e, dt, button, config) {
            var c = $.extend({}, config);
            fn(c);
            return original.call(this, e, dt, button, c);
        };
        patched.edusysBranded = true;
        ext[name].action = patched;
    }

    // Print
    wrap('print', function (c) {
        var own = c.customize;
        c.customize = function (w) {
            if (typeof own === 'function') { own.apply(this, arguments); }
            try {
                var d = w.document;
                $(d.head).append('<style>' + letterheadCss
                    + 'body{font-family:"Segoe UI",Roboto,Arial,sans-serif;color:#222;padding:18px 22px;}'
                    + 'h1{font-size:17px!important;text-align:left!important;margin:0 0 10px!important;color:#333;}'
                    + 'table.dataTable{border-collapse:collapse!important;width:100%!important;}'
                    + 'table.dataTable th{background:' + color + '!important;color:#fff!important;padding:7px 8px!important;font-size:12px;-webkit-print-color-adjust:exact;print-color-adjust:exact;}'
                    + 'table.dataTable td{padding:6px 8px!important;border-bottom:1px solid #e3e3e3;font-size:12px;}'
                    + 'table.dataTable tbody tr:nth-child(even) td{background:#f6f8fb;-webkit-print-color-adjust:exact;print-color-adjust:exact;}'
                    + '</style>');
                $(d.body).prepend(letterheadHtml()).append('<div class="edusys-printed">' + esc(printedOn()) + '</div>');
            } catch (err) { /* printing still works without the letterhead */ }
        };
    });

    // PDF
    function pdfCustomize(c) {
        var own = c.customize;
        c.customize = function (doc) {
            if (typeof own === 'function') { own.apply(this, arguments); }
            try {
                var text = [{ text: B.name, fontSize: 15, bold: true, color: color }];
                if (B.address) { text.push({ text: B.address, fontSize: 8, color: '#555555', margin: [0, 2, 0, 0] }); }
                if (contacts()) { text.push({ text: contacts(), fontSize: 8, color: '#555555', margin: [0, 1, 0, 0] }); }
                var cols = [];
                if (logoData) { cols.push({ image: logoData, fit: [48, 48], width: 52 }); }
                cols.push({ stack: text, margin: [logoData ? 6 : 0, logoData ? 4 : 0, 0, 0] });
                doc.content.unshift(
                    { columns: cols, columnGap: 4 },
                    { canvas: [{ type: 'line', x1: 0, y1: 4, x2: 515, y2: 4, lineWidth: 2, lineColor: color }], margin: [0, 0, 0, 8] }
                );
                var when = printedOn();
                doc.footer = function (page, pages) {
                    return { columns: [{ text: B.name + '  ·  ' + when, fontSize: 7, color: '#777777' }, { text: 'Page ' + page + ' of ' + pages, alignment: 'right', fontSize: 7, color: '#777777' }], margin: [40, 10, 40, 0] };
                };
                if (doc.styles && doc.styles.tableHeader) { doc.styles.tableHeader.fillColor = color; doc.styles.tableHeader.color = '#ffffff'; }
                if (doc.styles && doc.styles.title) { doc.styles.title.fontSize = 12; doc.styles.title.alignment = 'left'; doc.styles.title.margin = [0, 0, 0, 6]; }
            } catch (err) { /* the PDF still downloads without the letterhead */ }
        };
    }
    wrap('pdfHtml5', pdfCustomize);

    // Excel
    wrap('excelHtml5', function (c) {
        if (!c.messageTop) {
            c.messageTop = B.name + (B.address ? ' — ' + B.address : '') + (contacts() ? ' — ' + contacts() : '');
        }
    });

    // Report pages printed another way:
    //  - with the browser (window.print, also after a page swaps its body for the report, as the
    //    printDiv() helpers do): a letterhead is put at the top just before printing, removed after;
    //  - with a Popup(html) helper (prints from a hidden frame): the letterhead goes above the html.
    var path = win.location.pathname.toLowerCase();
    if (/(report|takings|\/results|rank|collection|due_?fees|attendance|attendence)/.test(path)) {
        $('head').append('<style>' + letterheadCss + '.edusys-print-letterhead{display:none;}'
            + '@media print{.edusys-print-letterhead{display:block!important;padding:0 15px;}.edusys-letterhead{-webkit-print-color-adjust:exact;print-color-adjust:exact;}}</style>');
        var added = null;
        win.addEventListener('beforeprint', function () {
            if (document.querySelector('.edusys-print-letterhead, .edusys-letterhead')) { return; }
            added = document.createElement('div');
            added.className = 'edusys-print-letterhead';
            added.innerHTML = letterheadHtml();
            var at = document.querySelector('.content-wrapper') || document.body;
            at.insertBefore(added, at.firstChild);
        });
        win.addEventListener('afterprint', function () {
            if (added && added.parentNode) { added.parentNode.removeChild(added); }
            added = null;
        });
        $(function () {
            if (typeof win.Popup === 'function' && !win.Popup.edusysBranded) {
                var popup = win.Popup;
                win.Popup = function (data) {
                    var args = [].slice.call(arguments);
                    if (typeof data === 'string' && data.indexOf('edusys-letterhead') === -1) {
                        args[0] = '<style>' + letterheadCss + '.edusys-letterhead{-webkit-print-color-adjust:exact;print-color-adjust:exact;}</style>' + letterheadHtml() + data;
                    }
                    return popup.apply(this, args);
                };
                win.Popup.edusysBranded = true;
            }
        });
    }
})(window.jQuery, window);
