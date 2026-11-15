/*
 * "Select Criteria" boxes load their list as soon as the page opens, instead of waiting for Search:
 *
 *   - a box with an everyone search (a submit button value="search_full", e.g. Student Details,
 *     Disabled Students, Alumni) runs it with an empty keyword: every student is listed;
 *   - otherwise a box with a Class select picks the first class (and its first section once they
 *     have loaded) and presses Search, but only when every other required field (* in the label)
 *     of that row already has a value; if not, the class and section are just pre-filled.
 *
 * It runs once, on a plain page load (window.EDUSYS_REQUEST_METHOD === 'GET', set in layout/footer.php),
 * so a page that is itself a search result (POSTed) is left alone. A form opts out with
 * data-autoload="off"; window.EDUSYS_CRITERIA_TITLE is the translated "Select Criteria" box title.
 * window.EDUSYS_AUTOLOAD_DRY (tests) logs what would be pressed instead of pressing it.
 */
(function ($) {
    'use strict';
    if (!$ || window.EDUSYS_REQUEST_METHOD !== 'GET' || /[?&](class_id|section_id)=/.test(location.search)) {
        return;
    }
    var title = $.trim(String(window.EDUSYS_CRITERIA_TITLE || '')).toLowerCase();

    function log(what, form, btn) {
        var msg = 'criteria-autoload: ' + what + ' ' + (form.getAttribute('action') || '') + (btn ? ' [' + (btn.name || '') + '=' + (btn.value || '') + ']' : '');
        if (window.console) { console.log(msg); }
        window.EDUSYS_AUTOLOAD_LOG = (window.EDUSYS_AUTOLOAD_LOG || []).concat([msg]);
    }

    function press(form, btn, what) {
        log(what, form, btn);
        if (window.EDUSYS_AUTOLOAD_DRY) { return; }
        $(btn).trigger('click'); // runs the page's click handlers, then the button's own click (submits once)
    }

    function criteriaForms() {
        var out = [];
        $('.box').each(function () {
            var t = $.trim($(this).find('.box-title').first().text()).toLowerCase();
            if (!title || t !== title) { return; }
            $(this).find('form').each(function () {
                if (this.getAttribute('data-autoload') !== 'off' && $(this).find('button[type=submit], input[type=submit]').length) {
                    out.push(this);
                }
            });
        });
        return out;
    }

    function empty(el) {
        var v = $(el).val();
        return v === null || v === undefined || (typeof v === 'string' && $.trim(v) === '') || ($.isArray(v) && !v.length);
    }

    // required fields (label followed by <small class="req">) of the row that holds the class select
    function missingRequired(scope) {
        var missing = false;
        $(scope).find('.req').each(function () {
            var group = $(this).closest('.form-group');
            group.find('select, input:not([type=hidden]):not([type=checkbox]):not([type=radio]), textarea').each(function () {
                if (!this.disabled && empty(this)) { missing = true; }
            });
        });
        return missing;
    }

    function firstValue(select) {
        var v = null;
        $(select).find('option').each(function () {
            if (v === null && $.trim(this.value) !== '' && this.value !== '0') { v = this.value; }
        });
        return v;
    }

    function searchButton(form) {
        var buttons = $(form).find('button[type=submit], input[type=submit]').filter(function () { return this.value !== 'search_full' && this.value !== 'keyword_search'; });
        var preferred = buttons.filter(function () { return /^(search|search_filter|class_search|filter)$/.test(this.value || '') || /^(search|class_search)$/.test(this.name || ''); });
        return (preferred.length ? preferred : buttons).get(0);
    }

    function waitFor(test, ms, done) {
        var end = Date.now() + ms;
        (function tick() {
            if (test()) { return done(true); }
            if (Date.now() > end) { return done(false); }
            setTimeout(tick, 150);
        })();
    }

    function run(form) {
        var everyone = $(form).find('button[type=submit][value=search_full], input[type=submit][value=search_full]').get(0);
        var cls = $(form).find('select[name=class_id]').get(0);
        if (everyone && (cls || $(form).find('select[name=role]').length)) {
            $(form).find('input[name=search_text]').val('');
            return press(form, everyone, 'everyone');
        }
        if (!cls || !empty(cls)) {
            return;
        }
        var first = firstValue(cls);
        if (first === null) {
            return;
        }
        $(cls).val(first).trigger('change');
        var section = $(form).find('select[name=section_id]').get(0);
        var row = $(cls).closest('.row').get(0) || form;
        var finish = function () {
            var btn = searchButton(form);
            if (!btn) { return; }
            if (missingRequired(row)) {
                log('prefilled only (other required fields)', form, btn);
                return;
            }
            press(form, btn, 'first class');
        };
        if (!section) {
            return finish();
        }
        waitFor(function () { return firstValue(section) !== null; }, 6000, function (loaded) {
            if (loaded && empty(section)) {
                $(section).val(firstValue(section)).trigger('change');
            }
            setTimeout(finish, 300); // let anything that depends on the section start loading
        });
    }

    $(window).on('load', function () {
        var forms = criteriaForms();
        if (forms.length) {
            run(forms[0]); // one search per page: the first criteria box
        }
    });
})(window.jQuery);
