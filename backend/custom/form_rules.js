/*
 * Form checks on every page (admin and student portal), from what each field is:
 *   phone   names with phone / mobile / contact_no: Nigerian 08031234567 or +2348031234567,
 *           or another country's +<8 to 15 digits>; spaces and dashes are removed
 *   email   type=email or names with email: name@domain.com
 *   number  amounts, fees, fines, discounts, salary, cost, quantity, marks, percentages:
 *           a number, not negative, not above the field's max (percentages not above 100)
 *   date    date fields (class date / datepicker, names with date or dob): a real date in the
 *           school's format; dates of birth not in the future; an end date not before its start date
 *   required  fields marked with the red * (or the required attribute) can't be left empty
 * Format checks run when a field is left; everything runs when the form is sent, and the form is
 * stopped (before the page's own submit code) until the problems are fixed. A field or form with
 * data-validate="off" is left alone. Search buttons that look through everyone (value search_full)
 * and buttons with formnovalidate skip the required check.
 */
(function () {
    'use strict';
    if (!window.jQuery) { return; }
    var $ = window.jQuery;

    var PHONE = /phone|mobile|contact_no|contactno|whatsapp/i;
    var EMAIL = /email/i;
    var EMAIL_NOT = /template|subject|body|config|setting|type|status|notification|sender_name/i;
    var NUMBER = /(^|_|\[)(amount|amt|fees?|fine|fine_amount|discount|salary|basic_salary|price|cost|perunitcost|unit_cost|qty|quantity|marks?|max_marks|min_marks|passing_marks|percentage|percent|balance|total)(_|\d|\]|$)/i;
    var NUMBER_NOT = /_id$|type|name|code|title|remark|note|description|mode|status/i;
    var DOB = /dob|date_of_birth|birth/i;
    var RANGE = [['from', 'to'], ['start', 'end'], ['begin', 'end']];

    function off(el) {
        return el.getAttribute('data-validate') === 'off' || $(el).closest('[data-validate="off"]').length > 0;
    }
    function visible(el) {
        return !!(el.offsetWidth || el.offsetHeight || el.getClientRects().length);
    }
    function textual(el) {
        var t = (el.getAttribute('type') || 'text').toLowerCase();
        return el.tagName === 'TEXTAREA' || (el.tagName === 'INPUT' && ['text', 'tel', 'email', 'number', 'date', ''].indexOf(t) >= 0);
    }
    function kind(el) {
        var name = (el.getAttribute('name') || el.id || '');
        var cls = el.className || '';
        var type = (el.getAttribute('type') || '').toLowerCase();
        if (!textual(el) || el.tagName === 'TEXTAREA') { return ''; }
        if (type === 'email' || (EMAIL.test(name) && !EMAIL_NOT.test(name))) { return 'email'; }
        if (PHONE.test(name)) { return 'phone'; }
        if (/\bdatetime\b|time/i.test(cls) || /time/i.test(name)) { return ''; }
        if (/\b(date|datepicker)\b/.test(cls) || /(^|_)(date|dob)(_|$)|_date|date_/i.test(name) || DOB.test(name) || type === 'date') { return 'date'; }
        if (type === 'number' || (NUMBER.test(name) && !NUMBER_NOT.test(name))) { return 'number'; }
        return '';
    }

    // the school's date format, e.g. dd-mm-yyyy, dd/mm/yyyy, mm-dd-yyyy, dd-M-yyyy, yyyy/mm/dd
    function parseDate(v) {
        var fmt = (window.date_format || 'dd-mm-yyyy').toLowerCase();
        v = $.trim(v);
        if (/^\d{4}-\d{2}-\d{2}$/.test(v)) { fmt = 'yyyy-mm-dd'; }
        var parts = v.split(/[-\/.\s]/), order = fmt.split(/[-\/.\s]/);
        if (parts.length !== 3 || order.length !== 3) { return null; }
        var d, m, y, months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
        for (var i = 0; i < 3; i++) {
            var p = parts[i], o = order[i];
            if (o === 'dd' || o === 'd') {
                d = /^\d{1,2}$/.test(p) ? parseInt(p, 10) : NaN;
            } else if (o === 'mm' || o === 'm') { // "m" is the M of dd-M-yyyy: a month name (Jan), or a number
                m = /^\d{1,2}$/.test(p) ? parseInt(p, 10) : months.indexOf(p.toLowerCase().slice(0, 3)) + 1;
            } else if (o === 'yyyy' || o === 'yy') {
                y = /^\d{4}$/.test(p) ? parseInt(p, 10) : NaN;
            }
        }
        if (!(d && m && y) || isNaN(d) || isNaN(m) || isNaN(y) || y < 1900 || y > 2200) { return null; }
        var dt = new Date(y, m - 1, d);
        return (dt.getFullYear() === y && dt.getMonth() === m - 1 && dt.getDate() === d) ? dt : null;
    }

    function check(el, withRequired) {
        if (off(el) || el.disabled || el.readOnly && !/\bdate\b|datepicker/.test(el.className) || !visible(el)) { return ''; }
        var v = $.trim($(el).val() == null ? '' : String($(el).val()));
        if (v === '') {
            return withRequired && isRequired(el) ? 'This field is required.' : '';
        }
        switch (kind(el)) {
            case 'phone':
                var p = v.replace(/[\s\-().]/g, '');
                if (p !== v && el.tagName === 'INPUT') { $(el).val(p); }
                if (/^0\d{10}$/.test(p) || /^\+?234\d{10}$/.test(p)) { return ''; }
                if (/^\+\d{8,15}$/.test(p) && !/^\+234/.test(p)) { return ''; }
                return 'Enter a valid phone number, for example 08031234567.';
            case 'email':
                return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v) ? '' : 'Enter a valid email address, for example name@example.com.';
            case 'number':
                var n = v.replace(/,/g, '');
                if (!/^-?\d+(\.\d+)?$/.test(n)) { return 'Enter a number.'; }
                n = parseFloat(n);
                if (n < 0) { return 'This can\'t be negative.'; }
                var max = el.getAttribute('max') || el.getAttribute('data-max');
                if (max !== null && max !== '' && !isNaN(parseFloat(max)) && n > parseFloat(max)) { return 'This can\'t be more than ' + max + '.'; }
                if (/percent/i.test(el.getAttribute('name') || '') && n > 100) { return 'A percentage can\'t be more than 100.'; }
                return '';
            case 'date':
                var dt = parseDate(v);
                if (!dt) { return 'Enter a real date (' + (window.date_format || 'dd-mm-yyyy') + ').'; }
                if (DOB.test(el.getAttribute('name') || '') && dt > new Date()) { return 'The date of birth can\'t be in the future.'; }
                return rangeProblem(el, dt);
        }
        return '';
    }

    // an end date before its start date: date_from / date_to, start_date / end_date, ...
    function rangeProblem(el, dt) {
        var name = el.getAttribute('name') || '', form = el.form;
        if (!form) { return ''; }
        for (var i = 0; i < RANGE.length; i++) {
            var a = RANGE[i][0], b = RANGE[i][1];
            var re = new RegExp('(^|_)' + b + '(_|$)', 'i');
            if (!re.test(name)) { continue; }
            var other = form.querySelector('[name="' + name.replace(re, '$1' + a + '$2') + '"]');
            if (!other || !$.trim(other.value)) { continue; }
            var start = parseDate(other.value);
            if (start && dt < start) { return 'This date can\'t be before the start date.'; }
        }
        return '';
    }

    function isRequired(el) {
        if (el.required) { return true; }
        if (el.type === 'checkbox' || el.type === 'radio' || el.type === 'file' || el.type === 'hidden') { return false; }
        var g = $(el).closest('.form-group, td, .col-md-12, .col-sm-12');
        if (!g.length) { return false; }
        // the red * next to this field's own label (not one of a neighbouring field in the same group)
        // (many labels say for="exampleInputEmail1": only a label pointing at a different, real field is someone else's)
        var label = g.find('label').filter(function () {
            var target = this.htmlFor ? document.getElementById(this.htmlFor) : null;
            return !target || target === el || !$(target).is('input, select, textarea');
        }).first();
        return label.length > 0 && g.find('input, select, textarea').filter(':visible').length === 1 &&
            (label.find('.req').length > 0 || label.next('.req').length > 0 || g.children('.req').length > 0);
    }

    function show(el, msg) {
        var $el = $(el), anchor = $el.closest('.input-group').length ? $el.closest('.input-group') : $el;
        anchor.next('.fr-msg').remove();
        $el.closest('.form-group').toggleClass('has-error', !!msg);
        if (msg) {
            anchor.after('<span class="text-danger fr-msg" style="display:block;font-size:12px">' + $('<div>').text(msg).html() + '</span>');
        }
    }

    function fields(form) {
        return $(form).find('input, select, textarea').filter(function () {
            var t = (this.getAttribute('type') || '').toLowerCase();
            return ['hidden', 'submit', 'button', 'reset', 'image', 'search'].indexOf(t) < 0;
        });
    }

    $(document).on('blur change', 'input, textarea', function () {
        if (!this.form || off(this.form)) { return; }
        show(this, check(this, false));
    });
    $(document).on('change', 'select', function () {
        if ($(this).next('.fr-msg').length) { show(this, check(this, true)); }
    });

    var submitter = null;
    document.addEventListener('click', function (e) {
        var b = e.target.closest ? e.target.closest('button, input[type=submit]') : null;
        submitter = b && b.form ? b : null;
    }, true);

    // capture phase: runs before the page's own submit handlers (which often send the form with AJAX)
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || form.tagName !== 'FORM' || off(form)) { return; }
        var btn = e.submitter || submitter;
        var skipRequired = btn && (btn.hasAttribute('formnovalidate') || btn.value === 'search_full');
        var first = null;
        fields(form).each(function () {
            var msg = check(this, !skipRequired);
            show(this, msg);
            if (msg && !first) { first = this; }
        });
        if (first) {
            e.preventDefault();
            e.stopImmediatePropagation();
            try { first.focus(); } catch (x) { /* hidden by a tab */ }
            if (first.scrollIntoView) { first.scrollIntoView({ block: 'center' }); }
        }
    }, true);

    window.EdusysFormRules = { check: check, kind: kind, parseDate: parseDate, isRequired: isRequired };
})();
