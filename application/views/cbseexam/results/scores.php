<?php
/* Report Cards > Classes & Results > a subject's scores (Results::scores): every assessment as a column. */
$n = function ($v) { return rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.'); };
$locked = $lock_reason !== '';
$can_type = $this->rbac->hasPrivilege('cbse_exam_marks', 'can_edit') && !$locked;
$status = isset($tt['marks_status']) ? $tt['marks_status'] : 'draft';
$back = site_url('cbseexam/results/klass/' . (int) $class['id'] . '?term=' . (int) $exam['cbse_term_id']);
?>
<style>
    .sc-top{display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;justify-content:space-between;margin-bottom:12px}
    .sc-top h3{margin:0;font-weight:800}
    .sc-bar{display:flex;flex-wrap:wrap;gap:10px;align-items:center;background:#fff;border:1px solid #e5e9f0;border-radius:14px;padding:10px 14px;margin-bottom:12px}
    .sc-bar .gap{flex:1}
    .sc-pill{display:inline-flex;gap:6px;align-items:center;border-radius:99px;padding:5px 12px;font-size:12.5px;font-weight:700;background:#f1f5f9;color:#334155}
    .sc-pill.ok{background:#dcfce7;color:#15803d}.sc-pill.warn{background:#fef3c7;color:#92400e}.sc-pill.lock{background:#fee2e2;color:#b91c1c}
    .sc-wrap{background:#fff;border:1px solid #e5e9f0;border-radius:14px;overflow:auto;max-height:68vh}
    .sc-table{width:100%;border-collapse:separate;border-spacing:0}
    .sc-table th{position:sticky;top:0;z-index:2;background:#f8fafc;font-size:11px;text-transform:uppercase;letter-spacing:.04em;color:#475569;padding:9px 10px;border-bottom:2px solid #e2e8f0;white-space:nowrap;text-align:center}
    .sc-table th small{display:block;text-transform:none;letter-spacing:0;color:#94a3b8;font-weight:600}
    .sc-table td{padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:center;vertical-align:middle}
    .sc-table td.nm,.sc-table th.nm{text-align:left;position:sticky;left:0;background:#fff;z-index:1;min-width:200px}
    .sc-table th.nm{z-index:3;background:#f8fafc}
    .sc-table input.sc{width:78px;text-align:center;font-weight:700;border:1px solid #dbe2ea;border-radius:8px;padding:6px 4px}
    .sc-table input.sc:focus{outline:2px solid var(--ui-brand,#1f4e79);border-color:transparent}
    .sc-table input.bad{border-color:#dc2626;background:#fef2f2;color:#b91c1c}
    .sc-table input[readonly]{background:#f8fafc;color:#64748b}
    .sc-table tr.done td.nm{box-shadow:inset 3px 0 0 #22c55e}
    .sc-tot{font-weight:800}
    .sc-cbt{display:inline-block;margin-top:3px;border-radius:6px;padding:1px 6px;font-size:10px;font-weight:800;background:#ede9fe;color:#6d28d9}
    @media (max-width:640px){.sc-table input.sc{width:60px}.sc-table td.nm,.sc-table th.nm{min-width:140px}}
</style>
<div class="content-wrapper">
    <section class="content">
        <div class="sc-top">
            <div>
                <a href="<?php echo $back; ?>" class="text-muted"><i class="fa fa-angle-left"></i> <?php echo html_escape($class['class'] . ' · ' . $term_name); ?></a>
                <h3><?php echo html_escape($subject['name']); ?> scores</h3>
            </div>
            <div class="text-muted">Type a score, or ABS for absent. Enter moves down.</div>
        </div>
        <div class="sc-bar">
            <?php if ($locked) { ?><span class="sc-pill lock"><i class="fa fa-lock"></i> <?php echo html_escape($lock_reason); ?></span>
            <?php } else { ?><span class="sc-pill" id="sc_count"></span><?php } ?>
            <span class="sc-pill"><?php echo html_escape(implode(' + ', array_map(function ($t) use ($n) { return $t['name'] . ' ' . $n($t['maximum_marks']); }, $subject['types']))); ?> = <?php echo $n($subject['max']); ?></span>
            <span class="gap"></span>
            <span id="sc_state" class="text-muted"></span>
            <?php if ($status === 'draft' && $can_type) { ?><button type="button" class="btn btn-info btn-sm sc-flow" data-action="submit"><i class="fa fa-paper-plane"></i> Submit for review</button><?php } ?>
            <?php if ($can_approve && $status !== 'approved') { ?><button type="button" class="btn btn-success btn-sm sc-flow" data-action="approve"><i class="fa fa-lock"></i> Approve &amp; lock</button><?php } ?>
            <?php if ($can_approve && $status === 'submitted') { ?><button type="button" class="btn btn-warning btn-sm sc-flow" data-action="return"><i class="fa fa-reply"></i> Return</button><?php } ?>
            <?php if ($can_approve && $status === 'approved') { ?><button type="button" class="btn btn-warning btn-sm sc-flow" data-action="unlock"><i class="fa fa-unlock"></i> Unlock</button><?php } ?>
            <?php if ($can_type) { ?><button type="button" class="btn btn-primary btn-sm" id="sc_save"><i class="fa fa-save"></i> Save</button><?php } ?>
        </div>
        <?php if (!$students) { ?>
            <div class="alert alert-info">No students in this class for the term.</div>
        <?php } else { ?>
        <div class="sc-wrap">
            <table class="sc-table" id="sc_table">
                <thead><tr><th class="nm">Student</th>
                    <?php foreach ($subject['types'] as $tid => $t) { ?><th><?php echo html_escape($t['name']); ?><small>out of <?php echo $n($t['maximum_marks']); ?></small><?php echo $t['cbt'] ? '<span class="sc-cbt">FROM CBT</span>' : ''; ?></th><?php } ?>
                    <th>Total<small>of <?php echo $n($subject['max']); ?></small></th></tr></thead>
                <tbody>
                <?php $last = null; foreach ($students as $es => $st) {
                    if ($last !== $st['section']) { $last = $st['section']; ?><tr><td class="nm" colspan="<?php echo count($subject['types']) + 2; ?>" style="background:#f8fafc;font-weight:800;font-size:12px"><?php echo html_escape($class['class'] . ' ' . $st['section']); ?></td></tr><?php } ?>
                    <tr class="sc-row">
                        <td class="nm"><b><?php echo html_escape($st['name']); ?></b><br><small class="text-muted"><?php echo html_escape($st['admission_no']); ?></small></td>
                        <?php foreach ($subject['types'] as $tid => $t) { $v = isset($marks[$es][$tid]) ? $marks[$es][$tid] : ''; ?>
                            <td><input class="sc" name="m[<?php echo (int) $es; ?>][<?php echo (int) $tid; ?>]" value="<?php echo html_escape($v); ?>" data-max="<?php echo (float) $t['maximum_marks']; ?>" inputmode="decimal" autocomplete="off" aria-label="<?php echo html_escape($st['name'] . ' ' . $t['name']); ?>" <?php echo $can_type ? '' : 'readonly'; ?>></td>
                        <?php } ?>
                        <td class="sc-tot">0</td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } ?>
    </section>
</div>
<script>
(function ($) {
    var table = $('#sc_table'), dirty = false, url = <?php echo json_encode(current_url()); ?>, csrf = <?php echo json_encode(array($this->security->get_csrf_token_name(), $this->security->get_csrf_hash())); ?>;
    function fmt(x) { return String(Math.round(x * 100) / 100); }
    function row(tr) {
        var sum = 0, filled = 0, all = 0;
        tr.find('input.sc').each(function () {
            all++;
            var v = $.trim(this.value).toUpperCase(), max = parseFloat(this.getAttribute('data-max'));
            var bad = v !== '' && v !== 'ABS' && v !== 'A' && (isNaN(parseFloat(v)) || parseFloat(v) < 0 || parseFloat(v) > max);
            $(this).toggleClass('bad', bad).attr('title', bad ? 'A score from 0 to ' + max + ', or ABS' : '');
            if (v !== '') { filled++; }
            if (v !== '' && v !== 'ABS' && v !== 'A' && !bad) { sum += parseFloat(v); }
        });
        tr.find('.sc-tot').text(fmt(sum));
        tr.toggleClass('done', filled === all);
    }
    function count() {
        var rows = table.find('tr.sc-row'), done = rows.filter('.done').length, bad = table.find('input.bad').length;
        $('#sc_count').attr('class', 'sc-pill ' + (bad ? 'warn' : (done === rows.length ? 'ok' : ''))).html('<i class="fa ' + (bad ? 'fa-exclamation-triangle' : 'fa-check-circle') + '"></i> ' + done + ' of ' + rows.length + ' complete' + (bad ? ' · ' + bad + ' not valid' : ''));
    }
    table.find('tr.sc-row').each(function () { row($(this)); });
    count();
    table.on('input', 'input.sc', function () { row($(this).closest('tr')); count(); dirty = true; $('#sc_state').text('Not saved yet'); });
    table.on('keydown', 'input.sc', function (e) {
        var down = e.key === 'Enter' || e.key === 'ArrowDown', up = e.key === 'ArrowUp';
        if (!down && !up) { return; }
        e.preventDefault();
        var td = $(this).closest('td'), col = td.index(), tr = td.closest('tr');
        var next = down ? tr.nextAll('tr.sc-row').first() : tr.prevAll('tr.sc-row').first();
        if (next.length) { next.children().eq(col).find('input').focus().select(); }
    });
    function save() {
        if (table.find('input.bad').length) { errorMsg('Fix the scores in red first.'); return $.Deferred().reject(); }
        var data = table.find('input.sc').serializeArray();
        data.push({ name: csrf[0], value: csrf[1] });
        $('#sc_save').prop('disabled', true);
        return $.post(url, data, null, 'json').done(function (r) {
            if (r.status === 'success') { dirty = false; $('#sc_state').text('Saved ' + new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })); successMsg(r.message); }
            else { errorMsg(r.message); }
        }).fail(function () { errorMsg('Could not save. Check your connection and try again.'); }).always(function () { $('#sc_save').prop('disabled', false); });
    }
    $('#sc_save').on('click', save);
    $(document).on('keydown', function (e) { if ((e.ctrlKey || e.metaKey) && e.key === 's') { e.preventDefault(); if ($('#sc_save').length) { save(); } } });
    window.addEventListener('beforeunload', function (e) { if (dirty) { e.preventDefault(); e.returnValue = ''; } });
    $('.sc-flow').on('click', function () {
        var action = $(this).data('action'), note = '';
        if (dirty && (action === 'submit' || action === 'approve')) { errorMsg('Save the scores first.'); return; }
        if (action === 'unlock' || action === 'return') { note = window.prompt(action === 'unlock' ? 'Why are you unlocking these scores?' : 'What should the teacher change?', ''); if (!note) { return; } }
        else if (!window.confirm(action === 'approve' ? 'Approve and lock these scores?' : 'Submit these scores for review? You cannot change them unless they are returned.')) { return; }
        var d = { 'timetable_id[]': <?php echo (int) $tt['id']; ?>, action: action, note: note };
        d[csrf[0]] = csrf[1];
        $.post(<?php echo json_encode(site_url('cbseexam/exam/marks_status')); ?>, d, function (r) {
            if (r.status === 'success') { successMsg(r.message); setTimeout(function () { location.reload(); }, 600); } else { errorMsg(r.message); }
        }, 'json');
    });
})(jQuery);
</script>
