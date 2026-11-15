<div class="content-wrapper">
    <section class="content">
        <?php if ($this->session->flashdata('msg')) { echo $this->session->flashdata('msg'); $this->session->unset_userdata('msg'); } ?>

        <div class="box box-<?php echo $restricted ? 'success' : 'warning'; ?>" id="restricted_box">
            <div class="box-body">
                <form method="post" action="<?php echo site_url('admin/teacher/subject_teachers'); ?>" class="pull-right" data-validate="off">
                    <?php echo $this->customlib->getCSRF(); ?>
                    <input type="hidden" name="class_id" value="<?php echo (int) $class_id; ?>">
                    <input type="hidden" name="section_id" value="<?php echo (int) $section_id; ?>">
                    <?php if ($can_edit) { ?>
                        <?php if ($restricted) { ?>
                            <button type="submit" name="restricted" value="no" class="btn btn-default btn-sm">Turn off</button>
                        <?php } else { ?>
                            <button type="submit" name="restricted" value="yes" class="btn btn-success btn-sm"><i class="fa fa-lock"></i> Turn on</button>
                        <?php } ?>
                    <?php } ?>
                </form>
                <?php if ($restricted) { ?>
                    <b><i class="fa fa-lock"></i> Teacher Restricted Mode is on.</b> Teachers see only the students, attendance, marks and homework of the classes and sections they teach (below) or are class teacher of (Assign Class Teacher).
                <?php } else { ?>
                    <b>Teacher Restricted Mode is off:</b> every teacher can see every class. Turn it on so teachers only see the classes and sections they teach or are class teacher of.
                <?php } ?>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
            </div>
            <form method="get" action="<?php echo site_url('admin/teacher/subject_teachers'); ?>">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('class'); ?></label><small class="req"> *</small>
                                <select name="class_id" id="st_class" class="form-control">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php foreach ($classlist as $c) { ?>
                                        <option value="<?php echo (int) $c['id']; ?>" <?php echo (int) $c['id'] === $class_id ? 'selected' : ''; ?>><?php echo html_escape($c['class']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('section'); ?></label><small class="req"> *</small>
                                <select name="section_id" id="st_section" class="form-control" data-selected="<?php echo (int) $section_id; ?>">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <label>&nbsp;</label>
                            <button type="submit" name="search" value="search" class="btn btn-primary btn-block"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <?php if ($class_id && $section_id) { ?>
        <div class="box box-info" id="subject_teacher_box">
            <div class="box-header with-border">
                <h3 class="box-title">Subject teachers</h3>
            </div>
            <?php if (!$subjects) { ?>
                <div class="box-body"><div class="alert alert-info" style="margin:0">This class and section has no subjects yet. Add them under Academics &gt; Subject Group (a group of subjects for the class and section), then come back here.</div></div>
            <?php } else { ?>
            <form method="post" action="<?php echo site_url('admin/teacher/subject_teachers'); ?>" data-validate="off">
                <?php echo $this->customlib->getCSRF(); ?>
                <input type="hidden" name="class_id" value="<?php echo (int) $class_id; ?>">
                <input type="hidden" name="section_id" value="<?php echo (int) $section_id; ?>">
                <div class="box-body table-responsive">
                    <table class="table table-hover" id="subject_teacher_table">
                        <thead><tr><th>Subject</th><th>Subject group</th><th style="width:45%">Teacher</th></tr></thead>
                        <tbody>
                        <?php foreach ($subjects as $s) { ?>
                            <tr>
                                <td><?php echo html_escape($s['name']); ?><?php if ($s['code'] !== '' && $s['code'] !== null) { ?> <span class="text-muted">(<?php echo html_escape($s['code']); ?>)</span><?php } ?></td>
                                <td class="text-muted"><?php echo html_escape($s['group_name']); ?></td>
                                <td>
                                    <select name="teacher[<?php echo (int) $s['sgs_id']; ?>]" class="form-control" <?php echo $can_edit ? '' : 'disabled'; ?>>
                                        <option value="0">&mdash; no teacher &mdash;</option>
                                        <?php foreach ($teachers as $t) { ?>
                                            <option value="<?php echo (int) $t['id']; ?>" <?php echo (int) $t['id'] === (int) $s['staff_id'] ? 'selected' : ''; ?>><?php echo html_escape(trim($t['name'] . ' ' . $t['surname']) . ($t['employee_id'] !== '' ? ' (' . $t['employee_id'] . ')' : '')); ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <?php if (!$teachers) { ?><p class="text-muted">There are no active staff with the Teacher role yet.</p><?php } ?>
                </div>
                <?php if ($can_edit) { ?>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
                <?php } ?>
            </form>
            <?php } ?>
        </div>
        <?php } ?>
    </section>
</div>
<script>
    $(function () {
        var cls = $('#st_class'), sec = $('#st_section');
        function load(selected) {
            sec.html('<option value=""><?php echo $this->lang->line('select'); ?></option>');
            if (!cls.val()) { return; }
            $.getJSON('<?php echo site_url('sections/getByClass'); ?>', { class_id: cls.val() }, function (rows) {
                $.each(rows, function (i, r) {
                    sec.append($('<option>').val(r.section_id).text(r.section).prop('selected', String(r.section_id) === String(selected)));
                });
                sec.trigger('change');
            });
        }
        cls.on('change', function () { load(''); });
        if (cls.val()) { load(sec.data('selected')); }
    });
</script>
