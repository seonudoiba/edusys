<?php
$e = function ($v) { return html_escape((string) $v); };
$money = function ($v) { return 'NGN ' . number_format((float) $v, 2); };
$methods = array('paystack' => 'Paystack', 'bank_transfer' => 'Bank transfer', 'manual' => 'Manual', '' => 'Free');
?>
<div class="content-wrapper">
    <section class="content-header"><h1><i class="fa fa-file-text-o"></i> Invoices</h1></section>
    <section class="content">
        <?php if ($message) { ?><div class="alert alert-success"><?php echo $e($message); ?></div><?php } ?>
        <?php if ($error) { ?><div class="alert alert-danger"><?php echo $e($error); ?></div><?php } ?>
        <div class="box box-primary">
            <div class="box-body">
                <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:10px">
                    <form method="get" action="<?php echo site_url('admin/billing/invoices'); ?>" style="display:flex;gap:6px">
                        <input type="search" name="q" class="form-control" placeholder="Invoice number, school, email" value="<?php echo $e($q); ?>" style="min-width:260px">
                        <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
                    </form>
                    <a class="btn btn-default" href="<?php echo site_url('admin/billing/invoices?export=csv' . ($q !== '' ? '&q=' . rawurlencode($q) : '')); ?>"><i class="fa fa-download"></i> Export CSV</a>
                    <form method="post" action="<?php echo site_url('admin/billing/issue_missing'); ?>" style="margin-left:auto" onsubmit="return confirm('Issue invoices for paid subscriptions that have none (no emails are sent)?');">
                        <?php echo $token; ?><button class="btn btn-default" type="submit">Issue missing invoices</button>
                    </form>
                </div>
                <p class="text-muted"><?php echo count($rows); ?> invoices · <?php echo $money($total); ?></p>
                <div class="table-responsive">
                    <table class="table table-hover table-condensed">
                        <thead><tr><th>Invoice</th><th>Issued</th><th>Billed to</th><th>For</th><th>Method</th><th class="text-right">Total</th><th class="text-right">Actions</th></tr></thead>
                        <tbody>
                            <?php if (!$rows) { ?><tr><td colspan="7" class="text-center text-muted" style="padding:24px">No invoices yet. One is issued for every paid subscription.</td></tr><?php } ?>
                            <?php foreach ($rows as $r) { ?>
                                <tr>
                                    <td><b><?php echo $e($r['invoice_no']); ?></b></td>
                                    <td><?php echo $e(date('d M Y', strtotime($r['issued_at']))); ?></td>
                                    <td><?php echo $r['school_ref'] ? '<a href="' . site_url('admin/schools/view/' . (int) $r['school_ref']) . '">' . $e($r['bill_to_name']) . '</a>' : $e($r['bill_to_name']); ?>
                                        <div class="text-muted" style="font-size:12px"><?php echo $e($r['bill_to_email']); ?></div></td>
                                    <td><?php echo $e($r['description']); ?></td>
                                    <td><?php echo $e(isset($methods[$r['method']]) ? $methods[$r['method']] : $r['method']); ?></td>
                                    <td class="text-right"><?php echo $money($r['total']); ?>
                                        <?php if ((float) $r['discount'] > 0) { ?><div class="text-muted" style="font-size:12px">discount <?php echo $money($r['discount']); ?></div><?php } ?></td>
                                    <td class="text-right" style="white-space:nowrap">
                                        <a class="btn btn-xs btn-default" href="<?php echo site_url('admin/billing/invoice/' . (int) $r['id']); ?>" target="_blank">PDF</a>
                                        <form method="post" action="<?php echo site_url('admin/billing/resend_invoice/' . (int) $r['id']); ?>" style="display:inline">
                                            <?php echo $token; ?><button class="btn btn-xs btn-default" type="submit" title="Email it to <?php echo $e($r['bill_to_email']); ?> again">Resend</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
