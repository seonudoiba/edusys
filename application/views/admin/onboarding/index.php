<?php
list($finished, $total) = $progress;
$labels = array('done' => array('success', 'Done'), 'skipped' => array('default', 'Skipped'), 'todo' => array('warning', 'To do'));
?>
<style>
    .ob-progress { height: 10px; margin: 6px 0 0; }
    .ob-step { display: flex; gap: 16px; padding: 16px 0; border-top: 1px solid #eee; }
    .ob-step:first-child { border-top: 0; }
    .ob-num { flex: 0 0 34px; height: 34px; border-radius: 50%; background: #eef2f7; color: #3c4b64; font-weight: 600; display: flex; align-items: center; justify-content: center; }
    .ob-step.is-done .ob-num { background: #00a65a; color: #fff; }
    .ob-step.is-skipped .ob-num { background: #d2d6de; color: #fff; }
    .ob-body { flex: 1; min-width: 0; }
    .ob-body h4 { margin: 4px 0 4px; font-size: 16px; }
    .ob-body p { margin: 0 0 10px; color: #666; }
    .ob-actions form { display: inline; }
    .ob-actions .btn-link { padding-left: 6px; padding-right: 6px; }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-flag-checkered"></i> Set up your school</h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-9">
                <?php if ($this->session->flashdata('msg')) { echo $this->session->flashdata('msg'); } ?>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $finished; ?> of <?php echo $total; ?> steps finished</h3>
                        <div class="progress ob-progress"><div class="progress-bar progress-bar-success" style="width: <?php echo $total ? round(100 * $finished / $total) : 0; ?>%"></div></div>
                    </div>
                    <div class="box-body">
                        <?php $i = 0; foreach ($steps as $step) { $i++; list($badge, $label) = $labels[$step['status']];
                            $allowed = $this->rbac->hasPrivilege($step['privilege'][0], $step['privilege'][1]); ?>
                            <div class="ob-step is-<?php echo $step['status']; ?>">
                                <div class="ob-num"><?php echo $step['status'] === 'done' ? '<i class="fa fa-check"></i>' : $i; ?></div>
                                <div class="ob-body">
                                    <h4><?php echo html_escape($step['title']); ?> <span class="label label-<?php echo $badge; ?>"><?php echo $label; ?></span></h4>
                                    <p><?php echo html_escape($step['text']); ?></p>
                                    <div class="ob-actions">
                                        <?php if ($allowed) { ?>
                                            <a class="btn btn-sm <?php echo $step['status'] === 'todo' ? 'btn-primary' : 'btn-default'; ?>" href="<?php echo html_escape($step['url']); ?>"><?php echo html_escape($step['button']); ?></a>
                                        <?php } else { ?>
                                            <span class="text-muted small">You don't have permission for this step. Ask your school's super admin.</span>
                                        <?php } ?>
                                        <?php if ($can_edit && !$step['auto']) {
                                            $buttons = $step['status'] === 'todo' ? array('done' => 'Mark as done', 'skipped' => "Skip, we don't need this") : array('todo' => 'Undo');
                                            foreach ($buttons as $how => $text) { ?>
                                                <form method="post" action="<?php echo site_url('admin/onboarding/mark'); ?>">
                                                    <?php echo $this->customlib->getCSRF(); ?>
                                                    <input type="hidden" name="step" value="<?php echo html_escape($step['key']); ?>">
                                                    <input type="hidden" name="how" value="<?php echo $how; ?>">
                                                    <button type="submit" class="btn btn-link btn-sm"><?php echo html_escape($text); ?></button>
                                                </form>
                                            <?php }
                                        } elseif ($step['auto']) { ?>
                                            <span class="text-muted small">Done in your settings.</span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="box box-solid">
                    <div class="box-body">
                        <p>Work through these in order: classes come before fees, because fees are set per class.</p>
                        <p class="text-muted small">Payment methods and online admission tick themselves once they are switched on in your settings.</p>
                        <?php if ($can_edit) { ?>
                            <form method="post" action="<?php echo site_url('admin/onboarding/hide'); ?>">
                                <?php echo $this->customlib->getCSRF(); ?>
                                <input type="hidden" name="hidden" value="<?php echo $state['hidden'] ? '0' : '1'; ?>">
                                <button type="submit" class="btn btn-default btn-sm btn-block"><?php echo $state['hidden'] ? 'Show this checklist on the dashboard' : 'Hide this checklist from the dashboard'; ?></button>
                            </form>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
