<?php
/* Transport > Assign Students / Hostel > Assign Students (admin/Studentplaces). $kind: transport | hostel */
$e = function ($v) {
    return html_escape((string) $v);
};
$is_t = $kind === 'transport';
$cur = $this->customlib->getSchoolCurrencyFormat();
$msg = $this->session->flashdata('sp_msg');
$base = 'admin/studentplaces/' . $kind;
// options grouped: transport by route and vehicle, hostel by hostel
$groups = array();
if ($is_t) {
    foreach ($options as $key => $o) {
        $groups[$o['route_title'] . ' · ' . $o['vehicle_no']][$key] = $o['route_title'] . ': ' . $o['point'] . ((float) $o['fees'] > 0 ? ' (' . $cur . number_format((float) $o['fees'], 2) . ')' : '') . ($o['pickup_time'] ? ' · ' . date('g:i a', strtotime($o['pickup_time'])) : '');
    }
} else {
    foreach ($rooms as $id => $r) {
        $groups[$r['hostel']][$id] = $r['hostel'] . ': room ' . $r['room_no'] . ($r['beds'] > 0 ? ' (' . $r['used'] . ' of ' . $r['beds'] . ' beds)' : '');
    }
}
$select = function ($name, $selected, $id = '') use ($groups, $e, $is_t) {
    $h = '<select name="' . $name . '" class="form-control input-sm sp-sel"' . ($id ? ' id="' . $id . '"' : '') . '><option value="">' . ($is_t ? 'No transport' : 'Not in the hostel') . '</option>';
    foreach ($groups as $g => $opts) {
        $h .= '<optgroup label="' . $e($g) . '">';
        foreach ($opts as $v => $label) {
            $h .= '<option value="' . $e($v) . '"' . ((string) $v === (string) $selected ? ' selected' : '') . '>' . $e($label) . '</option>';
        }
        $h .= '</optgroup>';
    }
    return $h . '</select>';
};
?>
<div class="content-wrapper">
    <section class="content">
        <?php if ($msg) { ?><div class="alert alert-<?php echo $e($msg[0]); ?>"><?php echo $e($msg[1]); ?></div><?php } ?>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa <?php echo $is_t ? 'fa-bus' : 'fa-bed'; ?>"></i> Assign students to <?php echo $is_t ? 'transport' : 'the hostel'; ?></h3>
                <div class="box-tools pull-right">
                    <?php if ($is_t) { ?><a class="btn btn-sm btn-default" href="<?php echo site_url('admin/pickuppoint/student_fees'); ?>"><i class="fa fa-money"></i> Transport fee months</a><?php } else { ?><a class="btn btn-sm btn-default" href="<?php echo site_url('admin/hostelroom'); ?>"><i class="fa fa-bed"></i> Hostel rooms</a><?php } ?>
                </div>
            </div>
            <div class="box-body">
                <form method="get" action="<?php echo site_url($base); ?>" style="display:flex;flex-wrap:wrap;gap:8px;align-items:flex-end;margin-bottom:12px">
                    <div><label for="sp_class" style="display:block;font-size:12px">Class</label>
                        <select id="sp_class" name="class_id" class="form-control" onchange="this.form.section_id.value='';this.form.submit()">
                            <option value="">Choose a class</option>
                            <?php foreach ($classes as $c) { ?><option value="<?php echo (int) $c['id']; ?>" <?php echo (int) $c['id'] === $class_id ? 'selected' : ''; ?>><?php echo $e($c['class']); ?></option><?php } ?>
                        </select></div>
                    <div><label for="sp_section" style="display:block;font-size:12px">Section</label>
                        <select id="sp_section" name="section_id" class="form-control" onchange="this.form.submit()">
                            <option value="">All sections</option>
                            <?php foreach ($sections as $s) { ?><option value="<?php echo (int) $s['section_id']; ?>" <?php echo (int) $s['section_id'] === $section_id ? 'selected' : ''; ?>><?php echo $e($s['section']); ?></option><?php } ?>
                        </select></div>
                </form>

                <?php if (!$groups) { ?>
                    <div class="alert alert-info"><?php echo $is_t
                        ? 'No routes yet. Set up Routes, Vehicles, Assign Vehicle and Route Pickup Point in the Transport menu first.'
                        : 'No hostel rooms yet. Add a hostel and its rooms in the Hostel menu first.'; ?></div>
                <?php } elseif (!$class_id) { ?>
                    <p class="text-muted" style="padding:16px 0">Choose a class to list its students.</p>
                <?php } elseif (!$students) { ?>
                    <p class="text-muted" style="padding:16px 0">No students in this class<?php echo $section_id ? ' and section' : ''; ?>.</p>
                <?php } else { ?>
                    <?php echo form_open($base === 'admin/studentplaces/transport' ? 'admin/studentplaces/save_transport' : 'admin/studentplaces/save_hostel', array('id' => 'sp_form')); ?>
                    <input type="hidden" name="class_id" value="<?php echo (int) $class_id; ?>"><input type="hidden" name="section_id" value="<?php echo (int) $section_id; ?>">
                    <div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin-bottom:10px;padding:8px 10px;background:#f8fafc;border:1px solid #e5e9f0;border-radius:8px">
                        <b style="font-size:13px">For every student below:</b>
                        <div style="min-width:260px"><?php echo $select('', '', 'sp_all'); ?></div>
                        <button type="button" class="btn btn-default btn-sm" id="sp_apply">Apply to all</button>
                        <span class="text-muted small">then change any student, and Save.</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th>Student</th><th>Admission no.</th><th>Section</th><th style="min-width:300px"><?php echo $is_t ? 'Route, vehicle and pickup point' : 'Hostel room'; ?></th></tr></thead>
                            <tbody>
                            <?php foreach ($students as $s) {
                                $now = $is_t ? ($s['route_pickup_point_id'] ? (int) $s['vehroute_id'] . ':' . (int) $s['route_pickup_point_id'] : '') : ((int) $s['hostel_room_id'] ?: ''); ?>
                                <tr>
                                    <td><b><?php echo $e(trim(preg_replace('/\s+/', ' ', $s['name']))); ?></b></td>
                                    <td><?php echo $e($s['admission_no']); ?></td>
                                    <td><?php echo $e($s['section']); ?></td>
                                    <td><?php echo $is_t ? $select('place[' . (int) $s['ss_id'] . ']', $now) : $select('room[' . (int) $s['student_id'] . ']', $now); ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;margin-top:6px">
                        <?php if ($is_t) { ?>
                            <label style="font-weight:normal;margin:0"><input type="checkbox" name="charge" value="1" <?php echo $months ? 'checked' : 'disabled'; ?>> Charge newly added students this session's transport fees (<?php echo (int) $months; ?> month<?php echo $months === 1 ? '' : 's'; ?>)<?php if (!$months) { ?> <span class="text-muted">: none set up in Transport &gt; Fees Master</span><?php } ?></label>
                        <?php } else { ?>
                            <label style="font-weight:normal;margin:0"><input type="checkbox" name="charge" value="1" checked> Charge the room's hostel fee (its cost per bed, once this session) <span class="text-muted small">· a room never gets more students than its beds</span></label>
                        <?php } ?>
                        <button class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                    </div>
                    <?php echo form_close(); ?>
                <?php } ?>
            </div>
        </div>
    </section>
</div>
<script>
$(function () {
    $('#sp_apply').on('click', function () { var v = $('#sp_all').val(); $('#sp_form select.sp-sel').not('#sp_all').val(v); });
    $('#sp_form').on('submit', function () { $('#sp_all').prop('disabled', true); });
});
</script>
