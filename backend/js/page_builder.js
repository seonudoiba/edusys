/*
 * Page builder editor (admin/front/page_builder/edit). The page is a list of sections, each with
 * columns of widgets; the DOM is the working copy (each .pb-widget holds its data) and collect() turns
 * it back into the layout JSON. The server cleans everything again before saving (Page_widgets::clean).
 */
(function ($) {
    'use strict';
    var C = window.PB;
    var $canvas = $('#pbCanvas');
    var dirty = false;
    var LAYOUT_LABELS = { '1': '1 column', '1-1': '2 columns', '1-2': 'Narrow + wide', '2-1': 'Wide + narrow', '1-1-1': '3 columns' };

    function colsOf(layout) { return layout.split('-').map(Number); }
    function clone(o) { return JSON.parse(JSON.stringify(o)); }
    function touch() { dirty = true; status('You have unsaved changes.'); }
    function status(text) { $('#pbStatus').text(text); }

    function defaults(fields) {
        var out = {};
        $.each(fields, function (name, f) { out[name] = clone(f['default'] === undefined ? '' : f['default']); });
        return out;
    }

    // ---------------------------------------------------------------- widgets

    function summary(w) {
        var s = w.settings || {};
        var text = s.title || s.label || s.text || s.address || s.url || '';
        if (!text && s.body) { text = $('<div>').html(s.body).text(); }
        if (!text && $.isArray(s.items)) { text = s.items.length + ' item(s)'; }
        return String(text).slice(0, 60);
    }

    function widgetEl(w) {
        var def = C.catalogue[w.type];
        var $el = $('<div class="pb-widget">').data('w', w);
        $('<i>').addClass('fa ' + def.icon).appendTo($el);
        var $name = $('<div class="pb-name">').appendTo($el);
        $('<span>').text(def.label).appendTo($name);
        $('<small>').text(summary(w) || def.hint || '').appendTo($name);
        var settings = !$.isEmptyObject(def.fields);
        if (settings) {
            $('<button type="button" class="btn btn-link btn-xs pb-edit" title="Settings" aria-label="Settings"><i class="fa fa-pencil"></i></button>').appendTo($el);
        }
        if (C.formTypes.indexOf(w.type) < 0) {
            $('<button type="button" class="btn btn-link btn-xs pb-copy" title="Duplicate" aria-label="Duplicate"><i class="fa fa-clone"></i></button>').appendTo($el);
        }
        $('<button type="button" class="btn btn-link btn-xs pb-del" title="Remove" aria-label="Remove"><i class="fa fa-times"></i></button>').appendTo($el);
        return $el;
    }

    function newWidget(type) {
        return { type: type, settings: defaults(C.catalogue[type].fields) };
    }

    /** One form per page, and none on the homepage: the page handles one form's submissions. */
    function refreshTools() {
        var used = false;
        $canvas.find('.pb-widget').each(function () {
            if (C.formTypes.indexOf($(this).data('w').type) >= 0) { used = true; }
        });
        C.formTypes.forEach(function (t) {
            var off = !C.formsAllowed || used;
            var $tool = $('.pb-tool[data-type="' + t + '"]');
            $tool.prop('disabled', off);
            if ($tool.hasClass('ui-draggable')) { $tool.draggable(off ? 'disable' : 'enable'); }
        });
        $('#pbEmpty').toggle($canvas.children('.pb-section').length === 0);
    }

    // ---------------------------------------------------------------- sections

    function sectionEl(sec) {
        var $s = $('<div class="pb-section">');
        var $bar = $('<div class="pb-sec-bar">').appendTo($s);
        $('<span class="pb-handle" title="Drag to move this section"><i class="fa fa-arrows"></i></span>').appendTo($bar);
        var $layout = $('<select class="form-control pb-layout" aria-label="Columns">').appendTo($bar);
        C.layouts.forEach(function (l) { $('<option>').val(l).text(LAYOUT_LABELS[l] || l).appendTo($layout); });
        $layout.val(sec.layout);
        var $bg = $('<select class="form-control pb-bg" aria-label="Background">').appendTo($bar);
        $.each(C.backgrounds, function (k, v) { $('<option>').val(k).text('Background: ' + v).appendTo($bg); });
        $bg.val(sec.bg);
        $('<label style="font-weight:normal;margin:0"><input type="checkbox" class="pb-full"> Full width</label>').appendTo($bar).find('input').prop('checked', !!sec.full);
        $('<span class="pb-spacer">').appendTo($bar);
        $('<button type="button" class="btn btn-link btn-xs pb-up" title="Move up" aria-label="Move section up"><i class="fa fa-arrow-up"></i></button>').appendTo($bar);
        $('<button type="button" class="btn btn-link btn-xs pb-down" title="Move down" aria-label="Move section down"><i class="fa fa-arrow-down"></i></button>').appendTo($bar);
        $('<button type="button" class="btn btn-link btn-xs pb-del-sec" title="Delete section" aria-label="Delete section"><i class="fa fa-trash"></i></button>').appendTo($bar);
        $('<div class="pb-cols">').appendTo($s);
        paintBg($s, sec.bg);
        buildCols($s, sec.layout, sec.cols || []);
        return $s;
    }

    function paintBg($s, bg) { $s.removeClass('pb-bg-light pb-bg-dark').addClass(bg === 'none' ? '' : 'pb-bg-' + bg); }

    function buildCols($s, layout, cols) {
        var $cols = $s.children('.pb-cols').empty();
        var parts = colsOf(layout), total = parts.reduce(function (a, b) { return a + b; }, 0);
        parts.forEach(function (part, i) {
            var $col = $('<div class="pb-col">').css('flex', part + ' 1 ' + Math.floor(100 * part / total - 2) + '%').appendTo($cols);
            (cols[i] || []).forEach(function (w) {
                if (C.catalogue[w.type]) { $col.append(widgetEl(w)); }
            });
            makeSortable($col);
        });
        refreshDraggables();
    }

    function colData($s) {
        return $s.find('> .pb-cols > .pb-col').map(function () {
            return [$(this).children('.pb-widget').map(function () { return $(this).data('w'); }).get()];
        }).get();
    }

    function makeSortable($col) {
        $col.sortable({
            connectWith: '.pb-col',
            placeholder: 'pb-ph',
            tolerance: 'pointer',
            cancel: 'button:not(.pb-tool), a',
            update: function () {
                // A widget dropped from the panel arrives as a copy of the button: make it a real widget.
                $(this).children('.pb-tool').each(function () {
                    $(this).replaceWith(widgetEl(newWidget($(this).data('type'))));
                });
                touch();
                refreshTools();
            }
        });
    }

    function refreshDraggables() {
        $('.pb-tool').each(function () {
            var $t = $(this);
            if ($t.hasClass('ui-draggable')) { $t.draggable('destroy'); }
            $t.draggable({ connectToSortable: '.pb-col', helper: 'clone', revert: 'invalid', appendTo: 'body', zIndex: 2000, cancel: '' });
        });
        refreshTools();
    }

    function addSection(sec) {
        var $s = sectionEl(sec || { layout: '1', bg: 'none', full: 0, cols: [[]] });
        $canvas.append($s);
        return $s;
    }

    function collect() {
        return {
            sections: $canvas.children('.pb-section').map(function () {
                var $s = $(this);
                return {
                    layout: $s.find('.pb-layout').val(),
                    bg: $s.find('.pb-bg').val(),
                    full: $s.find('.pb-full').prop('checked') ? 1 : 0,
                    cols: colData($s)
                };
            }).get()
        };
    }

    function load(layout) {
        $canvas.empty();
        (layout.sections || []).forEach(function (sec) { addSection(sec); });
        refreshDraggables();
    }

    // ---------------------------------------------------------------- settings dialog

    var editing = null, mediaTarget = null;

    function fieldEl(name, f, value) {
        var id = 'pbf_' + name + '_' + Math.random().toString(36).slice(2, 8);
        var $g = $('<div class="form-group">').attr('data-name', name).attr('data-type', f.type);
        var $label = $('<label>').attr('for', id).text(f.label);
        if (f.type === 'check') {
            $('<label style="font-weight:normal">').append($('<input type="checkbox" class="pbv">').attr('id', id).prop('checked', !!value)).append(' ' + f.label).appendTo($g);
            return $g;
        }
        $g.append($label);
        if (f.type === 'select') {
            var $sel = $('<select class="form-control pbv">').attr('id', id).appendTo($g);
            $.each(f.options, function (k, v) { $('<option>').val(k).text(v).appendTo($sel); });
            $sel.val(String(value));
        } else if (f.type === 'number') {
            $('<input type="number" class="form-control pbv">').attr({ id: id, min: f.min, max: f.max }).val(value).appendTo($g);
        } else if (f.type === 'textarea' || f.type === 'html') {
            if (f.type === 'html') {
                var $tb = $('<div class="btn-group btn-group-xs" style="margin-bottom:4px;display:block">').appendTo($g);
                [['b', 'Bold', 'fa-bold'], ['i', 'Italic', 'fa-italic'], ['ul', 'List', 'fa-list-ul'], ['h3', 'Heading', 'fa-header'], ['a', 'Link', 'fa-link']].forEach(function (t) {
                    $('<button type="button" class="btn btn-default pb-fmt">').attr({ 'data-tag': t[0], title: t[1], 'aria-label': t[1] }).append($('<i>').addClass('fa ' + t[2])).appendTo($tb);
                });
            }
            $('<textarea class="form-control pbv" rows="5">').attr('id', id).val(value).appendTo($g);
        } else if (f.type === 'image') {
            var $ig = $('<div class="input-group">').appendTo($g);
            $('<input type="text" class="form-control pbv" placeholder="Choose an image">').attr('id', id).val(value).appendTo($ig);
            $('<span class="input-group-btn"><button type="button" class="btn btn-default pb-choose">Choose</button></span>').appendTo($ig);
        } else if (f.type === 'items') {
            var $list = $('<div class="pb-items">').appendTo($g);
            (value || []).forEach(function (item) { $list.append(itemEl(f, item)); });
            $('<button type="button" class="btn btn-default btn-xs pb-add-item"><i class="fa fa-plus"></i> Add</button>').data('f', f).appendTo($g);
        } else {
            $('<input type="text" class="form-control pbv">').attr('id', id).val(value).appendTo($g);
        }
        return $g;
    }

    function itemEl(f, item) {
        var $i = $('<div class="pb-item">');
        $.each(f.fields, function (name, sub) { $i.append(fieldEl(name, sub, item[name] === undefined ? sub['default'] : item[name])); });
        $('<button type="button" class="btn btn-link btn-xs text-danger pb-del-item">Remove</button>').appendTo($i);
        return $i;
    }

    function readFields($root, fields) {
        var out = {};
        $.each(fields, function (name, f) {
            var $g = $root.children('.form-group[data-name="' + name + '"]');
            if (f.type === 'items') {
                out[name] = $g.find('> .pb-items > .pb-item').map(function () { return readFields($(this), f.fields); }).get();
            } else if (f.type === 'check') {
                out[name] = $g.find('.pbv').prop('checked') ? 1 : 0;
            } else if (f.type === 'number') {
                out[name] = parseInt($g.find('.pbv').val(), 10) || f.min;
            } else {
                out[name] = $g.find('.pbv').val();
            }
        });
        return out;
    }

    function openSettings($w) {
        var w = $w.data('w'), def = C.catalogue[w.type];
        editing = $w;
        $('#pbSettingsTitle').text(def.label);
        var $body = $('#pbSettingsBody').empty();
        if (def.hint) { $('<p class="text-muted">').text(def.hint).appendTo($body); }
        $.each(def.fields, function (name, f) {
            $body.append(fieldEl(name, f, w.settings[name] === undefined ? f['default'] : w.settings[name]));
        });
        $('#pbSettings').modal('show');
    }

    // Formatting buttons: wrap the selection in a tag.
    $(document).on('click', '.pb-fmt', function () {
        var $ta = $(this).closest('.form-group').find('textarea');
        var ta = $ta[0], tag = $(this).data('tag');
        var a = ta.selectionStart, b = ta.selectionEnd, sel = ta.value.slice(a, b) || 'text', out;
        if (tag === 'a') {
            var url = window.prompt('Link address (https://...)', 'https://');
            if (!url) { return; }
            out = '<a href="' + url.replace(/"/g, '') + '">' + sel + '</a>';
        } else if (tag === 'ul') {
            out = '<ul>\n' + sel.split('\n').map(function (l) { return '<li>' + l + '</li>'; }).join('\n') + '\n</ul>';
        } else {
            out = '<' + tag + '>' + sel + '</' + tag + '>';
        }
        ta.value = ta.value.slice(0, a) + out + ta.value.slice(b);
        ta.focus();
    });

    $(document).on('click', '.pb-add-item', function () {
        var f = $(this).data('f'), $list = $(this).siblings('.pb-items');
        if ($list.children().length < f.max) { $list.append(itemEl(f, defaults(f.fields))); }
    });
    $(document).on('click', '.pb-del-item', function () { $(this).closest('.pb-item').remove(); });

    $('#pbSettingsApply').on('click', function () {
        if (!editing) { return; }
        var w = editing.data('w');
        w.settings = readFields($('#pbSettingsBody'), C.catalogue[w.type].fields);
        editing.replaceWith(widgetEl(w));
        editing = null;
        $('#pbSettings').modal('hide');
        touch();
    });

    // ---------------------------------------------------------------- media manager

    $(document).on('click', '.pb-choose', function () {
        mediaTarget = $(this).closest('.input-group').find('.pbv');
        var $m = $('#pbMedia').modal('show');
        var $body = $m.find('.modal-media-body').html('<p class="text-muted">Loading…</p>');
        $.post(C.mediaUrl, {}, function (html) { $body.html(html); }, 'text');
    });
    $(document).on('click', '#pbMedia .img_div_modal', function () {
        $('#pbMedia .fadeoverlay').removeClass('active');
        $(this).find('.fadeoverlay').addClass('active');
    });
    $('#pbMediaUse').on('click', function () {
        var $img = $('#pbMedia .fadeoverlay.active img');
        if ($img.length && mediaTarget) { mediaTarget.val($img.data('img')); }
        $('#pbMedia').modal('hide');
    });
    // Closing the top dialog must leave the page scroll locked while the settings dialog is open.
    $('#pbMedia').on('hidden.bs.modal', function () {
        if ($('#pbSettings').hasClass('in')) { $('body').addClass('modal-open'); }
    });

    // ---------------------------------------------------------------- canvas events

    $canvas.on('click', '.pb-edit', function () { openSettings($(this).closest('.pb-widget')); });
    $canvas.on('dblclick', '.pb-widget', function () {
        if (!$.isEmptyObject(C.catalogue[$(this).data('w').type].fields)) { openSettings($(this)); }
    });
    $canvas.on('click', '.pb-copy', function () {
        var $w = $(this).closest('.pb-widget');
        $w.after(widgetEl(clone($w.data('w'))));
        touch();
    });
    $canvas.on('click', '.pb-del', function () {
        $(this).closest('.pb-widget').remove();
        touch();
        refreshTools();
    });
    $canvas.on('change', '.pb-layout', function () {
        var $s = $(this).closest('.pb-section'), layout = $(this).val();
        var cols = colData($s), n = colsOf(layout).length;
        // Fewer columns: the widgets of the removed columns move into the last one.
        var kept = cols.slice(0, n);
        cols.slice(n).forEach(function (extra) { kept[n - 1] = (kept[n - 1] || []).concat(extra); });
        buildCols($s, layout, kept);
        touch();
    });
    $canvas.on('change', '.pb-bg', function () { paintBg($(this).closest('.pb-section'), $(this).val()); touch(); });
    $canvas.on('change', '.pb-full', touch);
    $canvas.on('click', '.pb-up', function () { var $s = $(this).closest('.pb-section'); $s.prev('.pb-section').before($s); touch(); });
    $canvas.on('click', '.pb-down', function () { var $s = $(this).closest('.pb-section'); $s.next('.pb-section').after($s); touch(); });
    $canvas.on('click', '.pb-del-sec', function () {
        var $s = $(this).closest('.pb-section');
        if ($s.find('.pb-widget').length && !window.confirm('Delete this section and its widgets?')) { return; }
        $s.remove();
        touch();
        refreshTools();
    });
    $canvas.sortable({ handle: '.pb-handle', items: '> .pb-section', placeholder: 'pb-sec-ph', update: touch });

    $('#pbAddSection').on('click', function () {
        var $s = addSection();
        refreshDraggables();
        touch();
        $('html, body').animate({ scrollTop: $s.offset().top - 110 }, 150);
    });

    // Clicking a widget in the panel adds it to the last section (works without a mouse too).
    $('.pb-side').on('click', '.pb-tool', function () {
        if ($(this).prop('disabled')) { return; }
        var $last = $canvas.children('.pb-section').last();
        if (!$last.length) { $last = addSection(); refreshDraggables(); }
        var w = newWidget($(this).data('type'));
        $last.find('> .pb-cols > .pb-col').last().append(widgetEl(w));
        touch();
        refreshTools();
    });

    // ---------------------------------------------------------------- saving

    function save(publish, done) {
        var $btns = $('#pbPublish, #pbSave, #pbPreview').prop('disabled', true);
        status('Saving…');
        $.ajax({
            type: 'POST', url: C.saveUrl, dataType: 'json',
            data: { form_token: C.token, layout: JSON.stringify(collect()), publish: publish ? 1 : 0 }
        }).done(function (r) {
            status(r.message || '');
            if (r.ok) {
                dirty = false;
                load(r.layout); // show what was really saved, after the server cleaned it
                if (done) { done(); }
            } else if (done) {
                done(r.message || 'Not saved');
            }
        }).fail(function () {
            status('Saving failed. Please check your connection and try again.');
            if (done) { done('failed'); }
        }).always(function () { $btns.prop('disabled', false); });
    }

    $('#pbSave').on('click', function () { save(false); });
    $('#pbPublish').on('click', function () { save(true); });
    $('#pbPreview').on('click', function () {
        var win = window.open('about:blank', '_blank'); // opened now: browsers block it after the save
        save(false, function (err) {
            if (err) { if (win) { win.close(); } return; }
            if (win) { win.location = C.previewUrl; }
        });
    });
    window.addEventListener('beforeunload', function (e) {
        if (dirty) { e.preventDefault(); e.returnValue = ''; }
    });

    load(C.layout);
    status($('#pbStatus').text());
}(jQuery));
