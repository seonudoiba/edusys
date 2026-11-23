<?php
/* Income List / Expense List (Money_list). $kind: income | expense */
$is_income = $kind === 'income';
$base = $is_income ? 'admin/income' : 'admin/expense';
$word = $is_income ? 'Income' : 'Expense';
$cur = $this->customlib->getSchoolCurrencyFormat();
$m = function ($v) use ($cur) {
    return $cur . number_format((float) $v, 2);
};
$e = function ($v) {
    return html_escape((string) $v);
};
$f += array('source' => '');
$qs = http_build_query(array_filter(array('period' => $f['period'], 'from' => $f['from'], 'to' => $f['to'], 'head_id' => $f['head_id'] ?: '', 'q' => $f['q'], 'source' => $f['source'])));
$src_tag = array('manual' => array('#eef2f7', '#334155'), 'fees' => array('#e2f3f1', '#015f5c'), 'admission' => array('#ede9fe', '#5b21b6'), 'course' => array('#fef3c7', '#92400e'));
?>
<style>
    .ml-kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;margin-bottom:14px}
    .ml-kpi{background:#fff;border:1px solid #e3e8f1;border-radius:10px;padding:12px 16px;border-top:3px solid <?php echo $is_income ? '#017e7a' : '#c0392b'; ?>}
    .ml-kpi span{display:block;color:#7a849a;font-size:12px;text-transform:uppercase;letter-spacing:.05em;font-weight:600}
    .ml-kpi b{display:block;font-size:20px;margin-top:3px;color:#0a1a36;font-variant-numeric:tabular-nums}
    .ml-filters{display:flex;flex-wrap:wrap;gap:8px;align-items:flex-end}
    .ml-filters .form-group{margin:0}
    .ml-filters label{font-size:12px;color:#5b6680;margin-bottom:3px}
    .ml-table td,.ml-table th{vertical-align:middle!important}
    .ml-table .n{text-align:right;white-space:nowrap;font-variant-numeric:tabular-nums}
    .ml-heads{display:flex;flex-wrap:wrap;gap:6px;margin:0 0 12px;padding:0;list-style:none}
    .ml-heads li{background:#f3f5f9;border-radius:99px;padding:4px 11px;font-size:12px;color:#334}
    .ml-heads b{font-variant-numeric:tabular-nums}
</style>
<div class="content-wrapper">
    <section class="content">
        <?php if ($this->session->flashdata('ml_msg')) { ?><div class="alert alert-success"><?php echo $e($this->session->flashdata('ml_msg')); ?></div><?php } ?>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa <?php echo $is_income ? 'fa-arrow-circle-down' : 'fa-arrow-circle-up'; ?>"></i> <?php echo $word; ?> List</h3>
                <div class="box-tools pull-right">
                    <?php if ($this->rbac->hasPrivilege($kind, 'can_add')) { ?><a class="btn btn-sm btn-primary" href="<?php echo site_url($base); ?>"><i class="fa fa-plus"></i> Record <?php echo strtolower($word); ?></a><?php } ?>
                    <a class="btn btn-sm btn-default" href="<?php echo site_url($is_income ? 'admin/incomehead' : 'admin/expensehead'); ?>"><i class="fa fa-tags"></i> <?php echo $word; ?> heads</a>
                </div>
            </div>
            <div class="box-body">
                <form method="get" action="<?php echo site_url($base . ($is_income ? '/incomesearch' : '/expensesearch')); ?>" class="ml-filters" style="margin-bottom:14px">
                    <div class="form-group">
                        <label for="ml_period">Period</label>
                        <select id="ml_period" name="period" class="form-control" onchange="document.getElementById('ml_dates').style.display=this.value==='custom'?'flex':'none'">
                            <?php foreach (Money_list::PERIODS as $k => $label) { ?><option value="<?php echo $k; ?>" <?php echo $f['period'] === $k ? 'selected' : ''; ?>><?php echo $label; ?></option><?php } ?>
                        </select>
                    </div>
                    <div id="ml_dates" style="display:<?php echo $f['period'] === 'custom' ? 'flex' : 'none'; ?>;gap:8px">
                        <div class="form-group"><label for="ml_from">From</label><input id="ml_from" type="date" name="from" class="form-control" value="<?php echo $e($f['from']); ?>"></div>
                        <div class="form-group"><label for="ml_to">To</label><input id="ml_to" type="date" name="to" class="form-control" value="<?php echo $e($f['to']); ?>"></div>
                    </div>
                    <div class="form-group">
                        <label for="ml_head"><?php echo $word; ?> head</label>
                        <select id="ml_head" name="head_id" class="form-control">
                            <option value="">All heads</option>
                            <?php foreach ($heads as $id => $name) { ?><option value="<?php echo (int) $id; ?>" <?php echo (int) $f['head_id'] === (int) $id ? 'selected' : ''; ?>><?php echo $e($name); ?></option><?php } ?>
                        </select>
                    </div>
                    <?php if ($is_income) { ?>
                    <div class="form-group">
                        <label for="ml_source">Source</label>
                        <select id="ml_source" name="source" class="form-control">
                            <option value="">All income</option>
                            <?php foreach (Money_list::SOURCES as $k => $label) { ?><option value="<?php echo $k; ?>" <?php echo $f['source'] === $k ? 'selected' : ''; ?>><?php echo $label; ?></option><?php } ?>
                        </select>
                    </div>
                    <?php } ?>
                    <div class="form-group" style="flex:1;min-width:180px">
                        <label for="ml_q">Search</label>
                        <input id="ml_q" type="search" name="q" class="form-control" value="<?php echo $e($f['q']); ?>" placeholder="Name, invoice number or description">
                    </div>
                    <button class="btn btn-primary"><i class="fa fa-search"></i> Show</button>
                    <?php if ($qs !== '') { ?><a class="btn btn-default" href="<?php echo site_url($base . ($is_income ? '/incomesearch' : '/expensesearch')); ?>">Clear</a><?php } ?>
                </form>

                <div class="ml-kpis">
                    <div class="ml-kpi"><span>Total <?php echo strtolower($word); ?></span><b><?php echo $m($total); ?></b></div>
                    <div class="ml-kpi"><span>Records</span><b><?php echo count($rows); ?></b></div>
                    <div class="ml-kpi"><span>Period</span><b style="font-size:15px"><?php
                        list($from, $to) = Money_list::range($f['period'], $f['from'], $f['to']);
                        echo $from || $to ? $e(($from ? date('j M Y', strtotime($from)) : '…') . ' – ' . ($to ? date('j M Y', strtotime($to)) : '…')) : 'All time'; ?></b></div>
                </div>
                <?php if (count($by_head) > 1) { ?>
                    <ul class="ml-heads">
                        <?php foreach ($by_head as $h) { ?><li><?php echo $e($h[0]); ?>: <b><?php echo $m($h[2]); ?></b> (<?php echo (int) $h[1]; ?>)</li><?php } ?>
                    </ul>
                <?php } ?>

                <?php if ($is_income) { ?><p class="text-muted small" style="margin:-4px 0 12px"><i class="fa fa-info-circle"></i> School fees, admission form fees and online course payments are added here automatically when they are paid. Change them on the payment itself (for example the student's fees page); only income recorded here can be edited.</p><?php } ?>
                <?php if (!$rows) { ?>
                    <p class="text-muted" style="padding:20px 0;text-align:center">No <?php echo strtolower($word); ?> recorded<?php echo $qs !== '' ? ' for this search' : ' yet'; ?>.</p>
                <?php } else { ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover ml-table example">
                            <thead><tr><th>Date</th><th>Name</th><th><?php echo $word; ?> head</th><th>Invoice no.</th><th>Description</th><th class="n">Amount</th><th class="text-right noExport">Action</th></tr></thead>
                            <tbody>
                            <?php foreach ($rows as $r) { ?>
                                <tr>
                                    <td data-order="<?php echo $e($r['date']); ?>"><?php echo $e(date('j M Y', strtotime($r['date']))); ?></td>
                                    <td><?php echo $e($r['name']); ?></td>
                                    <td><?php $st = $src_tag[isset($r['source']) ? $r['source'] : 'manual']; ?><?php echo $e($r['head']); ?><?php if ($is_income && $r['source'] !== 'manual') { ?><br><span style="display:inline-block;margin-top:2px;padding:0 7px;border-radius:99px;font-size:11px;font-weight:700;background:<?php echo $st[0]; ?>;color:<?php echo $st[1]; ?>">Automatic</span><?php } ?></td>
                                    <td><?php echo $e($r['invoice_no']); ?></td>
                                    <td><small><?php echo $e(mb_strimwidth((string) $r['note'], 0, 80, '…')); ?></small></td>
                                    <td class="n"><?php echo $m($r['amount']); ?></td>
                                    <td class="text-right" style="white-space:nowrap">
                                        <?php if (isset($r['source']) && $r['source'] !== 'manual') { ?>
                                            <?php if ($r['link'] !== '') { ?><a class="btn btn-xs btn-default" href="<?php echo site_url($r['link']); ?>" title="Open the payment"><i class="fa fa-external-link"></i> View</a><?php } ?>
                                        <?php } else { ?>
                                        <?php if ((string) $r['documents'] !== '') { ?><a class="btn btn-xs btn-default" href="<?php echo site_url($base . '/download/' . (int) $r['id']); ?>" title="Download the attached document"><i class="fa fa-paperclip"></i></a><?php } ?>
                                        <?php if ($this->rbac->hasPrivilege($kind, 'can_edit')) { ?><a class="btn btn-xs btn-default" href="<?php echo site_url($base . '/edit/' . (int) $r['id']); ?>" title="Edit"><i class="fa fa-pencil"></i> Edit</a><?php } ?>
                                        <?php if ($this->rbac->hasPrivilege($kind, 'can_delete')) { ?><a class="btn btn-xs btn-default text-danger" href="<?php echo site_url($base . '/delete/' . (int) $r['id']) . '?back=list' . ($qs !== '' ? '&' . $e($qs) : ''); ?>" onclick="return confirm('Delete this <?php echo strtolower($word); ?> record (<?php echo $e(addslashes($r['name'])); ?>, <?php echo $e($m($r['amount'])); ?>)? This cannot be undone.')" title="Delete"><i class="fa fa-trash"></i></a><?php } ?>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                            <tfoot><tr><th colspan="5" class="text-right">Total</th><th class="n"><?php echo $m($total); ?></th><th></th></tr></tfoot>
                        </table>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
</div>
