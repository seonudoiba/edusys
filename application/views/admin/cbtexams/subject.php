<?php
/* CBT Exams > a class > a subject (Cbtexams::subject): its CBTs by term, practice CBTs, and a new CBT. */
$cr_msg = $this->session->flashdata('cr_msg');
$this->load->view('admin/cbtexams/_style');
$return = 'admin/cbtexams/subject/' . (int) $class['id'] . '/' . (int) $subject['id'];
$groups = array();
foreach ($terms as $t) { $groups[(int) $t['id']] = array('name' => $t['name'], 'rows' => array()); }
$groups[0] = array('name' => 'Practice (not on a report card)', 'rows' => array());
foreach ($cbts as $o) { $k = $o['link'] && isset($groups[(int) $o['link']['cbse_term_id']]) ? (int) $o['link']['cbse_term_id'] : 0; $groups[$k]['rows'][] = $o; }
$can_add = $this->rbac->hasPrivilege('online_examination', 'can_add');
?>
<div class="content-wrapper">
    <section class="content">
        <a href="<?php echo site_url('admin/cbtexams/klass/' . (int) $class['id']); ?>" class="cbt-muted"><i class="fa fa-angle-left"></i> <?php echo html_escape($class['class']); ?> subjects</a>
        <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;justify-content:space-between;margin-bottom:6px">
            <h3 style="margin:2px 0;font-weight:800"><?php echo html_escape($subject['name']); ?> <small class="cbt-muted"><?php echo html_escape($class['class']); ?> · CBT exams</small></h3>
            <div style="display:flex;gap:8px">
                <a class="btn btn-default btn-sm" href="<?php echo site_url('cbseexam/results/subject/' . (int) $class['id'] . '/' . (int) $subject['id']); ?>"><i class="fa fa-th-list"></i> Scores in Results</a>
                <?php if ($can_add) { $this->load->view('admin/cbtexams/_new', array('class' => $class, 'subject' => $subject, 'targets' => $targets, 'return' => $return, 'preset' => '')); } ?>
            </div>
        </div>
        <?php if ($cr_msg) { ?><div class="alert alert-<?php echo $cr_msg[0]; ?>"><?php echo html_escape($cr_msg[1]); ?></div><?php } ?>
        <?php if (!$cbts) { ?><div class="alert alert-info" style="margin-top:14px">No CBT for <?php echo html_escape($subject['name']); ?> in <?php echo html_escape($class['class']); ?> yet. Use New CBT.</div><?php } ?>
        <?php foreach ($groups as $g) { if (!$g['rows']) { continue; } ?>
            <div class="cbt-group"><i class="fa fa-calendar-o"></i> <?php echo html_escape($g['name']); ?> <span><?php echo count($g['rows']); ?> CBT<?php echo count($g['rows']) === 1 ? '' : 's'; ?></span></div>
            <div class="cbt-cards">
                <?php foreach ($g['rows'] as $o) { $this->load->view('admin/cbtexams/_card', array('o' => $o, 'return' => $return, 'class_id' => (int) $class['id'], 'subject_id' => (int) $subject['id'], 'targets' => $targets)); } ?>
            </div>
        <?php } ?>
    </section>
</div>
