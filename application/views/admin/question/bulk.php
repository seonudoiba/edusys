<?php
/*
 * Add questions (Question::bulk / bulk_save): the class, section, subject and level once, then any
 * number of question cards; Save all saves them together (none if one has a problem). Optionally
 * put them straight into an online exam with the same marks each.
 * $classList, $subjectlist, $question_level, $exams, $exam_id, $subject_id.
 */
$preset_subject = $subject_id;
if (!$preset_subject && $exam_id) {
    foreach ($exams as $e) {
        if ((int) $e['id'] === $exam_id && !empty($e['subject_id'])) { $preset_subject = (int) $e['subject_id']; }
    }
}
?>
<style>
    .qb-card { border: 1px solid #dfe3e8; border-radius: 4px; padding: 12px 14px; margin-bottom: 12px; background: #fff; position: relative; }
    .qb-card.has-error { border-color: #dd4b39; }
    .qb-card .qb-no { font-weight: 600; margin-right: 10px; }
    .qb-card .qb-remove { position: absolute; top: 8px; right: 10px; }
    .qb-card textarea { resize: vertical; }
    .qb-opt { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
    .qb-opt .qb-letter { width: 18px; font-weight: 600; text-align: center; }
    .qb-opt input[type=text] { flex: 1 1 auto; }
    .qb-opt label { margin: 0; font-weight: normal; white-space: nowrap; }
    .qb-error { color: #dd4b39; margin-top: 6px; }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-list-ol"></i> Add questions</h1>
    </section>
    <section class="content">
        <form id="bulk_form" action="<?php echo site_url('admin/question/bulk_save'); ?>" method="post" data-validate="off" data-autoload="off">
            <?php echo $this->customlib->getCSRF(); ?>
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">For all the questions below</h3></div>
                <div class="box-body">
                    <div id="bulk_head_errors"></div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="bq_class">Class</label><small class="req"> *</small>
                                <select id="bq_class" name="class_id" class="form-control">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php foreach ($classList as $c) { ?><option value="<?php echo (int) $c['id']; ?>"><?php echo html_escape($c['class']); ?></option><?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="bq_section">Section</label>
                                <select id="bq_section" name="section_id" class="form-control"><option value="">All sections</option></select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="bq_subject">Subject</label><small class="req"> *</small>
                                <select id="bq_subject" name="subject_id" class="form-control">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php foreach ($subjectlist as $s) { ?>
                                        <option value="<?php echo (int) $s['id']; ?>" <?php echo (int) $s['id'] === $preset_subject ? 'selected' : ''; ?>><?php echo html_escape($s['name'] . ($s['code'] ? ' (' . $s['code'] . ')' : '')); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="bq_level">Level</label>
                                <select id="bq_level" name="level" class="form-control">
                                    <?php foreach ($question_level as $k => $v) { ?><option value="<?php echo html_escape($k); ?>" <?php echo $k === 'medium' ? 'selected' : ''; ?>><?php echo html_escape($v); ?></option><?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group" style="margin-bottom:0">
                                <label for="bq_exam">Also add them to an online exam <span class="text-muted" style="font-weight:normal">(optional)</span></label>
                                <select id="bq_exam" name="exam_id" class="form-control">
                                    <option value="">No, only the question bank</option>
                                    <?php foreach ($exams as $e) { ?><option value="<?php echo (int) $e['id']; ?>" <?php echo (int) $e['id'] === $exam_id ? 'selected' : ''; ?>><?php echo html_escape($e['exam']); ?></option><?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3 js-exam-marks">
                            <div class="form-group" style="margin-bottom:0">
                                <label for="bq_marks">Marks for each</label>
                                <input id="bq_marks" name="marks" type="number" min="0" step="0.5" value="1" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-3 js-exam-marks">
                            <div class="form-group" style="margin-bottom:0">
                                <label for="bq_neg">Negative marks for each</label>
                                <input id="bq_neg" name="neg_marks" type="number" min="0" step="0.25" value="0" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="bulk_cards"></div>

            <div class="box box-solid">
                <div class="box-body">
                    <button type="button" class="btn btn-default" id="bq_add"><i class="fa fa-plus"></i> Add another question</button>
                    <span class="text-muted" style="margin-left:10px">Tip: press <b>Ctrl+Enter</b> in a question to add the next one.</span>
                    <button type="submit" class="btn btn-primary pull-right" id="bq_save"><i class="fa fa-save"></i> Save all questions</button>
                </div>
            </div>
        </form>
    </section>
</div>

<template id="bq_tpl">
    <div class="qb-card">
        <button type="button" class="btn btn-default btn-xs qb-remove" title="Remove this question"><i class="fa fa-times"></i></button>
        <div class="form-inline" style="margin-bottom:8px">
            <span class="qb-no"></span>
            <select class="form-control input-sm qb-type" data-f="type">
                <option value="singlechoice">Single choice (one right answer)</option>
                <option value="multichoice">Multiple choice (several right answers)</option>
                <option value="true_false">True or false</option>
                <option value="descriptive">Written answer</option>
            </select>
        </div>
        <textarea class="form-control qb-text" data-f="text" rows="2" placeholder="Type the question"></textarea>
        <div class="qb-options" style="margin-top:8px"></div>
        <div class="qb-tf" style="margin-top:8px;display:none">
            <label class="radio-inline"><input type="radio" value="true" data-f="correct"> True</label>
            <label class="radio-inline"><input type="radio" value="false" data-f="correct"> False</label>
            <span class="text-muted" style="margin-left:8px">the right answer</span>
        </div>
        <p class="qb-desc text-muted" style="margin:8px 0 0;display:none">Students type their answer; a teacher marks it under Evaluation.</p>
        <div class="qb-error"></div>
    </div>
</template>

<script>
(function ($) {
    var next = 0, letters = ['a', 'b', 'c', 'd', 'e'];

    function optionsHtml(n, multi) {
        var h = '';
        letters.forEach(function (l) {
            h += '<div class="qb-opt"><span class="qb-letter">' + l.toUpperCase() + '</span>'
                + '<input type="text" class="form-control input-sm" name="q[' + n + '][opt_' + l + ']" placeholder="Option ' + l.toUpperCase() + (l > 'b' ? ' (optional)' : '') + '">'
                + (multi ? '<label><input type="checkbox" name="q[' + n + '][ans][]" value="opt_' + l + '"> right</label>'
                         : '<label><input type="radio" name="q[' + n + '][correct]" value="opt_' + l + '"> right</label>')
                + '</div>';
        });
        return h;
    }

    function setType(card) {
        var n = card.data('n'), type = card.find('.qb-type').val(), keep = {};
        card.find('.qb-options input[type=text]').each(function () { keep[this.name.replace(/.*\[(opt_\w)\]$/, '$1')] = this.value; });
        var box = card.find('.qb-options').empty();
        if (type === 'singlechoice' || type === 'multichoice') {
            box.html(optionsHtml(n, type === 'multichoice'));
            box.find('input[type=text]').each(function () { var k = this.name.replace(/.*\[(opt_\w)\]$/, '$1'); if (keep[k]) { this.value = keep[k]; } });
        }
        card.find('.qb-tf').toggle(type === 'true_false');
        card.find('.qb-desc').toggle(type === 'descriptive');
    }

    function renumber() {
        $('#bulk_cards .qb-card').each(function (i) { $(this).find('.qb-no').text('Question ' + (i + 1)); });
        $('.qb-remove').toggle($('#bulk_cards .qb-card').length > 1);
    }

    function addCard(type) {
        var n = next++, card = $($('#bq_tpl').html());
        card.data('n', n).attr('data-n', n);
        card.find('[data-f]').each(function () {
            var f = $(this).data('f');
            this.name = 'q[' + n + '][' + f + ']';
        });
        if (type) { card.find('.qb-type').val(type); }
        $('#bulk_cards').append(card);
        setType(card);
        renumber();
        return card;
    }

    $(document).on('change', '.qb-type', function () { setType($(this).closest('.qb-card')); });
    $(document).on('click', '.qb-remove', function () { $(this).closest('.qb-card').remove(); renumber(); });
    $('#bq_add').on('click', function () {
        var last = $('#bulk_cards .qb-card').last().find('.qb-type').val();
        addCard(last).find('.qb-text').focus();
    });
    $(document).on('keydown', '.qb-text', function (e) {
        if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) { e.preventDefault(); $('#bq_add').click(); }
    });

    // sections of the class
    $('#bq_class').on('change', function () {
        var sel = $('#bq_section').html('<option value="">All sections</option>');
        if (!this.value) { return; }
        $.getJSON(baseurl + 'sections/getByClass', { class_id: this.value }, function (d) {
            $.each(d || [], function (i, o) { sel.append($('<option>').val(o.section_id).text(o.section)); });
        });
    });
    $('#bq_exam').on('change', function () { $('.js-exam-marks').toggle(!!this.value); }).trigger('change');

    $('#bulk_form').on('submit', function (e) {
        e.preventDefault();
        var form = this, btn = $('#bq_save').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        $('#bulk_head_errors').empty();
        $('.qb-card').removeClass('has-error').find('.qb-error').empty();
        $.ajax({ url: form.action, type: 'POST', data: $(form).serialize(), dataType: 'json' })
            .done(function (r) {
                if (r.status === 1) { window.location = r.redirect; return; }
                var errs = r.errors || {};
                if (errs.head) {
                    $('#bulk_head_errors').html('<div class="alert alert-danger">' + errs.head.map(function (t) { return $('<div>').text(t).html(); }).join('<br>') + '</div>');
                }
                var first = null;
                $.each(errs.q || {}, function (n, text) {
                    var card = $('#bulk_cards .qb-card[data-n="' + n + '"]').addClass('has-error');
                    card.find('.qb-error').text(text + ' Nothing was saved yet.');
                    first = first || card;
                });
                var target = errs.head ? $('#bulk_head_errors') : first;
                if (target && target.length) { $('html, body').animate({ scrollTop: target.offset().top - 80 }, 200); }
                btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save all questions');
            })
            .fail(function () {
                errorMsg('The questions could not be saved. Check your connection and try again.');
                btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save all questions');
            });
    });

    for (var i = 0; i < 5; i++) { addCard('singlechoice'); }
})(jQuery);
</script>
