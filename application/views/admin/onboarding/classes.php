<?php
$existing_names = array();
foreach ($existing as $c) {
    $existing_names[mb_strtolower($c->class)] = true;
}
$n = 0;
?>
<style>
    .ob-stage { margin-bottom: 18px; }
    .ob-stage h4 { font-size: 15px; margin: 0 0 8px; }
    .ob-classes { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 8px; }
    .ob-class { display: flex; align-items: center; gap: 8px; border: 1px solid #e3e6ea; border-radius: 4px; padding: 6px 8px; }
    .ob-class input[type=text] { flex: 1; min-width: 0; border: 0; border-bottom: 1px dashed #ccc; padding: 2px 0; background: transparent; }
    .ob-class .label { font-weight: normal; }
    .ob-existing li { margin-bottom: 4px; }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-mortar-board"></i> Set up classes and arms</h1>
    </section>
    <section class="content">
        <form method="post" action="<?php echo site_url('admin/onboarding/classes'); ?>" id="ob-classes-form">
            <?php echo $this->customlib->getCSRF(); ?>
            <div class="row">
                <div class="col-md-8">
                    <?php if ($this->session->flashdata('msg')) { echo $this->session->flashdata('msg'); } ?>
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">1. Tick the classes your school has</h3>
                        </div>
                        <div class="box-body">
                            <p class="text-muted">These are the usual Nigerian classes. Untick the ones you don't run; you can rename any class before saving.</p>
                            <div class="form-group">
                                <label class="radio-inline"><input type="radio" name="naming" value="standard" checked> Primary 1&ndash;6, JSS 1&ndash;3</label>
                                <label class="radio-inline"><input type="radio" name="naming" value="basic"> Basic 1&ndash;9 (UBE naming)</label>
                            </div>
                            <?php foreach ($stages as $stage => $spec) { list($label, $classes, $default_on) = $spec; ?>
                                <div class="ob-stage">
                                    <h4><?php echo html_escape($label); ?>
                                        <small><a href="#" class="ob-toggle" data-stage="<?php echo $stage; ?>">tick all / none</a></small></h4>
                                    <div class="ob-classes">
                                        <?php foreach ($classes as $c) { $n++;
                                            $on = $default_on === null || in_array($c[0], $default_on, true); ?>
                                            <label class="ob-class">
                                                <input type="checkbox" name="classes[<?php echo $n; ?>][on]" value="1" data-stage="<?php echo $stage; ?>" <?php echo $on ? 'checked' : ''; ?>>
                                                <input type="text" name="classes[<?php echo $n; ?>][name]" value="<?php echo html_escape($c[0]); ?>" maxlength="60"
                                                       data-standard="<?php echo html_escape($c[0]); ?>" data-basic="<?php echo html_escape($c[1]); ?>" aria-label="Class name">
                                                <input type="hidden" name="classes[<?php echo $n; ?>][stage]" value="<?php echo $stage; ?>">
                                                <?php if (isset($existing_names[mb_strtolower($c[0])])) { ?><span class="label label-default" title="Already exists: only missing arms are added">exists</span><?php } ?>
                                            </label>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">2. Arms (sections) in each class</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="ob-arms">Arms</label>
                                <input type="text" class="form-control" id="ob-arms" name="arms" value="A" maxlength="400">
                                <p class="help-block">Separate with commas, e.g. <code>A, B, C</code> or <code>Gold, Diamond</code>. Leave empty if each class is a single group: students go in an arm called General.</p>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" name="ss_departments" value="1" checked>
                                    Split senior secondary classes into departments: <?php echo html_escape(implode(', ', $departments)); ?></label>
                                <p class="help-block">SS classes then get these instead of the arms above. You can rename or add arms later under Academics &gt; Sections.</p>
                            </div>
                            <?php if ($samples) { ?>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="remove_samples" value="1" checked>
                                        Remove the sample classes (<?php echo html_escape(implode(', ', $samples)); ?>)</label>
                                    <p class="help-block">They came with your account as examples. Only classes with no students, applications or fees are listed.</p>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Create classes</button>
                            <a href="<?php echo site_url('admin/onboarding'); ?>" class="btn btn-default">Back to checklist</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="box box-solid">
                        <div class="box-header with-border"><h3 class="box-title">Classes you have now</h3></div>
                        <div class="box-body">
                            <?php if (!$existing) { ?>
                                <p class="text-muted">None yet.</p>
                            <?php } else { ?>
                                <ul class="list-unstyled ob-existing">
                                    <?php foreach (array_reverse($existing) as $c) { ?>
                                        <li><strong><?php echo html_escape($c->class); ?></strong>
                                            <span class="text-muted"><?php echo html_escape(implode(', ', array_map(function ($s) { return $s->section; }, $c->vehicles))); ?></span></li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                            <p class="small text-muted">Saving again is safe: existing classes are kept and only get the arms they are missing.</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>
<script>
(function () {
    var form = document.getElementById('ob-classes-form');
    form.querySelectorAll('input[name=naming]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            var key = this.value;
            form.querySelectorAll('input[data-standard]').forEach(function (input) {
                input.value = input.getAttribute('data-' + key);
            });
        });
    });
    form.querySelectorAll('.ob-toggle').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            var boxes = form.querySelectorAll('input[type=checkbox][data-stage="' + this.getAttribute('data-stage') + '"]');
            var anyOff = Array.prototype.some.call(boxes, function (b) { return !b.checked; });
            boxes.forEach(function (b) { b.checked = anyOff; });
        });
    });
})();
</script>
