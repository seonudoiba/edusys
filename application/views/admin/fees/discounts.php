<?php
$e = function ($v) { return html_escape((string) $v); };
$picked = array_flip((array) $this->input->post('sections'));
$chosen = (int) ($this->input->post('discount_id') ?: $this->input->get('discount'));
$can_edit_dc = $this->rbac->hasPrivilege('fees_discount', 'can_edit');
$can_del_dc = $this->rbac->hasPrivilege('fees_discount', 'can_delete');
$new_type = $this->input->post('new_type') === 'percentage' ? 'percentage' : 'fix';
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-tag"></i> Discounts <small><?php echo $e($session_name); ?> session</small></h1>
    </section>
    <section class="content">
        <?php $this->load->view('admin/fees/_nav', array('tab' => $tab)); ?>
        <p><a href="<?php echo site_url('admin/fees/adjust'); ?>"><i class="fa fa-angle-left"></i> Discounts and balances</a></p>
        <ul class="fw-steps"><li class="on">1 Discount and classes</li><li>2 Students and preview</li></ul>
        <?php if ($errors) { ?>
            <div class="alert alert-danger"><b>Please fix the following:</b><ul style="margin:6px 0 0"><?php foreach ($errors as $err) { ?><li><?php echo $e($err); ?></li><?php } ?></ul></div>
        <?php } ?>

        <form method="post" action="<?php echo site_url('admin/fees/discounts'); ?>" id="dc">
            <?php echo $this->customlib->getCSRF(); ?>
            <input type="hidden" name="step" value="students">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">1. Which discount?</h3></div>
                <div class="box-body">
                    <?php if ($discounts) { ?>
                        <div class="table-responsive">
                            <table class="table table-hover fw-table" style="margin-bottom:10px">
                                <thead><tr><th style="width:30px"></th><th>Discount</th><th class="n">Value</th><th class="n">Students this session</th><th></th></tr></thead>
                                <tbody>
                                <?php foreach ($discounts as $d) { ?>
                                    <tr>
                                        <td><input type="radio" name="discount_id" id="dc_<?php echo (int) $d['id']; ?>" value="<?php echo (int) $d['id']; ?>" <?php echo $chosen === (int) $d['id'] ? 'checked' : ''; ?>></td>
                                        <td><label for="dc_<?php echo (int) $d['id']; ?>" style="margin:0"><?php echo $e($d['name']); ?></label></td>
                                        <td class="n"><?php echo $e($this->fee_adjustments->discount_label($d, $currency_symbol)); ?></td>
                                        <td class="n"><?php echo (int) $d['students']; ?><?php echo $d['used'] ? ' <span class="fw-muted">(' . (int) $d['used'] . ' used)</span>' : ''; ?></td>
                                        <td class="n" style="white-space:nowrap">
                                            <?php if ($can_edit_dc) { ?><a class="btn btn-default btn-xs" href="<?php echo site_url('admin/feediscount/edit/' . (int) $d['id']); ?>" title="Edit name, code or value"><i class="fa fa-pencil"></i></a><?php } ?>
                                            <?php if ($can_del_dc && !(int) $d['students']) { ?><a class="btn btn-default btn-xs" href="<?php echo site_url('admin/feediscount/delete/' . (int) $d['id']); ?>" onclick="return confirm('Delete this discount?')" title="Delete (only when no student has it)"><i class="fa fa-trash"></i></a><?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                    <label style="font-weight:normal"><input type="radio" name="discount_id" value="0" id="dc_new" <?php echo !$chosen && ($this->input->post('new_name') || !$discounts) ? 'checked' : ''; ?>> <b>A new discount</b></label>
                    <div class="fw-filters" style="margin:6px 0 0 20px">
                        <input type="text" name="new_name" class="form-control" style="max-width:260px" maxlength="100" placeholder="e.g. Sibling discount" value="<?php echo $e($this->input->post('new_name')); ?>" aria-label="Discount name">
                        <select name="new_type" class="form-control" style="min-width:150px" aria-label="Kind">
                            <option value="fix" <?php echo $new_type === 'fix' ? 'selected' : ''; ?>>Fixed amount (<?php echo $e($currency_symbol); ?>)</option>
                            <option value="percentage" <?php echo $new_type === 'percentage' ? 'selected' : ''; ?>>Percentage (%)</option>
                        </select>
                        <input type="text" name="new_value" class="form-control" style="max-width:140px" inputmode="decimal" placeholder="Amount or %" value="<?php echo $e($this->input->post('new_value')); ?>" aria-label="Amount or percentage">
                    </div>
                    <p class="help-block" style="margin-left:20px">It is saved only when you apply it in the next step.</p>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">2. Students of which classes?</h3>
                    <p class="fw-muted" style="margin:4px 0 0">You pick the students themselves in the next step.</p></div>
                <div class="box-body">
                    <?php foreach ($classes as $class_id => $c) { ?>
                        <div style="margin-bottom:8px">
                            <b style="display:inline-block;min-width:110px"><?php echo $e($c['name']); ?></b>
                            <?php foreach ($c['sections'] as $section_id => $section) { $v = (int) $class_id . '-' . (int) $section_id; ?>
                                <label class="checkbox-inline"><input type="checkbox" name="sections[]" value="<?php echo $v; ?>" <?php echo isset($picked[$v]) ? 'checked' : ''; ?>> <?php echo $e($section); ?></label>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-lg">Next: choose students <i class="fa fa-angle-right"></i></button>
        </form>
        <script>
            (function () {
                var f = document.getElementById('dc');
                f.querySelectorAll('[name=new_name],[name=new_value],[name=new_type]').forEach(function (el) {
                    el.addEventListener('focus', function () { document.getElementById('dc_new').checked = true; });
                });
            })();
        </script>
    </section>
</div>
