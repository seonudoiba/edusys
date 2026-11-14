<?php
$e = function ($v) { return html_escape((string) $v); };
$given = 0;
foreach ($discounts as $d) {
    $given += $d['students'];
}
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-sliders"></i> Discounts and balances <small><?php echo $e($session_name); ?> session</small></h1>
    </section>
    <section class="content">
        <?php $this->load->view('admin/fees/_nav', array('tab' => $tab)); ?>
        <p class="fw-muted" style="margin:-4px 0 14px">Changes to what students owe. Each one shows you who is affected and by how much before anything is saved.</p>
        <div class="fw-cards">
            <div class="fw-card">
                <h4><i class="fa fa-user-plus"></i> Students not charged yet</h4>
                <p><?php echo $uncharged ? '<b>' . (int) $uncharged . ($uncharged === 1 ? ' student is' : ' students are') . '</b> in a class that has fees but not charged them, usually because they were admitted after the fees were set.' : 'Every student is charged the fees of their class.'; ?></p>
                <?php if ($this->rbac->hasPrivilege('fees_master', 'can_add')) { ?>
                    <a class="btn btn-<?php echo $uncharged ? 'primary' : 'default'; ?> btn-sm" href="<?php echo site_url('admin/fees/catchup'); ?>"><?php echo $uncharged ? 'Review and charge' : 'Check'; ?></a>
                <?php } ?>
            </div>
            <div class="fw-card">
                <h4><i class="fa fa-tag"></i> Discounts</h4>
                <p><?php echo count($discounts) ? count($discounts) . ' discount' . (count($discounts) === 1 ? '' : 's') . ', given to ' . (int) $given . ' student' . ($given === 1 ? '' : 's') . ' this session.' : 'No discounts yet.'; ?> Give a sibling, staff-child or scholarship discount to chosen students; it comes off at their next payment.</p>
                <?php if ($this->rbac->hasPrivilege('fees_discount_assign', 'can_view')) { ?>
                    <a class="btn btn-default btn-sm" href="<?php echo site_url('admin/fees/discounts'); ?>">Give a discount</a>
                <?php } ?>
            </div>
            <div class="fw-card">
                <h4><i class="fa fa-share"></i> Balances from last session</h4>
                <?php if (!$previous) { ?>
                    <p>There is no session before this one, so there is nothing to bring forward.</p>
                <?php } else { ?>
                    <p><b><?php echo (int) $carry_owing . ($carry_owing === 1 ? ' student' : ' students'); ?></b> still owe from <?php echo $e($previous->session); ?><?php echo $carry_done ? '; ' . (int) $carry_done . ' already brought forward' : ''; ?>. Bring unpaid balances into this session so they are collected with this session's fees.</p>
                    <?php if ($this->rbac->hasPrivilege('fees_carry_forward', 'can_view')) { ?>
                        <a class="btn btn-default btn-sm" href="<?php echo site_url('admin/fees/carryforward'); ?>">Carry forward</a>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </section>
</div>
