/*
 * Block editor for school websites (admin/front/page_builder/edit).
 *
 * The page is a tree of blocks held in `state` (see Page_widgets): sections hold columns or content,
 * columns hold content. Text blocks are edited directly on the page; other blocks show a preview
 * rendered by the server and are changed in the settings panel on the right. Every change goes
 * through the undo history. The server cleans everything again before saving.
 */
(function ($) {
    'use strict';

    var C = window.BE, CAT = C.catalogue;
    var GROUPS = [['text', 'Text'], ['media', 'Media'], ['design', 'Design'], ['dynamic', 'From your school\'s data'], ['platform', 'Platform website'], ['content', 'More']];
    var TEXT = { paragraph: 1, heading: 1, list: 1, quote: 1 };
    var LOCAL = { paragraph: 1, heading: 1, list: 1, quote: 1, table: 1, image: 1, button: 1, separator: 1, spacer: 1, section: 1, columns: 1, column: 1 };
    var CANVAS_FIELDS = { inline: 1, listhtml: 1, table: 1 };

    var $canvas = $('#beCanvas'), $insp = $('#beInspectorBody');
    var state = { blocks: [] };
    var selected = null, seq = 0;
    var undoStack = [], redoStack = [], typingTimer = null, typingOpen = false;
    var dirty = false, saving = false, version = 0;
    var previews = {}, pendingPreviews = {}, inflight = {}, previewTimer = null;
    var slash = null; // {id, items, index}

    // ================================================================ helpers

    function uid() { seq += 1; return 'b' + seq; }
    function clone(o) { return JSON.parse(JSON.stringify(o)); }
    function esc(s) { return $('<div>').text(s == null ? '' : String(s)).html(); }
    function srcUrl(u) { return /^https?:\/\//i.test(u) ? u : C.baseUrl + String(u).replace(/^\/+/, ''); }
    function widths(layout) { return C.layouts[layout] || [12]; }

    function defaults(type) {
        var out = {};
        $.each((CAT[type] || {}).fields || {}, function (name, f) { out[name] = clone(f['default'] === undefined ? '' : f['default']); });
        return out;
    }

    /** A new block with default settings; columns get their empty columns. */
    function make(type, attrs) {
        var b = { id: uid(), type: type, attrs: $.extend(defaults(type), attrs || {}) };
        if (type === 'section') { b.inner = []; }
        if (type === 'columns') {
            b.inner = widths(b.attrs.layout).map(function () { return { id: uid(), type: 'column', attrs: {}, inner: [] }; });
        }
        return b;
    }

    /** Gives blocks from the server or a pattern their ids, settings and columns. */
    function adopt(blocks) {
        return (blocks || []).filter(function (b) { return b && CAT[b.type]; }).map(function (b) {
            var n = { id: uid(), type: b.type, attrs: $.extend(defaults(b.type), b.attrs || b.settings || {}) };
            if (b.type === 'section' || b.type === 'column') { n.inner = adopt(b.inner); }
            if (b.type === 'columns') {
                var cols = (b.inner || []).filter(function (c) { return c && c.type === 'column'; });
                n.inner = widths(n.attrs.layout).map(function (w, i) { return { id: uid(), type: 'column', attrs: {}, inner: adopt(cols[i] ? cols[i].inner : []) }; });
            }
            return n;
        });
    }

    /** The tree as the server stores it (no editor ids). */
    function strip(blocks) {
        return blocks.map(function (b) {
            var o = { type: b.type, attrs: b.attrs };
            if (b.inner) { o.inner = strip(b.inner); }
            return o;
        });
    }

    function walk(blocks, fn, parent) {
        blocks.forEach(function (b) { fn(b, parent); if (b.inner) { walk(b.inner, fn, b); } });
    }

    /** Where a block is: {block, list, index, parent}. */
    function find(id, list, parent) {
        list = list || state.blocks;
        for (var i = 0; i < list.length; i++) {
            if (list[i].id === id) { return { block: list[i], list: list, index: i, parent: parent || null }; }
            if (list[i].inner) {
                var r = find(id, list[i].inner, list[i]);
                if (r) { return r; }
            }
        }
        return null;
    }

    function contextOf(parent) { return parent ? parent.type : 'root'; }

    /** Sections hold columns or content; columns hold content only; nothing holds a section but the page. */
    function canHold(ctx, type) {
        if (type === 'column') { return ctx === 'columns'; }
        if (ctx === 'columns') { return false; }
        if (type === 'section') { return ctx === 'root'; }
        if (type === 'columns') { return ctx !== 'column'; }
        return true;
    }

    function formCount() {
        var n = 0;
        walk(state.blocks, function (b) { if (C.formTypes.indexOf(b.type) >= 0) { n++; } });
        return n;
    }

    function containsForm(b) {
        var found = false;
        walk([b], function (x) { if (C.formTypes.indexOf(x.type) >= 0) { found = true; } });
        return found;
    }

    // ================================================================ history

    function snapshot() {
        undoStack.push(JSON.stringify(state.blocks));
        if (undoStack.length > 100) { undoStack.shift(); }
        redoStack = [];
        changed();
    }

    function changed() {
        dirty = true;
        version++;
        status('Unsaved changes');
        $('#beUndo').prop('disabled', undoStack.length === 0);
        $('#beRedo').prop('disabled', redoStack.length === 0);
    }

    /** Typing counts as one change until a pause. */
    function typingSnapshot() {
        if (!typingOpen) { snapshot(); typingOpen = true; } else { changed(); }
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function () { typingOpen = false; }, 900);
    }

    function restore(from, to) {
        if (!from.length) { return; }
        to.push(JSON.stringify(state.blocks));
        state.blocks = JSON.parse(from.pop());
        typingOpen = false;
        if (selected && !find(selected)) { selected = null; }
        changed();
        render();
    }
    function undo() { restore(undoStack, redoStack); }
    function redo() { restore(redoStack, undoStack); }

    // ================================================================ changing the tree

    /** Puts a block after another, moving up to the nearest place that may hold it. */
    function placeAfter(id, block) {
        var loc = find(id);
        if (!loc) { state.blocks.push(block); return; }
        if (canHold(contextOf(loc.parent), block.type)) {
            loc.list.splice(loc.index + 1, 0, block);
            return;
        }
        if (loc.parent && loc.parent.type === 'column') {
            var col = find(loc.parent.id);
            placeAfter(col.parent.id, block);
        } else if (loc.parent) {
            placeAfter(loc.parent.id, block);
        } else {
            state.blocks.push(block);
        }
    }

    function insertBlocks(blocks, ownerId) {
        if (!blocks.length) { return; }
        snapshot();
        var after = null;
        blocks.forEach(function (b) {
            if (ownerId) {
                var owner = ownerId === 'root' ? null : find(ownerId).block;
                var list = owner ? owner.inner : state.blocks;
                if (canHold(owner ? owner.type : 'root', b.type)) { list.push(b); } else if (owner) { placeAfter(owner.type === 'column' ? find(owner.id).parent.id : owner.id, b); } else { state.blocks.push(b); }
                ownerId = null;
            } else if (after || selected) {
                placeAfter(after || selected, b);
            } else {
                state.blocks.push(b);
            }
            after = b.id;
        });
        selected = blocks[0].id;
        render();
        if (!blocks[0].inner) { focusText(blocks[0].id, false); } // containers stay selected as a whole
        scrollTo(blocks[0].id);
    }

    function insertType(type, ownerId) {
        if (C.formTypes.indexOf(type) >= 0 && (!C.formsAllowed || formCount() > 0)) { return; }
        insertBlocks([make(type)], ownerId);
    }

    function remove(id) {
        var loc = find(id);
        if (!loc) { return; }
        var b = loc.block, hasInner = false;
        walk(b.inner || [], function (x) { if (x.type !== 'column') { hasInner = true; } });
        if (hasInner && !window.confirm('Remove this ' + CAT[b.type].label.toLowerCase() + ' and everything in it?')) { return; }
        snapshot();
        loc.list.splice(loc.index, 1);
        var next = loc.list[loc.index - 1] || loc.list[loc.index] || (loc.parent && loc.parent.type !== 'column' ? loc.parent : null);
        selected = next ? next.id : null;
        render();
        if (next) { focusText(next.id, true); }
    }

    function move(id, dir) {
        var loc = find(id);
        if (!loc) { return; }
        var to = loc.index + dir;
        if (to < 0 || to >= loc.list.length) { return; }
        snapshot();
        loc.list.splice(to, 0, loc.list.splice(loc.index, 1)[0]);
        render();
        scrollTo(id);
    }

    function duplicate(id) {
        var loc = find(id);
        if (!loc) { return; }
        if (containsForm(loc.block)) { notice('A page can have only one form, so this block can\'t be copied.', 'error'); return; }
        var copy = adopt(strip([loc.block]))[0];
        snapshot();
        loc.list.splice(loc.index + 1, 0, copy);
        selected = copy.id;
        render();
    }

    function setAttr(b, name, value, rerender) {
        snapshot();
        b.attrs[name] = value;
        if (b.type === 'columns' && name === 'layout') {
            var n = widths(value).length, cols = b.inner;
            while (cols.length < n) { cols.push({ id: uid(), type: 'column', attrs: {}, inner: [] }); }
            while (cols.length > n) { var extra = cols.pop(); cols[cols.length - 1].inner = cols[cols.length - 1].inner.concat(extra.inner); }
        }
        if (rerender !== false) { renderCanvas(); }
    }

    // ================================================================ drawing the page

    function render() {
        renderCanvas();
        renderInspector();
        renderInserter();
    }

    function renderCanvas() {
        hideSlash();
        $canvas.empty();
        if (!state.blocks.length) {
            $canvas.append(startScreen());
            return;
        }
        var $root = listEl('root', state.blocks).addClass('be-root');
        $canvas.append($root);
        $('<button type="button" class="be-appender be-root-appender" data-owner="root"><i class="fa fa-plus"></i> Add block</button>').appendTo($canvas);
        makeSortable();
        markSelected();
        flushPreviews();
    }

    function listEl(ownerId, blocks) {
        var $list = $('<div class="be-list">').attr('data-owner', ownerId);
        blocks.forEach(function (b) { $list.append(blockEl(b)); });
        if (ownerId !== 'root') {
            $('<button type="button" class="be-appender"><i class="fa fa-plus"></i> Add block</button>').attr('data-owner', ownerId).appendTo($list);
        }
        return $list;
    }

    function rt(tag, b, field, placeholder, plain) {
        var $el = $(tag).addClass('be-rt').attr({ contenteditable: 'true', 'data-field': field, 'data-placeholder': placeholder, 'aria-label': placeholder, spellcheck: 'true' });
        if (plain) { $el.attr('data-plain', '1').text(b.attrs[field] || ''); } else { $el.html(b.attrs[field] || ''); }
        return $el;
    }

    function blockEl(b) {
        var def = CAT[b.type], a = b.attrs;
        var $el = $('<div class="be-block">').attr({ 'data-id': b.id, 'data-type': b.type, tabindex: 0, role: 'group', 'aria-label': def.label });
        switch (b.type) {
            case 'paragraph':
                $el.append(rt('<p>', b, 'html', 'Type, or press / to choose a block').addClass('be-text-' + a.align));
                break;
            case 'heading':
                $el.append(rt('<h' + (a.level || '2') + '>', b, 'html', 'Heading').addClass('pw-h be-text-' + a.align));
                break;
            case 'list':
                $el.append(rt(a.ordered ? '<ol>' : '<ul>', b, 'html', 'List').addClass('pw-ul').html(a.html || '<li></li>'));
                break;
            case 'quote':
                $el.append($('<blockquote class="pw-blockquote">').append(rt('<p>', b, 'html', 'Quote')).append(rt('<cite>', b, 'cite', 'Who said it (optional)', true)));
                break;
            case 'table':
                var $t = $('<table class="table table-bordered pw-table be-table">');
                (a.rows || []).forEach(function (row, r) {
                    var $tr = $('<tr>').appendTo($t);
                    row.forEach(function (cell, c) {
                        $('<' + (r === 0 && a.header ? 'th' : 'td') + '>').addClass('be-cell').attr({ contenteditable: 'true', 'data-r': r, 'data-c': c, 'aria-label': 'Row ' + (r + 1) + ', column ' + (c + 1) }).text(cell).appendTo($tr);
                    });
                });
                $el.append($('<div class="table-responsive">').append($t));
                break;
            case 'image':
                if (a.src) {
                    var $img = $('<img class="pw-img">').attr({ src: srcUrl(a.src), alt: a.alt || '' });
                    $el.append($img);
                } else {
                    $el.append(placeholder('fa-image', 'Choose an image from the media library.', 'Choose image', 'be-choose-image'));
                }
                break;
            case 'button':
                $el.append($('<div>').addClass('be-text-' + (a.align === 'center' ? 'center' : 'left')).append(rt('<a class="btn btn-primary">', b, 'text', 'Button text', true)));
                break;
            case 'separator':
                $el.append('<hr class="pw-hr">');
                break;
            case 'spacer':
                $el.append($('<div class="be-placeholder" aria-hidden="true">').css({ height: (a.height || 40) + 'px', padding: 0, background: 'repeating-linear-gradient(45deg,#fafafa,#fafafa 6px,#f0f0f0 6px,#f0f0f0 12px)' }));
                break;
            case 'section':
                $el.addClass('be-section pw-section pw-bg-' + a.bg + ' pw-pad-' + a.padding);
                $el.append($('<span class="be-label">').text('Section'));
                $el.append(listEl(b.id, b.inner));
                break;
            case 'columns':
                $el.addClass('be-columns').append($('<span class="be-label">').text('Columns'));
                var $cols = $('<div class="be-cols">').appendTo($el);
                widths(a.layout).forEach(function (w, i) {
                    var col = b.inner[i];
                    $('<div class="be-col">').css('flex', w + ' 1 0px').append(listEl(col.id, col.inner)).appendTo($cols);
                });
                break;
            default:
                var key = previewKey(b);
                var $p = $('<div class="be-preview">').attr('data-key', key);
                if (previews[key] !== undefined) {
                    $p.html(previews[key] || '<div class="pw-empty">' + esc(def.label) + ': nothing to show yet.</div>');
                } else {
                    $p.html('<div class="be-loading">' + esc(def.label) + '…</div>');
                    if (!inflight[key]) { pendingPreviews[key] = { type: b.type, attrs: a }; }
                }
                $el.append($p);
        }
        return $el;
    }

    function placeholder(icon, text, button, cls) {
        return $('<div class="be-placeholder">').append($('<i>').addClass('fa ' + icon)).append($('<span>').text(text))
            .append($('<button type="button" class="btn btn-default btn-sm">').addClass(cls).text(button));
    }

    function startScreen() {
        var $s = $('<div class="be-start">');
        $('<h3>').text('Start this page').appendTo($s);
        $('<p class="text-muted">').text('Begin with a ready-made page and change the words and pictures, or start from nothing.').appendTo($s);
        var $g = $('<div class="be-templates">').appendTo($s);
        $.each(C.patterns, function (key, p) {
            if (!p.template) { return; }
            $('<button type="button" class="be-template">').attr('data-pattern', key).append($('<b>').text(p.title)).append($('<small>').text(p.description)).appendTo($g);
        });
        $('<button type="button" class="btn btn-default" id="beBlank">').text('Start with an empty page').appendTo($s);
        return $s;
    }

    // ---------------------------------------------------------------- server previews

    function previewKey(b) { return b.type + ':' + JSON.stringify(b.attrs); }

    function flushPreviews() {
        clearTimeout(previewTimer);
        previewTimer = setTimeout(function () {
            var keys = Object.keys(pendingPreviews);
            if (!keys.length) { return; }
            var blocks = keys.map(function (k) { return pendingPreviews[k]; });
            pendingPreviews = {};
            keys.forEach(function (k) { inflight[k] = true; });
            $.post(C.renderUrl, { form_token: C.token, blocks: JSON.stringify(blocks) }, null, 'json').done(function (r) {
                keys.forEach(function (k, i) {
                    delete inflight[k];
                    previews[k] = r && r.html ? r.html[i] || '' : '';
                    $canvas.find('.be-preview').filter(function () { return $(this).attr('data-key') === k; })
                        .html(previews[k] || '<div class="pw-empty">Nothing to show yet.</div>');
                });
            }).fail(function () {
                keys.forEach(function (k) { delete inflight[k]; delete previews[k]; });
                $canvas.find('.be-loading').text('The preview could not be loaded.');
            });
        }, 60);
    }

    // ================================================================ selection and the block toolbar

    function select(id) {
        if (selected === id) { return; }
        selected = id;
        markSelected();
        renderInspector();
    }

    function markSelected() {
        $canvas.find('.be-block.is-selected').removeClass('is-selected');
        $canvas.find('.be-toolbar').remove();
        if (!selected) { return; }
        var loc = find(selected);
        if (!loc) { selected = null; return; }
        var $el = blockElById(selected).addClass('is-selected');
        $el.prepend(toolbar(loc.block));
    }

    function blockElById(id) { return $canvas.find('.be-block').filter(function () { return $(this).attr('data-id') === id; }).first(); }

    function tb(icon, label, action, extra) {
        return $('<button type="button">').attr({ title: label, 'aria-label': label, 'data-action': action }).addClass(extra || '').append($('<i>').addClass('fa ' + icon));
    }

    function toolbar(b) {
        var def = CAT[b.type];
        var $t = $('<div class="be-toolbar" role="toolbar">').attr('aria-label', def.label + ' tools');
        $t.append(tb('fa-arrows', 'Drag to move', 'none', 'be-drag'));
        $t.append($('<span class="be-name">').text(def.label));
        $t.append('<span class="be-sep"></span>');
        $t.append(tb('fa-chevron-up', 'Move up (Alt+Up)', 'up')).append(tb('fa-chevron-down', 'Move down (Alt+Down)', 'down'));
        if (TEXT[b.type] || b.type === 'button') {
            $t.append('<span class="be-sep"></span>');
            if (b.type !== 'button') {
                $t.append(tb('fa-bold', 'Bold (Ctrl+B)', 'bold')).append(tb('fa-italic', 'Italic (Ctrl+I)', 'italic'));
            }
            $t.append(tb('fa-link', 'Link (Ctrl+K)', 'link'));
        }
        $t.append('<span class="be-sep"></span>');
        $t.append(tb('fa-clone', 'Duplicate (Ctrl+Shift+D)', 'duplicate'));
        $t.append(tb('fa-bookmark-o', 'Save as a pattern to reuse', 'pattern'));
        $t.append(tb('fa-trash-o', 'Remove', 'remove'));
        return $t;
    }

    function focusText(id, atEnd) {
        var $el = blockElById(id).find('.be-rt, .be-cell').first();
        if (!$el.length) { return; }
        var el = $el[0];
        el.focus();
        var range = document.createRange();
        range.selectNodeContents(el);
        range.collapse(!atEnd);
        var sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);
    }

    function scrollTo(id) {
        var $el = blockElById(id);
        if (!$el.length) { return; }
        var top = $el.offset().top, view = $(window).scrollTop(), h = $(window).height();
        if (top < view + 140 || top > view + h - 80) { $('html, body').animate({ scrollTop: top - 160 }, 150); }
    }

    // ================================================================ settings panel (right)

    function renderInspector() {
        $insp.empty();
        var loc = selected ? find(selected) : null;
        if (!loc) {
            var $e = $('<div class="be-insp-empty">').appendTo($insp);
            $('<h4>').text('Page').appendTo($e);
            $('<p>').text('Select a block on the page to change its settings here.').appendTo($e);
            $('<p>').html('Add blocks with <b>Add</b> at the top, the <b>+</b> buttons on the page, or by typing <kbd>/</kbd> in an empty paragraph.').appendTo($e);
            $('<p>').text('Changes are saved as a draft automatically every minute. Visitors see the page only when you publish it.').appendTo($e);
            return;
        }
        var b = loc.block, def = CAT[b.type];
        var $head = $('<div class="be-insp-head">').appendTo($insp);
        $head.append($('<i>').addClass('fa ' + def.icon)).append($('<div>').append($('<b>').text(def.label)).append($('<small>').text(def.hint || inspectorHint(b.type))));
        $.each(def.fields || {}, function (name, f) {
            if (CANVAS_FIELDS[f.type] && f.type !== 'table') { return; }
            $insp.append(fieldEl(b, name, f));
        });
        if (b.type === 'table') { $insp.append(tableControls(b)); }
        warnings(b);
    }

    function inspectorHint(type) {
        return {
            paragraph: 'Type on the page. Select text to make it bold or a link.',
            heading: 'Type on the page.',
            list: 'Type on the page. Press Enter for a new item.',
            quote: 'Type the quote and who said it on the page.',
            section: 'A band across the page with a background. Put blocks or columns inside it.',
            columns: 'Blocks side by side. On phones they stack.',
            table: 'Type in the cells on the page.',
            button: 'Type the button text on the page.'
        }[type] || '';
    }

    function warnings(b) {
        var a = b.attrs, msg = '';
        if (b.type === 'image' && a.src && !$.trim(a.alt) && !a.decorative) { msg = 'Add a description, or mark the image as decorative. The page can\'t be published without one.'; }
        if (b.type === 'image_text' && a.src && !$.trim(a.alt)) { msg = 'Add an image description. The page can\'t be published without one.'; }
        if (b.type === 'image_gallery' && (a.images || []).some(function (i) { return !$.trim(i.alt); })) { msg = 'Every gallery image needs a description before the page can be published.'; }
        if (msg) { $('<p class="be-warn">').text(msg).appendTo($insp); }
    }

    function fieldId() { return 'bef_' + Math.random().toString(36).slice(2, 9); }

    function fieldEl(b, name, f) {
        var id = fieldId(), value = b.attrs[name];
        var $g = $('<div class="form-group">');
        var commit = function (v) { setAttr(b, name, v); };
        if (f.type === 'check') {
            $('<label class="be-check">').append($('<input type="checkbox">').attr('id', id).prop('checked', !!value).on('change', function () {
                setAttr(b, name, this.checked ? 1 : 0);
                renderInspector();
            })).append(' ' + esc(f.label)).appendTo($g);
            return $g;
        }
        $('<label>').attr('for', id).text(f.label).appendTo($g);
        switch (f.type) {
            case 'select':
                var $s = $('<select class="form-control input-sm">').attr('id', id).appendTo($g);
                $.each(f.options, function (k, v) { $('<option>').val(k).text(v).appendTo($s); });
                $s.val(String(value)).on('change', function () { commit($(this).val()); renderInspector(); });
                break;
            case 'number':
                $('<input type="number" class="form-control input-sm">').attr({ id: id, min: f.min, max: f.max }).val(value).appendTo($g)
                    .on('change', function () { commit(Math.max(f.min, Math.min(f.max, parseInt(this.value, 10) || f['default']))); });
                break;
            case 'textarea':
            case 'html':
                $('<textarea class="form-control input-sm" rows="5">').attr('id', id).val(value).appendTo($g).on('change', function () { commit(this.value); });
                if (f.type === 'html') { $('<p class="be-help">').text('Basic formatting: <b>bold</b>, <i>italic</i>, <a href="...">links</a>, lists.').appendTo($g); }
                break;
            case 'image':
                if (value) { $('<img class="pw-img" style="max-height:120px;margin-bottom:6px">').attr('src', srcUrl(value)).appendTo($g); }
                var $ig = $('<div class="input-group input-group-sm">').appendTo($g);
                $('<input type="text" class="form-control" placeholder="Choose an image">').attr('id', id).val(value).appendTo($ig).on('change', function () { commit(this.value); renderInspector(); });
                $('<span class="input-group-btn">').append($('<button type="button" class="btn btn-default">').text('Choose').on('click', function () {
                    openMedia(false, function (list) { commit(list[0]); renderInspector(); });
                })).appendTo($ig);
                break;
            case 'items':
                $g.append(itemsEditor(b, name, f));
                break;
            case 'images':
                $g.append(imagesEditor(b, name, f));
                break;
            case 'table':
                return $();
            default: // text, url
                $('<input type="text" class="form-control input-sm">').attr('id', id).attr('placeholder', f.type === 'url' ? 'https://… or a page such as page/about-us' : '').val(value).appendTo($g)
                    .on('change', function () { commit(this.value); renderInspector(); });
        }
        return $g;
    }

    function itemsEditor(b, name, f) {
        var $w = $('<div>');
        var items = b.attrs[name] || [];
        items.forEach(function (item, i) {
            var $box = $('<div class="be-item-box">').appendTo($w);
            $.each(f.fields, function (sub, sf) {
                var id = fieldId();
                $('<label>').attr('for', id).text(sf.label).appendTo($box);
                var $in = sf.type === 'textarea' ? $('<textarea class="form-control input-sm" rows="3">') : $('<input type="text" class="form-control input-sm">');
                $in.attr('id', id).val(item[sub] === undefined ? '' : item[sub]).css('margin-bottom', '6px').appendTo($box).on('change', function () {
                    var copy = clone(b.attrs[name]);
                    copy[i][sub] = this.value;
                    setAttr(b, name, copy);
                });
            });
            $('<button type="button" class="btn btn-link btn-xs text-danger">').text('Remove').appendTo($box).on('click', function () {
                var copy = clone(b.attrs[name]);
                copy.splice(i, 1);
                setAttr(b, name, copy);
                renderInspector();
            });
        });
        if (items.length < f.max) {
            $('<button type="button" class="btn btn-default btn-xs"><i class="fa fa-plus"></i> Add</button>').appendTo($w).on('click', function () {
                var copy = clone(b.attrs[name] || []), item = {};
                $.each(f.fields, function (sub, sf) { item[sub] = sf['default'] || ''; });
                copy.push(item);
                setAttr(b, name, copy);
                renderInspector();
            });
        }
        return $w;
    }

    function imagesEditor(b, name, f) {
        var $w = $('<div class="be-thumbs">');
        (b.attrs[name] || []).forEach(function (img, i) {
            var $row = $('<div class="be-thumb">').appendTo($w);
            $('<img>').attr({ src: srcUrl(img.src), alt: '' }).appendTo($row);
            var $right = $('<div style="flex:1">').appendTo($row);
            $('<input type="text" class="form-control" placeholder="Describe this image">').attr('aria-label', 'Description of image ' + (i + 1)).val(img.alt).appendTo($right).on('change', function () {
                var copy = clone(b.attrs[name]);
                copy[i].alt = this.value;
                setAttr(b, name, copy);
                renderInspector();
            });
            $('<button type="button" class="btn btn-link btn-xs text-danger">').text('Remove').appendTo($right).on('click', function () {
                var copy = clone(b.attrs[name]);
                copy.splice(i, 1);
                setAttr(b, name, copy);
                renderInspector();
            });
        });
        if ((b.attrs[name] || []).length < f.max) {
            $('<button type="button" class="btn btn-default btn-sm"><i class="fa fa-plus"></i> Add images</button>').appendTo($w).on('click', function () {
                openMedia(true, function (list) {
                    var copy = clone(b.attrs[name] || []);
                    list.forEach(function (src) { if (copy.length < f.max) { copy.push({ src: src, alt: '' }); } });
                    setAttr(b, name, copy);
                    renderInspector();
                });
            });
        }
        return $w;
    }

    function tableControls(b) {
        var $w = $('<div class="form-group">');
        $('<label>').text('Rows and columns').appendTo($w);
        var $g = $('<div class="btn-group btn-group-xs" style="display:flex;flex-wrap:wrap;gap:4px">').appendTo($w);
        var change = function (fn) { var rows = clone(b.attrs.rows); fn(rows); setAttr(b, 'rows', rows); renderInspector(); };
        var width = function (rows) { return rows.length ? rows[0].length : 0; };
        $('<button type="button" class="btn btn-default">').text('+ Row').appendTo($g).on('click', function () { change(function (r) { if (r.length < 40) { r.push(new Array(width(r) || 1).fill('')); } }); });
        $('<button type="button" class="btn btn-default">').text('+ Column').appendTo($g).on('click', function () { change(function (r) { if (width(r) < 8) { r.forEach(function (row) { row.push(''); }); } }); });
        $('<button type="button" class="btn btn-default">').text('- Row').appendTo($g).on('click', function () { change(function (r) { if (r.length > 1) { r.pop(); } }); });
        $('<button type="button" class="btn btn-default">').text('- Column').appendTo($g).on('click', function () { change(function (r) { if (width(r) > 1) { r.forEach(function (row) { row.pop(); }); } }); });
        return $w;
    }

    // ================================================================ the block list (left)

    function renderInserter() {
        var q = $.trim($('#beSearch').val()).toLowerCase();
        var match = function (text) { return !q || String(text).toLowerCase().indexOf(q) >= 0; };
        var $bl = $('#beBlocksList').empty();
        var formsOff = !C.formsAllowed || formCount() > 0;
        GROUPS.forEach(function (g) {
            var items = [];
            $.each(CAT, function (type, def) {
                if (def.group === g[0] && match(def.label + ' ' + (def.keywords || '') + ' ' + (def.hint || ''))) { items.push([type, def]); }
            });
            if (!items.length) { return; }
            $('<h6>').text(g[1]).appendTo($bl);
            var $grid = $('<div class="be-grid">').appendTo($bl);
            items.forEach(function (it) {
                var off = C.formTypes.indexOf(it[0]) >= 0 && formsOff;
                $('<button type="button" class="be-item">').attr({ 'data-type': it[0], title: off ? 'A page can have one form, and none on the homepage.' : (it[1].hint || '') })
                    .prop('disabled', off).append($('<i>').addClass('fa ' + it[1].icon)).append($('<span>').text(it[1].label)).appendTo($grid);
            });
        });
        if (!$bl.children().length) { $('<p class="text-muted">').text('No blocks match.').appendTo($bl); }

        var $pl = $('#bePatternsList').empty();
        var section = function (title, entries) {
            if (!entries.length) { return; }
            $('<h6>').text(title).appendTo($pl);
            entries.forEach(function (e) { $pl.append(e); });
        };
        var built = [], pages = [];
        $.each(C.patterns, function (key, p) {
            if (!match(p.title + ' ' + p.description)) { return; }
            var $b = $('<button type="button" class="be-pattern">').attr('data-pattern', key).append($('<b>').text(p.title)).append($('<small>').text(p.description));
            (p.template ? pages : built).push($b);
        });
        var saved = (C.savedPatterns || []).filter(function (p) { return match(p.title); }).map(function (p) {
            return $('<div class="be-pattern-row">').append($('<button type="button" class="be-pattern">').attr('data-saved', p.id).append($('<b>').text(p.title)).append($('<small>').text('Saved by your school')))
                .append($('<button type="button" class="be-pattern-del">').attr({ 'data-del': p.id, title: 'Delete this pattern', 'aria-label': 'Delete ' + p.title }).html('&times;'));
        });
        section('Patterns', built);
        section('Saved by your school', saved);
        section('Whole pages (replace this page)', pages);
        if (!$pl.children().length) { $('<p class="text-muted">').text('No patterns match.').appendTo($pl); }
    }

    function usePattern(blocks, wholePage) {
        var fresh = adopt(blocks);
        if (!C.formsAllowed || formCount() > 0) {
            var before = fresh.length;
            fresh = fresh.filter(function (b) { return !containsForm(b); });
            if (fresh.length < before) { notice('The form in this pattern was left out: a page can have one form, and none on the homepage.', 'error'); }
        }
        if (wholePage) {
            if (state.blocks.length && !window.confirm('Replace everything on this page with this template? You can undo this.')) { return; }
            snapshot();
            state.blocks = fresh;
            selected = null;
            render();
            return;
        }
        // Patterns are sections: they go after the section the cursor is in.
        if (selected) {
            var loc = find(selected);
            while (loc && loc.parent) { loc = find(loc.parent.id); }
            selected = loc ? loc.block.id : null;
        }
        insertBlocks(fresh);
    }

    // ================================================================ the "/" menu

    function slashMatches(query) {
        var q = query.toLowerCase(), out = [];
        $.each(CAT, function (type, def) {
            if (def.group === 'hidden' || (C.formTypes.indexOf(type) >= 0 && (!C.formsAllowed || formCount() > 0))) { return; }
            if (!q || (def.label + ' ' + (def.keywords || '')).toLowerCase().indexOf(q) >= 0) { out.push(type); }
        });
        return out.slice(0, 9);
    }

    function showSlash(id, query) {
        var items = slashMatches(query);
        hideSlash();
        if (!items.length) { return; }
        slash = { id: id, items: items, index: 0 };
        var $el = blockElById(id);
        var $m = $('<div class="be-slash" role="listbox" aria-label="Choose a block">');
        items.forEach(function (type, i) {
            $('<button type="button" role="option">').attr('data-type', type).toggleClass('active', i === 0)
                .append($('<i>').addClass('fa ' + CAT[type].icon)).append($('<span>').text(CAT[type].label)).appendTo($m);
        });
        $m.css({ top: '100%', left: 0, marginTop: 4 }).appendTo($el);
    }

    function hideSlash() { $('.be-slash').remove(); slash = null; }

    function chooseSlash(type) {
        var id = slash.id;
        hideSlash();
        var loc = find(id);
        if (!loc) { return; }
        var b = make(type);
        snapshot();
        if (canHold(contextOf(loc.parent), type)) {
            loc.list.splice(loc.index, 1, b); // the "/text" paragraph becomes the block
        } else {
            loc.block.attrs.html = '';
            placeAfter(id, b);
        }
        selected = b.id;
        render();
        focusText(b.id, false);
    }

    // ================================================================ typing on the page

    function blockOf(el) { var $b = $(el).closest('.be-block'); return $b.length ? find($b.attr('data-id')) : null; }

    $canvas.on('input', '.be-rt', function () {
        var loc = blockOf(this);
        if (!loc) { return; }
        var field = $(this).attr('data-field');
        typingSnapshot();
        loc.block.attrs[field] = $(this).attr('data-plain') ? this.textContent : this.innerHTML;
        if (loc.block.type === 'paragraph') {
            var m = /^\/([a-z ]{0,20})$/i.exec(this.textContent);
            if (m) { showSlash(loc.block.id, $.trim(m[1])); } else if (slash) { hideSlash(); }
        }
    });

    $canvas.on('input', '.be-cell', function () {
        var loc = blockOf(this);
        if (!loc) { return; }
        typingSnapshot();
        loc.block.attrs.rows[+$(this).attr('data-r')][+$(this).attr('data-c')] = this.textContent;
    });

    // Pasting keeps the words, not the formatting of wherever they came from.
    $canvas.on('paste', '.be-rt, .be-cell', function (e) {
        e.preventDefault();
        var text = (e.originalEvent.clipboardData || window.clipboardData).getData('text/plain') || '';
        if ($(this).attr('data-plain') || $(this).hasClass('be-cell') || /^H\d$/.test(this.tagName)) {
            document.execCommand('insertText', false, text.replace(/\s*\n\s*/g, ' '));
        } else {
            document.execCommand('insertHTML', false, esc(text).replace(/\r?\n/g, '<br>'));
        }
    });

    function caretAtStart(el) {
        var sel = window.getSelection();
        if (!sel.rangeCount) { return false; }
        var r = sel.getRangeAt(0).cloneRange();
        r.setStart(el, 0);
        return r.toString() === '' && sel.isCollapsed;
    }

    $canvas.on('keydown', '.be-rt, .be-cell', function (e) {
        if (slash) {
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                slash.index = (slash.index + (e.key === 'ArrowDown' ? 1 : -1) + slash.items.length) % slash.items.length;
                $('.be-slash button').removeClass('active').eq(slash.index).addClass('active');
                return;
            }
            if (e.key === 'Enter') { e.preventDefault(); chooseSlash(slash.items[slash.index]); return; }
            if (e.key === 'Escape') { e.preventDefault(); e.stopPropagation(); hideSlash(); return; }
        }
        var loc = blockOf(this);
        if (!loc) { return; }
        var b = loc.block, el = this;
        if (e.key === 'Enter' && !e.shiftKey) {
            if ($(el).hasClass('be-cell') || $(el).attr('data-plain')) { e.preventDefault(); return; }
            if (b.type === 'list') {
                var li = $(window.getSelection().anchorNode).closest('li')[0];
                if (li && li === el.lastElementChild && $.trim(li.textContent) === '') {
                    e.preventDefault();
                    $(li).remove();
                    b.attrs.html = el.innerHTML;
                    insertBlocks([make('paragraph')]);
                }
                return;
            }
            e.preventDefault();
            var rest = '';
            if (b.type === 'paragraph') {
                // Everything after the cursor moves to a new paragraph.
                var sel = window.getSelection();
                if (sel.rangeCount) {
                    var r = sel.getRangeAt(0);
                    r.deleteContents();
                    var tail = document.createRange();
                    tail.setStart(r.endContainer, r.endOffset);
                    tail.setEnd(el, el.childNodes.length);
                    var holder = document.createElement('div');
                    holder.appendChild(tail.extractContents());
                    rest = holder.innerHTML.replace(/^(<br\s*\/?>)+$/i, '');
                    if ($.trim(el.textContent) === '' && !/<img/i.test(el.innerHTML)) { el.innerHTML = ''; }
                    b.attrs.html = el.innerHTML;
                }
            }
            snapshot();
            var p = make('paragraph', { html: rest });
            placeAfter(b.id, p);
            selected = p.id;
            typingOpen = false;
            render();
            focusText(p.id, false);
            return;
        }
        if (e.key === 'Backspace' && b.type === 'paragraph' && $.trim(el.textContent) === '' && caretAtStart(el)) {
            e.preventDefault();
            var prev = loc.list[loc.index - 1];
            snapshot();
            loc.list.splice(loc.index, 1);
            selected = prev ? prev.id : (loc.parent && loc.parent.type !== 'column' ? loc.parent.id : null);
            render();
            if (prev) { focusText(prev.id, true); }
        }
    });

    // Links inside the editor never navigate away.
    $canvas.on('click', 'a', function (e) { e.preventDefault(); });

    // ================================================================ selecting, toolbar and buttons on the page

    $canvas.on('mousedown focusin', '.be-block', function (e) {
        if ($(e.target).closest('.be-toolbar').length) { return; }
        var id = $(e.target).closest('.be-block').attr('data-id');
        if (id) { select(id); }
        e.stopPropagation();
    });

    $('#beMain').on('mousedown', function (e) {
        if (!$(e.target).closest('.be-block, .be-slash, .be-appender, .be-start').length) {
            selected = null;
            markSelected();
            renderInspector();
        }
    });

    // Toolbar buttons keep the text selection (for bold and links).
    $canvas.on('mousedown', '.be-toolbar button', function (e) { if ($(this).attr('data-action') !== 'none') { e.preventDefault(); } });
    $canvas.on('click', '.be-toolbar button', function () {
        var action = $(this).attr('data-action'), id = selected;
        if (!id) { return; }
        if (action === 'up') { move(id, -1); } else if (action === 'down') { move(id, 1); }
        else if (action === 'duplicate') { duplicate(id); } else if (action === 'remove') { remove(id); }
        else if (action === 'pattern') { savePattern(id); }
        else if (action === 'bold' || action === 'italic') { document.execCommand(action); syncActive(); }
        else if (action === 'link') { makeLink(); }
    });

    function syncActive() {
        var el = document.activeElement;
        if (el && $(el).hasClass('be-rt')) { $(el).trigger('input'); }
    }

    function makeLink() {
        var el = document.activeElement;
        var loc = selected ? find(selected) : null;
        if (loc && loc.block.type === 'button') {
            var url = window.prompt('Where should the button go? (https://… or a page such as page/about-us)', loc.block.attrs.link || '');
            if (url !== null) { setAttr(loc.block, 'link', $.trim(url)); renderInspector(); }
            return;
        }
        if (!el || !$(el).hasClass('be-rt') || window.getSelection().isCollapsed) {
            notice('Select some text first, then choose Link.', 'error');
            return;
        }
        var href = window.prompt('Link address (https://…, or a page such as page/about-us). Leave empty to remove the link.', 'https://');
        if (href === null) { return; }
        document.execCommand(href === '' ? 'unlink' : 'createLink', false, $.trim(href));
        syncActive();
    }

    $canvas.on('click', '.be-appender', function () {
        selected = null;
        markSelected();
        renderInspector();
        openInserterFor($(this).attr('data-owner'));
    });
    $canvas.on('click', '.be-choose-image', function () {
        var loc = blockOf(this);
        openMedia(false, function (list) { setAttr(loc.block, 'src', list[0]); renderInspector(); });
    });
    $canvas.on('click', '.be-template', function () { usePattern(C.patterns[$(this).attr('data-pattern')].blocks, true); });
    $canvas.on('click', '#beBlank', function () { insertBlocks([make('paragraph')]); });

    // "Add block" in a container: the next block from the list goes in there.
    var insertOwner = null;
    function openInserterFor(owner) {
        insertOwner = owner;
        showInserter(true);
        switchTab('blocks');
        $('#beSearch').val('').focus();
        renderInserter();
    }

    // ================================================================ drag to reorder

    function makeSortable() {
        $canvas.find('.be-list').each(function () {
            var $list = $(this);
            $list.sortable({
                items: '> .be-block',
                handle: '> .be-toolbar .be-drag',
                cancel: 'input, textarea, select, option, .be-rt, .be-cell',
                connectWith: '.be-list',
                placeholder: 'be-drop',
                tolerance: 'pointer',
                cursor: 'grabbing',
                start: function () { snapshot(); },
                receive: function (e, ui) {
                    var owner = $list.attr('data-owner'), type = ui.item.attr('data-type');
                    var ctx = owner === 'root' ? 'root' : find(owner).block.type;
                    if (!canHold(ctx, type)) { $(ui.sender).sortable('cancel'); notice('That block can\'t go inside a ' + (ctx === 'column' ? 'column' : 'section') + '.', 'error'); }
                },
                // After jQuery UI has finished with the dragged element.
                stop: function () { setTimeout(syncFromDom, 0); }
            });
        });
    }

    function syncFromDom() {
        var index = {};
        walk(state.blocks, function (b) { index[b.id] = b; });
        var read = function ($list) {
            return $list.children('.be-block').map(function () {
                var b = index[$(this).attr('data-id')];
                if (b.type === 'section') { b.inner = read($(this).children('.be-list')); }
                if (b.type === 'columns') {
                    var $cols = $(this).find('> .be-cols > .be-col');
                    b.inner.forEach(function (col, i) { col.inner = read($cols.eq(i).children('.be-list')); });
                }
                return b;
            }).get();
        };
        state.blocks = read($canvas.children('.be-list'));
        render();
    }

    // ================================================================ media library

    var mediaDone = null, mediaMulti = false;
    function openMedia(multi, done) {
        mediaDone = done;
        mediaMulti = multi;
        $('#beMediaTitle').text(multi ? 'Choose images' : 'Choose an image');
        var $m = $('#beMedia').modal('show');
        var $body = $m.find('.modal-media-body').html('<p class="text-muted">Loading…</p>');
        $.post(C.mediaUrl, {}, function (html) { $body.html(html); }, 'text');
    }
    $(document).on('click', '#beMedia .img_div_modal', function () {
        var $o = $(this).find('.fadeoverlay');
        if (!mediaMulti) { $('#beMedia .fadeoverlay').not($o).removeClass('active'); }
        $o.toggleClass('active');
    });
    $('#beMediaUse').on('click', function () {
        var list = $('#beMedia .fadeoverlay.active img').map(function () { return $(this).data('img'); }).get().filter(Boolean);
        $('#beMedia').modal('hide');
        if (list.length && mediaDone) { mediaDone(list); }
    });

    // ================================================================ saving, autosave, preview, versions

    function status(text) { $('#beStatus').text(text); }

    function notice(html, kind) {
        var $n = $('#beNotice').removeClass('be-notice-ok be-notice-error').addClass(kind === 'ok' ? 'be-notice-ok' : (kind === 'error' ? 'be-notice-error' : ''));
        $n.html(html).prop('hidden', false);
        clearTimeout(notice.t);
        if (kind !== 'error') { notice.t = setTimeout(function () { $n.prop('hidden', true); }, 6000); }
    }

    function save(publish, autosave, done) {
        if (saving) { if (done) { done('busy'); } return; }
        saving = true;
        var sent = version;
        $('#bePublish, #beSave').prop('disabled', true);
        status(publish ? 'Publishing…' : 'Saving…');
        $.ajax({
            type: 'POST', url: C.saveUrl, dataType: 'json',
            data: { form_token: C.token, layout: JSON.stringify({ v: 2, blocks: strip(state.blocks) }), publish: publish ? 1 : 0, autosave: autosave ? 1 : 0 }
        }).done(function (r) {
            if (r.ok) {
                if (version === sent) { dirty = false; }
                status((publish ? 'Published at ' : (autosave ? 'Saved automatically at ' : 'Draft saved at ')) + r.saved_at);
                if (!autosave) { notice(esc(r.message), 'ok'); }
                if (done) { done(); }
            } else {
                status('Not saved');
                var list = r.problems ? '<ul>' + r.problems.map(function (p) { return '<li>' + esc(p) + '</li>'; }).join('') + '</ul>' : '';
                notice(r.problems ? '<b>Not published yet.</b> Fix these first:' + list : esc(r.message || 'Not saved.'), 'error');
                if (done) { done(r.message || 'error'); }
            }
        }).fail(function () {
            status('Not saved: check your connection');
            if (!autosave) { notice('Saving failed. Check your connection and try again.', 'error'); }
            if (done) { done('failed'); }
        }).always(function () {
            saving = false;
            $('#bePublish, #beSave').prop('disabled', false);
        });
    }

    setInterval(function () { if (dirty && !saving) { save(false, true); } }, 60000);

    $('#beSave').on('click', function () { save(false, false); });
    $('#bePublish').on('click', function () { save(true, false); });

    $('#bePreview').on('click', function () {
        save(false, false, function (err) {
            if (err) { return; }
            var url = C.previewUrl + (C.previewUrl.indexOf('?') >= 0 ? '&' : '?') + 't=' + Date.now();
            $('#bePreviewFrame').attr('src', url);
            $('#bePreviewTab').attr('href', url);
            $('#bePreviewModal').modal('show');
        });
    });
    $('.be-devices').on('click', 'button', function () {
        $(this).addClass('active').siblings().removeClass('active');
        $('#bePreviewFrame').css('width', $(this).attr('data-width'));
    });

    $('#beRevisions').on('click', function () {
        var $b = $('#beRevisionsBody').html('<p class="text-muted">Loading…</p>');
        $('#beRevisionsModal').modal('show');
        $.getJSON(C.revisionsUrl).done(function (r) {
            $b.empty();
            if (!r.revisions || !r.revisions.length) { $b.html('<p class="text-muted">No earlier versions yet. A version is kept each time the page is saved or published.</p>'); return; }
            $('<p class="text-muted">').text('Loading a version puts it in the editor. Visitors see it only after you publish.').appendTo($b);
            r.revisions.forEach(function (v) {
                $('<div class="be-rev">').append($('<div class="be-flex">').append($('<b>').text(v.what)).append(' ' + esc(v.when) + (v.who ? ' by ' + esc(v.who) : '')))
                    .append($('<button type="button" class="btn btn-default btn-xs">').text('Load').attr('data-rev', v.id)).appendTo($b);
            });
        }).fail(function () { $b.html('<p class="text-danger">The versions could not be loaded.</p>'); });
    });
    $('#beRevisionsBody').on('click', '[data-rev]', function () {
        var id = $(this).attr('data-rev');
        $.getJSON(C.revisionUrl + '/' + id).done(function (r) {
            if (!r.ok) { notice(esc(r.message), 'error'); return; }
            snapshot();
            state.blocks = adopt(r.layout.blocks);
            selected = null;
            render();
            $('#beRevisionsModal').modal('hide');
            notice('That version is now in the editor. Save or publish to keep it, or undo to go back.', 'ok');
        });
    });

    function savePattern(id) {
        var loc = find(id);
        if (!loc) { return; }
        var title = window.prompt('Name this pattern so you can add it to other pages:', CAT[loc.block.type].label);
        if (!title) { return; }
        $.post(C.patternSaveUrl, { form_token: C.token, title: title, blocks: JSON.stringify(strip([loc.block])) }, null, 'json').done(function (r) {
            notice(esc(r.message || ''), r.ok ? 'ok' : 'error');
            if (r.ok) { C.savedPatterns = r.patterns; renderInserter(); }
        });
    }

    $('#bePatternsList').on('click', '[data-del]', function () {
        if (!window.confirm('Delete this saved pattern? Pages that already use it keep their copy.')) { return; }
        $.post(C.patternDeleteUrl + '/' + $(this).attr('data-del'), { form_token: C.token }, null, 'json').done(function (r) {
            if (r.ok) { C.savedPatterns = r.patterns; renderInserter(); }
        });
    });
    $('#bePatternsList').on('click', '[data-pattern]', function () {
        var p = C.patterns[$(this).attr('data-pattern')];
        usePattern(p.blocks, p.template);
    });
    $('#bePatternsList').on('click', '[data-saved]', function () {
        var id = +$(this).attr('data-saved');
        var p = (C.savedPatterns || []).filter(function (x) { return x.id === id; })[0];
        if (p) { usePattern(p.blocks, false); }
    });

    // ================================================================ the block list: tabs, search, clicks

    function switchTab(tab) {
        $('.be-tab').removeClass('active').attr('aria-selected', 'false').filter('[data-tab="' + tab + '"]').addClass('active').attr('aria-selected', 'true');
        $('#beBlocksList').prop('hidden', tab !== 'blocks');
        $('#bePatternsList').prop('hidden', tab !== 'patterns');
    }
    function showInserter(show) {
        $('#beInserter').prop('hidden', !show);
        $('#beToggleInserter').attr('aria-expanded', show ? 'true' : 'false');
    }
    $('.be-tab').on('click', function () { switchTab($(this).attr('data-tab')); });
    $('#beSearch').on('input', renderInserter);
    $('#beToggleInserter').on('click', function () {
        var show = $('#beInserter').prop('hidden');
        insertOwner = null;
        showInserter(show);
        if (show) { $('#beSearch').focus(); }
    });
    $('#beBlocksList').on('click', '.be-item', function () {
        var owner = insertOwner;
        insertOwner = null;
        insertType($(this).attr('data-type'), owner);
    });
    $('#beUndo').on('click', undo);
    $('#beRedo').on('click', redo);
    $('#beShortcuts').on('click', function (e) { e.preventDefault(); $('#beShortcutsModal').modal('show'); });

    // ================================================================ keyboard

    $(document).on('keydown', function (e) {
        var mod = e.ctrlKey || e.metaKey, k = e.key.toLowerCase();
        if ($(e.target).closest('.modal').length) { return; }
        var inField = $(e.target).is('input, textarea, select');
        if (mod && k === 'z' && !inField) { e.preventDefault(); if (e.shiftKey) { redo(); } else { undo(); } return; }
        if (mod && k === 'y' && !inField) { e.preventDefault(); redo(); return; }
        if (mod && k === 's') { e.preventDefault(); save(false, false); return; }
        if (mod && k === 'k' && $(e.target).hasClass('be-rt')) { e.preventDefault(); makeLink(); return; }
        if (mod && e.shiftKey && k === 'd' && selected) { e.preventDefault(); duplicate(selected); return; }
        if (e.altKey && (e.key === 'ArrowUp' || e.key === 'ArrowDown') && selected) { e.preventDefault(); move(selected, e.key === 'ArrowUp' ? -1 : 1); return; }
        if (e.key === 'Escape') {
            if (slash) { hideSlash(); return; }
            if (selected) { selected = null; markSelected(); renderInspector(); }
            return;
        }
        // A selected block that isn't being typed in: Delete removes it.
        if ((e.key === 'Delete' || e.key === 'Backspace') && $(e.target).hasClass('be-block') && selected) { e.preventDefault(); remove(selected); return; }
        if (e.key === 'Enter' && $(e.target).hasClass('be-block')) { e.preventDefault(); focusText(selected, true); }
    });

    $(document).on('click', '.be-slash button', function () { chooseSlash($(this).attr('data-type')); });

    window.addEventListener('beforeunload', function (e) {
        if (dirty) { e.preventDefault(); e.returnValue = ''; }
    });

    // ================================================================ start

    state.blocks = adopt((C.layout && C.layout.blocks) || []);
    // Small screens: the page first; the block list opens with Add.
    if (window.innerWidth < 992) { showInserter(false); }
    render();
    status($('#beStatus').text());
}(jQuery));
