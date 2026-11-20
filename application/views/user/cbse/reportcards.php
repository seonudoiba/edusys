<?php /* Student / parent portal: published report cards (user/cbse/exam/reportcards). */ ?>
<style>
    .rc-list{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:14px}
    .rc-item{background:#fff;border:1px solid #e5e9f0;border-radius:16px;padding:18px}
    .rc-item h4{margin:0 0 4px;font-weight:800}
    .rc-item small{color:#64748b}
    .rc-item .rc-b{display:flex;gap:8px;margin-top:14px}
</style>
<div class="content-wrapper">
    <section class="content">
        <h3 style="margin:0 0 16px;font-weight:800"><i class="fa fa-file-text-o"></i> My Results &amp; Report Cards</h3>
        <?php if (!$cards) { ?>
            <div class="alert alert-info">No report card has been published for you yet. It appears here as soon as the school publishes it.</div>
        <?php } else { ?>
            <div class="rc-list">
                <?php foreach ($cards as $c) { ?>
                    <div class="rc-item">
                        <h4><?php echo html_escape($c['term_name'] ?: $c['name']); ?></h4>
                        <small><?php echo html_escape($c['session'] . ' session · ' . $c['class'] . ' ' . $c['section']); ?></small>
                        <div class="rc-b">
                            <a class="btn btn-primary btn-sm" target="_blank" href="<?php echo site_url('user/cbse/exam/reportcard/' . (int) $c['es']); ?>"><i class="fa fa-eye"></i> View</a>
                            <a class="btn btn-default btn-sm" href="<?php echo site_url('user/cbse/exam/reportcard/' . (int) $c['es'] . '?pdf=1'); ?>"><i class="fa fa-download"></i> Download PDF</a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </section>
</div>
