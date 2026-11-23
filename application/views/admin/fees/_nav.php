<?php
/* The fees workspace: tabs and shared styles. $tab: overview | collections | create | adjust | followup */
$tabs = array(
    'overview' => array('admin/fees', 'fa-pie-chart', 'Overview'),
    'collections' => array('admin/fees/collections', 'fa-money', 'Record payments'),
    'create' => array('admin/fees/create', 'fa-plus-circle', 'Create fees'),
    'adjust' => array('admin/fees/adjust', 'fa-sliders', 'Discounts & balances'),
    'followup' => array('admin/fees/followup', 'fa-bell-o', 'Reminders and reports'),
);
?>
<style>
    .fw-tabs { display: flex; flex-wrap: wrap; gap: 6px; margin: 0 0 16px; padding: 0; list-style: none; }
    .fw-tabs a { display: inline-flex; align-items: center; gap: 7px; padding: 9px 14px; border-radius: 6px; background: #fff; border: 1px solid #dfe4ec; color: #4a5672; font-weight: 600; }
    .fw-tabs a:hover { border-color: #017e7a; color: #015f5c; text-decoration: none; }
    .fw-tabs a.on { background: #017e7a; border-color: #017e7a; color: #fff; }
    .fw-kpis { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 12px; margin-bottom: 16px; }
    .fw-kpi { background: #fff; border: 1px solid #e3e8f1; border-radius: 8px; padding: 14px 16px; border-top: 3px solid #0b2d64; }
    .fw-kpi.teal { border-top-color: #017e7a; } .fw-kpi.gold { border-top-color: #fbb307; } .fw-kpi.red { border-top-color: #c0392b; }
    .fw-kpi span { display: block; color: #7a849a; font-size: 12px; text-transform: uppercase; letter-spacing: .05em; font-weight: 600; }
    .fw-kpi b { display: block; font-size: 22px; line-height: 1.25; margin-top: 4px; color: #0a1a36; font-variant-numeric: tabular-nums; }
    .fw-kpi small { color: #7a849a; }
    .fw-bar { height: 8px; background: #eef1f6; border-radius: 99px; overflow: hidden; min-width: 90px; }
    .fw-bar i { display: block; height: 100%; background: #017e7a; border-radius: 99px; }
    .fw-table td, .fw-table th { vertical-align: middle !important; }
    .fw-table .n { text-align: right; white-space: nowrap; font-variant-numeric: tabular-nums; }
    .fw-filters { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; margin-bottom: 12px; }
    .fw-filters select { width: auto; min-width: 160px; }
    .fw-tag { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: 11px; font-weight: 600; }
    .fw-tag.paid { background: #e2f3f1; color: #015f5c; } .fw-tag.owing { background: #fff4d9; color: #8a5d00; } .fw-tag.overdue { background: #fdecea; color: #a5281b; }
    .fw-muted { color: #7a849a; }
    .fw-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 12px; }
    .fw-card { background: #fff; border: 1px solid #e3e8f1; border-radius: 8px; padding: 16px; }
    .fw-card h4 { margin: 0 0 6px; font-weight: 700; }
    .fw-card p { color: #4a5672; margin: 0 0 10px; }
    .fw-steps { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 14px; padding: 0; list-style: none; }
    .fw-steps li { padding: 6px 12px; border-radius: 99px; background: #eef1f6; color: #4a5672; font-weight: 600; font-size: 12px; }
    .fw-steps li.on { background: #0b2d64; color: #fff; }
    .fw-sum { position: sticky; bottom: 0; z-index: 5; background: #fff; border: 1px solid #e3e8f1; border-radius: 8px; padding: 12px 16px; display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between; box-shadow: 0 -4px 14px rgba(10,26,54,.06); }
    .fw-sum b { font-variant-numeric: tabular-nums; }
</style>
<ul class="fw-tabs">
    <?php foreach ($tabs as $key => $t) {
        if (($key === 'create' && !$this->rbac->hasPrivilege('fees_master', 'can_add')) || ($key === 'adjust' && !$this->rbac->hasPrivilege('fees_discount', 'can_view'))) {
            continue;
        } ?>
        <li><a class="<?php echo $tab === $key ? 'on' : ''; ?>" href="<?php echo site_url($t[0]); ?>"><i class="fa <?php echo $t[1]; ?>"></i> <?php echo $t[2]; ?></a></li>
    <?php } ?>
</ul>
<?php if ($this->session->flashdata('fees_msg')) { ?>
    <div class="alert alert-success"><?php echo html_escape($this->session->flashdata('fees_msg')); ?></div>
<?php } ?>
