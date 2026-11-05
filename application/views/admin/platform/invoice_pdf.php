<?php
$e = function ($v) { return html_escape((string) $v); };
$i = $invoice;
$methods = array('paystack' => 'Paystack (online)', 'bank_transfer' => 'Bank transfer', 'manual' => 'Recorded by the platform', '' => '-');
?>
<style>
    body { font-family: sans-serif; font-size: 11pt; color: #222; }
    .top td { vertical-align: top; }
    h1 { font-size: 22pt; margin: 0; color: #333; }
    .muted { color: #777; font-size: 9pt; }
    table.lines { width: 100%; border-collapse: collapse; margin-top: 24px; }
    table.lines th { background: #f2f2f2; text-align: left; padding: 8px; font-size: 10pt; }
    table.lines td { padding: 8px; border-bottom: 1px solid #e5e5e5; }
    .right { text-align: right; }
    .total td { font-weight: bold; font-size: 12pt; border-bottom: none; }
    .paid { color: #00a65a; font-weight: bold; font-size: 14pt; }
</style>
<table class="top" width="100%">
    <tr>
        <td width="55%">
            <h1><?php echo $e($seller['name']); ?></h1>
            <div class="muted"><?php echo nl2br($e($seller['address'])); ?></div>
            <div class="muted"><?php echo $e($seller['email']); ?> <?php echo $seller['phone'] ? '· ' . $e($seller['phone']) : ''; ?></div>
            <?php if ($seller['tax_id']) { ?><div class="muted">Tax ID: <?php echo $e($seller['tax_id']); ?></div><?php } ?>
        </td>
        <td width="45%" class="right">
            <div style="font-size:16pt;font-weight:bold">INVOICE / RECEIPT</div>
            <div><?php echo $e($i['invoice_no']); ?></div>
            <div class="muted">Issued <?php echo $e(date('d M Y', strtotime($i['issued_at']))); ?></div>
            <div class="paid">PAID</div>
        </td>
    </tr>
</table>
<div style="margin-top:24px">
    <div class="muted">Billed to</div>
    <div><b><?php echo $e($i['bill_to_name']); ?></b></div>
    <div><?php echo $e($i['bill_to_email']); ?></div>
</div>
<table class="lines">
    <tr><th>Description</th><th class="right">Amount</th></tr>
    <tr><td><?php echo $e($i['description']); ?></td><td class="right"><?php echo $e($money($i['price'])); ?></td></tr>
    <?php if ((float) $i['discount'] > 0) { ?>
        <tr><td>Discount</td><td class="right">- <?php echo $e($money($i['discount'])); ?></td></tr>
    <?php } ?>
    <tr class="total"><td class="right">Total paid</td><td class="right"><?php echo $e($money($i['total'])); ?></td></tr>
</table>
<div style="margin-top:18px" class="muted">
    Payment: <?php echo $e(isset($methods[$i['method']]) ? $methods[$i['method']] : ucwords(str_replace('_', ' ', $i['method']))); ?>
    <?php if ($i['reference']) { ?> · Reference: <?php echo $e($i['reference']); ?><?php } ?>
</div>
<div style="margin-top:40px" class="muted"><?php echo nl2br($e($seller['footer'])); ?></div>
