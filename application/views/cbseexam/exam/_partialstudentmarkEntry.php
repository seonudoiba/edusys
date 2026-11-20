<?php
/*
 * Report Cards marks entry for one subject (Exam::subjectstudent → #subjectModal). The field names
 * are what Exam::entrymarks reads: exam_student_id[], mark[es][type][marks|cbse_exam_timetable_assessment_type],
 * absent[es][type], exam_student_note[es]. The page (cbseexam/exam/index.php) validates and posts it.
 * Here: search, a filled-in counter, a live total per student, out-of-range warnings, Enter = next
 * student, sticky headings and names.
 */
$me_types = array();
foreach ((array) $exam_assessment_types as $t) {
    if (!is_null($t->cbse_exam_timetable_assessment_type_id)) {
        $me_types[] = (array) $t;
    }
}
$me_max = 0;
foreach ($me_types as $t) {
    $me_max += (float) $t['maximum_marks'];
}
$me_fmt = function ($n) { return rtrim(rtrim(number_format((float) $n, 2, '.', ''), '0'), '.'); };
?>
<style>
    .me-bar{display:flex;flex-wrap:wrap;gap:10px;align-items:center;justify-content:space-between;margin-bottom:12px}
    .me-bar .me-search{max-width:260px;border-radius:8px}
    .me-pill{display:inline-flex;align-items:center;gap:6px;background:#f1f5f9;border-radius:999px;padding:5px 12px;font-size:13px;color:#334155;font-weight:600}
    .me-pill.ok{background:#dcfce7;color:#15803d}
    .me-pill.warn{background:#fef3c7;color:#92400e}
    .me-help{font-size:12px;color:#64748b}
    .me-scroll{max-height:62vh;overflow:auto;border:1px solid #e5e9f0;border-radius:12px}
    .me-table{margin:0;border-collapse:separate;border-spacing:0;width:100%}
    .me-table thead th{position:sticky;top:0;z-index:3;background:#f8fafc;border-bottom:2px solid #e2e8f0!important;font-size:12px;text-transform:uppercase;letter-spacing:.03em;color:#475569;white-space:nowrap;vertical-align:bottom}
    .me-table thead th small{display:block;text-transform:none;letter-spacing:0;color:#94a3b8;font-weight:500}
    .me-table td{vertical-align:middle!important;border-top:1px solid #eef2f6!important}
    .me-table tbody tr:hover td{background:#f8fbff}
    .me-table tr.me-done td.me-name{box-shadow:inset 3px 0 0 #22c55e}
    .me-table .me-name{position:sticky;left:0;z-index:2;background:#fff;min-width:210px}
    .me-table thead .me-name{z-index:4;background:#f8fafc}
    .me-name b{display:block;color:#0f172a}
    .me-name small{color:#64748b}
    .me-cell{display:flex;align-items:center;gap:6px}
    .me-cell input.mark{width:84px!important;text-align:center;font-weight:600;border-radius:8px}
    .me-cell input.mark.me-bad{border-color:#dc2626;background:#fef2f2;color:#b91c1c}
    .me-cell input.mark[readonly]{background:#f1f5f9;color:#94a3b8}
    .me-abs{display:inline-flex;align-items:center;gap:4px;margin:0;font-size:11px;font-weight:700;color:#64748b;cursor:pointer;border:1px solid #e2e8f0;border-radius:6px;padding:4px 6px;user-select:none}
    .me-abs input{margin:0}
    .me-abs.on{background:#fee2e2;border-color:#fecaca;color:#b91c1c}
    .me-total{font-weight:700;color:#0f172a;white-space:nowrap}
    .me-total small{display:block;font-weight:500;color:#64748b}
    .me-table input.note{min-width:160px;border-radius:8px}
    .me-table td.text-danger{font-size:11px}
</style>
<form method="post" action="<?php echo site_url('cbseexam/exam/entrymarks') ?>" id="assign_form1111">
    <input type="hidden" name="cbse_exam_timetable_id" value="<?php echo $timetable_id; ?>">
    <?php if (isset($resultlist) && !empty($resultlist)) { ?>
        <div class="me-bar">
            <input type="search" class="form-control input-sm me-search" id="me_search" placeholder="Find a student..." aria-label="Find a student">
            <span class="me-pill" id="me_count"></span>
            <span class="me-help"><i class="fa fa-keyboard-o"></i> Enter: next student &nbsp;·&nbsp; ↑ ↓: move &nbsp;·&nbsp; ABS: absent</span>
        </div>
        <div class="me-scroll">
            <table class="table me-table">
                <thead>
                    <tr>
                        <th style="width:36px">#</th>
                        <th class="me-name"><?php echo $this->lang->line('student_name'); ?><small><?php echo $this->lang->line('admission_no'); ?> · <?php echo $this->lang->line('roll_no'); ?> · <?php echo $this->lang->line('class'); ?></small></th>
                        <?php foreach ($me_types as $t) { ?>
                            <th><?php echo html_escape($t['name']) . ($t['code'] ? ' (' . html_escape($t['code']) . ')' : ''); ?><small>max <?php echo $me_fmt($t['maximum_marks']); ?></small></th>
                        <?php } ?>
                        <?php if (count($me_types) > 1) { ?><th><?php echo $this->lang->line('total'); ?><small>of <?php echo $me_fmt($me_max); ?></small></th><?php } ?>
                        <th><?php echo $this->lang->line('note') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $me_i = 0;
                    foreach ($resultlist as $student) {
                        $me_i++;
                        $name = $this->customlib->getFullName($student['firstname'], $student['middlename'], $student['lastname'], $sch_setting->middlename, $sch_setting->lastname);
                        $roll = $exam['use_exam_roll_no'] != 0 ? $student['exam_roll_no'] : (($student['roll_no'] != 0) ? $student['roll_no'] : '-');
                        $note = '';
                        foreach ($exam_assessment_types as $t) {
                            if (array_key_exists($t->id, $student['marks']) && $student['marks'][$t->id]['note']) {
                                $note = $student['marks'][$t->id]['note'];
                            }
                        }
                        ?>
                        <tr class="std_adm_<?php echo $student['admission_no']; ?> me-row" data-text="<?php echo html_escape(strtolower($name . ' ' . $student['admission_no'] . ' ' . $roll)); ?>">
                            <td class="text-muted"><input type="hidden" name="exam_student_id[]" value="<?php echo $student['exam_student_id']; ?>"><?php echo $me_i; ?></td>
                            <td class="me-name">
                                <b><?php echo html_escape($name); ?></b>
                                <small><?php echo html_escape($student['admission_no']); ?> · <?php echo html_escape($roll); ?> · <?php echo html_escape($student['class_name'] . ' (' . $student['section_name'] . ')'); ?></small>
                            </td>
                            <?php foreach ($me_types as $value) {
                                $es = $student['exam_student_id'];
                                $absent = !empty($student['marks'][$value['id']]) && $student['marks'][$value['id']]['is_absent'] ? 1 : 0;
                                $val = !empty($student['marks'][$value['id']]['marks']) ? $student['marks'][$value['id']]['marks'] : '';
                                ?>
                                <td>
                                    <input type="hidden" value="<?php echo $value['cbse_exam_timetable_assessment_type_id']; ?>" name="mark[<?php echo $es; ?>][<?php echo $value['id'] ?>][cbse_exam_timetable_assessment_type]">
                                    <div class="me-cell">
                                        <input type="number" data-marks="<?php echo $value['maximum_marks']; ?>" class="mark form-control input-sm" name="mark[<?php echo $es; ?>][<?php echo $value['id'] ?>][marks]" value="<?php echo html_escape($val); ?>" step="any" min="0" placeholder="/<?php echo $me_fmt($value['maximum_marks']); ?>" aria-label="<?php echo html_escape($name . ' – ' . $value['name']); ?>" <?php echo $absent ? "readonly='readonly'" : ''; ?>>
                                        <label class="me-abs<?php echo $absent ? ' on' : ''; ?>" title="<?php echo $this->lang->line('mark_as_absent'); ?>">
                                            <input type="checkbox" name="absent[<?php echo $es; ?>][<?php echo $value['id'] ?>]" value="1" <?php echo $absent ? "checked='checked'" : ''; ?> class="check_absent"> ABS
                                        </label>
                                    </div>
                                </td>
                            <?php } ?>
                            <?php if (count($me_types) > 1) { ?><td class="me-total"><span class="me-sum">0</span><small class="me-pct"></small></td><?php } ?>
                            <td><input type="text" class="form-control input-sm note noteinput" name="exam_student_note[<?php echo $student['exam_student_id']; ?>]" value="<?php echo html_escape($note); ?>" placeholder="Optional"></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php if ($this->rbac->hasPrivilege('cbse_exam_marks', 'can_edit')) { ?>
            <div class="modal-footer clearboth mx-nt-lr-15 pb0" style="display:flex;align-items:center;justify-content:space-between;gap:10px">
                <span class="me-help" id="me_unsaved"></span>
                <button type="submit" class="allot-fees btn btn-primary" id="load" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please Wait.."><i class="fa fa-save"></i> <?php echo $this->lang->line('save'); ?></button>
            </div>
        <?php } ?>
        <script>
        (function ($) {
            var form = $('#assign_form1111'), MAX = <?php echo json_encode((float) $me_max); ?>, dirty = false;
            function fmt(n) { return String(Math.round(n * 100) / 100); }
            function rowState(tr) {
                var sum = 0, filled = 0, all = 0;
                tr.find('input.mark').each(function () {
                    all++;
                    var v = this.value, max = parseFloat(this.getAttribute('data-marks')), abs = $(this).closest('td').find('.check_absent').prop('checked');
                    var bad = v !== '' && (isNaN(parseFloat(v)) || parseFloat(v) < 0 || parseFloat(v) > max);
                    $(this).toggleClass('me-bad', bad).attr('title', bad ? 'Must be between 0 and ' + max : '');
                    if (abs || v !== '') { filled++; }
                    if (!abs && v !== '' && !bad) { sum += parseFloat(v); }
                });
                tr.find('.me-sum').text(fmt(sum));
                tr.find('.me-pct').text(MAX ? Math.round(sum * 100 / MAX) + '%' : '');
                tr.toggleClass('me-done', filled === all);
                return filled === all;
            }
            function counter() {
                var rows = form.find('tr.me-row'), done = 0, bad = form.find('input.mark.me-bad').length;
                rows.each(function () { if (rowState($(this))) { done++; } });
                $('#me_count').attr('class', 'me-pill ' + (bad ? 'warn' : (done === rows.length ? 'ok' : '')))
                    .html('<i class="fa ' + (bad ? 'fa-exclamation-triangle' : 'fa-check-circle') + '"></i> ' + done + ' of ' + rows.length + ' students complete' + (bad ? ' · ' + bad + ' out of range' : ''));
            }
            form.on('input change', 'input.mark, .check_absent, input.note', function () {
                rowState($(this).closest('tr'));
                counter();
                if (!dirty) { dirty = true; $('#me_unsaved').html('<i class="fa fa-circle text-warning"></i> Unsaved changes'); }
            });
            form.on('change', '.check_absent', function () { $(this).closest('.me-abs').toggleClass('on', this.checked); });
            // Enter / arrows: same column, next or previous student
            form.on('keydown', 'input.mark', function (e) {
                var down = e.key === 'Enter' || e.key === 'ArrowDown', up = e.key === 'ArrowUp';
                if (!down && !up) { return; }
                e.preventDefault();
                var td = $(this).closest('td'), col = td.index(), tr = td.closest('tr');
                var next = down ? tr.nextAll('tr.me-row:visible').first() : tr.prevAll('tr.me-row:visible').first();
                if (next.length) { next.children().eq(col).find('input.mark').focus().select(); }
            });
            $('#me_search').on('input', function () {
                var q = this.value.toLowerCase().trim();
                form.find('tr.me-row').each(function () { this.style.display = !q || this.getAttribute('data-text').indexOf(q) > -1 ? '' : 'none'; });
            });
            // a save on its way clears the warning; a failed save brings it back
            $(document).off('.me').on('ajaxSend.me', function (e, xhr, s) {
                if (s.url && s.url.indexOf('entrymarks') > -1) { dirty = false; $('#me_unsaved').text(''); }
            }).on('ajaxError.me', function (e, xhr, s) {
                if (s.url && s.url.indexOf('entrymarks') > -1) { dirty = true; $('#me_unsaved').html('<i class="fa fa-circle text-danger"></i> Not saved'); }
            });
            $('#subjectModal').off('hide.bs.modal.me').on('hide.bs.modal.me', function (e) {
                if (dirty && !window.confirm('You have marks that are not saved. Close without saving?')) { e.preventDefault(); return; }
                dirty = false;
            });
            counter();
        })(jQuery);
        </script>
    <?php } else { ?>
        <div class="alert alert-info"><?php echo $this->lang->line('no_record_found'); ?></div>
    <?php } ?>
</form>
