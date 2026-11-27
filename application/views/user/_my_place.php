<?php
/*
 * The student's own transport or hostel (Student_places), at the top of Transport Routes and Hostel
 * Rooms in the portal. $kind: transport | hostel; $mine: the row, or null when nothing is assigned.
 */
$e = function ($v) {
    return html_escape((string) $v);
};
$cur = $this->customlib->getSchoolCurrencyFormat();
$is_parent = $this->customlib->getUserRole() === 'parent';
?>
<div style="display:flex;gap:14px;align-items:center;padding:14px 16px;margin:0 0 16px;border-radius:14px;border:1px solid <?php echo $mine ? '#bbf7d0' : '#e5e9f0'; ?>;background:<?php echo $mine ? 'linear-gradient(120deg,#f0fdf4,#fff)' : '#f8fafc'; ?>">
    <div style="flex:none;width:46px;height:46px;border-radius:13px;display:grid;place-items:center;color:#fff;font-size:20px;background:<?php echo $mine ? 'linear-gradient(150deg,#34d399,#059669)' : 'linear-gradient(150deg,#94a3b8,#64748b)'; ?>">
        <i class="fa <?php echo $kind === 'transport' ? 'fa-bus' : 'fa-bed'; ?>"></i>
    </div>
    <div style="flex:1;min-width:0">
        <?php if (!$mine) { ?>
            <b style="font-size:15px"><?php echo $kind === 'transport' ? 'No school transport' : 'Not in the hostel'; ?></b>
            <div class="text-muted" style="font-size:13px"><?php echo $is_parent ? 'Your child has' : 'You have'; ?> not been given <?php echo $kind === 'transport' ? 'a bus route' : 'a hostel room'; ?>. Ask the school office if this is wrong.</div>
        <?php } elseif ($kind === 'transport') { ?>
            <div style="font-size:12px;text-transform:uppercase;letter-spacing:.05em;color:#166534;font-weight:700"><?php echo $is_parent ? 'Your child\'s' : 'Your'; ?> bus</div>
            <b style="font-size:16px"><?php echo $e($mine['point']); ?><?php echo Student_places::time($mine['pickup_time']) ? ' · pickup ' . $e(Student_places::time($mine['pickup_time'])) : ''; ?></b>
            <div style="font-size:13px;color:#334155">Route <?php echo $e($mine['route_title']); ?><?php echo $mine['vehicle_no'] ? ' · bus ' . $e($mine['vehicle_no']) : ''; ?><?php echo $mine['driver_name'] ? ' · driver ' . $e($mine['driver_name']) . ($mine['driver_contact'] ? ' (' . $e($mine['driver_contact']) . ')' : '') : ''; ?><?php echo (float) $mine['fees'] > 0 ? ' · ' . $e($cur . number_format((float) $mine['fees'], 2)) . ' a month' : ''; ?></div>
        <?php } else { ?>
            <div style="font-size:12px;text-transform:uppercase;letter-spacing:.05em;color:#166534;font-weight:700"><?php echo $is_parent ? 'Your child\'s' : 'Your'; ?> room</div>
            <b style="font-size:16px"><?php echo $e($mine['hostel_name']); ?> · Room <?php echo $e($mine['room_no']); ?></b>
            <div style="font-size:13px;color:#334155"><?php echo $mine['room_type'] ? $e($mine['room_type']) . ' · ' : ''; ?><?php echo (int) $mine['no_of_bed']; ?> bed<?php echo (int) $mine['no_of_bed'] === 1 ? '' : 's'; ?><?php echo (float) $mine['cost_per_bed'] > 0 ? ' · ' . $e($cur . number_format((float) $mine['cost_per_bed'], 2)) . ' a bed' : ''; ?></div>
        <?php } ?>
    </div>
</div>
