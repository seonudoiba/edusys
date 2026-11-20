<?php /* CBT Exams (Cbtexams::index): every class as a card with its CBTs this session. */ $cr_msg = $this->session->flashdata('cr_msg'); $this->load->view('admin/cbtexams/_style'); ?>
<div class="content-wrapper">
    <section class="content">
        <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end;justify-content:space-between;margin-bottom:16px">
            <div><h3 style="margin:0;font-weight:800"><i class="fa fa-laptop"></i> CBT Exams</h3><span class="cbt-muted"><?php echo html_escape($session_name); ?> session · open a class, then a subject, to see and make its CBTs</span></div>
            <a class="btn btn-default btn-sm" href="<?php echo site_url('admin/onlineexam'); ?>"><i class="fa fa-list"></i> All CBTs as a list</a>
        </div>
        <?php if ($cr_msg) { ?><div class="alert alert-<?php echo $cr_msg[0]; ?>"><?php echo html_escape($cr_msg[1]); ?></div><?php } ?>
        <div class="cbt-grid">
            <?php foreach ($classes as $c) { $n = isset($per[$c['id']]) ? $per[$c['id']] : 0; ?>
                <a class="cbt-tile" href="<?php echo site_url('admin/cbtexams/klass/' . (int) $c['id']); ?>">
                    <h4><?php echo html_escape($c['name']); ?></h4>
                    <small><i class="fa fa-users"></i> <?php echo (int) $c['students']; ?> students · <?php echo html_escape(implode(', ', $c['sections'])); ?></small><br>
                    <span class="num"><?php echo $n; ?> CBT<?php echo $n === 1 ? '' : 's'; ?></span>
                </a>
            <?php } ?>
            <?php if ($none) { ?>
                <a class="cbt-tile" href="<?php echo site_url('admin/onlineexam'); ?>"><h4>Not given to a class</h4><small>CBTs with no students yet</small><br><span class="num"><?php echo (int) $none; ?> CBT<?php echo $none === 1 ? '' : 's'; ?></span></a>
            <?php } ?>
        </div>
    </section>
</div>
