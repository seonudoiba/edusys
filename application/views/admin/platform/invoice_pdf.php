<?php
/*
 * The invoice document: the PDF (mPDF: tables and simple CSS only) and the web page's content.
 * $invoice, $lines [description, detail, qty, unit, amount], $status (draft, unpaid, paid, void),
 * $overdue, $seller, $bank_instructions, $methods, $money, $for_pdf.
 */
$e = function ($v) { return html_escape((string) $v); };
$i = $invoice;
$date = function ($v) { return $v ? date('d M Y', strtotime($v)) : '-'; };
$titles = array('draft' => 'PRO-FORMA INVOICE', 'unpaid' => 'INVOICE', 'paid' => 'INVOICE / RECEIPT', 'void' => 'INVOICE');
$stamp = array(
    'draft' => array('DRAFT', '#6b7280'),
    'unpaid' => $overdue ? array('OVERDUE', '#c0392b') : array('AMOUNT DUE', '#d97706'),
    'paid' => array('PAID', '#15803d'),
    'void' => array('CANCELLED', '#6b7280'),
);
list($stamp_text, $stamp_color) = $stamp[$status];
$accent = '#0b5394';
?>
<style>
    .inv { font-family: sans-serif; font-size: 10pt; color: #1f2937; line-height: 1.45; }
    .inv table { border-collapse: collapse; }
    .muted { color: #6b7280; font-size: 9pt; }
    .label { color: #6b7280; font-size: 8pt; text-transform: uppercase; letter-spacing: 1px; }
    .title { font-size: 20pt; font-weight: bold; color: <?php echo $accent; ?>; letter-spacing: 1px; }
    .bar { height: 5px; background: <?php echo $accent; ?>; }
    .lines th { background: <?php echo $accent; ?>; color: #fff; font-size: 8.5pt; text-transform: uppercase; letter-spacing: .5px; padding: 8px 10px; text-align: left; }
    .lines td { padding: 10px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
    .right, .lines th.right { text-align: right; }
    .totals td { padding: 6px 10px; }
    .grand td { font-size: 12pt; font-weight: bold; border-top: 2px solid <?php echo $accent; ?>; padding-top: 10px; }
    .stamp { font-weight: bold; font-size: 11pt; color: <?php echo $stamp_color; ?>; border: 2px solid <?php echo $stamp_color; ?>; padding: 4px 10px; letter-spacing: 1px; }
    .box { background: #f8fafc; border: 1px solid #e5e7eb; padding: 12px 14px; }
    .prose p { margin: 0 0 6px; }
</style>
<div class="inv">
    <div class="bar"></div>
    <table width="100%" style="margin-top:18px">
        <tr>
            <td width="55%" style="vertical-align:top">
                <?php if ($seller['logo']) { ?>
                    <img src="<?php echo $e($seller['logo']); ?>" alt="" style="height:52px;width:auto;margin-bottom:8px"><br>
                <?php } ?>
                <b style="font-size:12pt"><?php echo $e($seller['name']); ?></b><br>
                <?php if ($seller['address']) { ?><span class="muted"><?php echo nl2br($e($seller['address'])); ?></span><br><?php } ?>
                <span class="muted"><?php echo $e($seller['email']); ?><?php echo $seller['phone'] ? ' &middot; ' . $e($seller['phone']) : ''; ?></span>
                <?php if ($seller['tax_id']) { ?><br><span class="muted">Tax ID: <?php echo $e($seller['tax_id']); ?></span><?php } ?>
            </td>
            <td width="45%" class="right" style="vertical-align:top">
                <div class="title"><?php echo $titles[$status]; ?></div>
                <table style="margin-top:8px" align="right">
                    <tr><td class="label right" style="padding:2px 10px 2px 0">Invoice no.</td><td class="right"><b><?php echo $e($i['invoice_no']); ?></b></td></tr>
                    <tr><td class="label right" style="padding:2px 10px 2px 0">Issue date</td><td class="right"><?php echo $e($date($i['issued_at'])); ?></td></tr>
                    <?php if ($status === 'paid') { ?>
                        <tr><td class="label right" style="padding:2px 10px 2px 0">Paid on</td><td class="right"><?php echo $e($date(!empty($i['paid_at']) ? $i['paid_at'] : $i['issued_at'])); ?></td></tr>
                    <?php } elseif (!empty($i['due_date'])) { ?>
                        <tr><td class="label right" style="padding:2px 10px 2px 0">Due date</td><td class="right"><b><?php echo $e($date($i['due_date'])); ?></b></td></tr>
                    <?php } ?>
                </table>
                <div style="clear:both;margin-top:12px"><span class="stamp"><?php echo $stamp_text; ?></span></div>
            </td>
        </tr>
    </table>

    <table width="100%" style="margin-top:22px">
        <tr>
            <td width="55%" style="vertical-align:top">
                <div class="label">Bill to</div>
                <b style="font-size:11pt"><?php echo $e($i['bill_to_name']); ?></b><br>
                <?php if (!empty($i['bill_to_address'])) { ?><?php echo nl2br($e($i['bill_to_address'])); ?><br><?php } ?>
                <?php echo $e($i['bill_to_email']); ?>
                <?php if (!empty($i['bill_to_phone'])) { ?><br><?php echo $e($i['bill_to_phone']); ?><?php } ?>
            </td>
            <td width="45%" class="right" style="vertical-align:top">
                <div class="label"><?php echo $status === 'paid' ? 'Amount paid' : 'Amount due'; ?></div>
                <div style="font-size:18pt;font-weight:bold;color:<?php echo $accent; ?>"><?php echo $e($money($status === 'void' ? 0 : $i['total'])); ?></div>
            </td>
        </tr>
    </table>

    <table class="lines" width="100%" style="margin-top:22px">
        <thead>
            <tr><th width="52%">Description</th><th class="right" width="12%">Qty</th><th class="right" width="18%">Unit price</th><th class="right" width="18%">Amount</th></tr>
        </thead>
        <tbody>
            <?php foreach ($lines as $l) { ?>
                <tr>
                    <td><b><?php echo $e($l[0]); ?></b><?php if ($l[1] !== '') { ?><br><span class="muted"><?php echo $e($l[1]); ?></span><?php } ?></td>
                    <td class="right"><?php echo $e(number_format((float) $l[2])); ?></td>
                    <td class="right"><?php echo $e($money($l[3])); ?></td>
                    <td class="right"><?php echo $e($money($l[4])); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <table width="100%" style="margin-top:6px">
        <tr>
            <td width="52%"></td>
            <td width="48%">
                <table class="totals" width="100%">
                    <tr><td>Subtotal</td><td class="right"><?php echo $e($money($i['price'])); ?></td></tr>
                    <?php if ((float) $i['discount'] > 0) { ?>
                        <tr><td>Discount<?php echo !empty($i['reference']) && stripos($i['reference'], 'coupon') === 0 ? ' (' . $e($i['reference']) . ')' : ''; ?></td><td class="right">- <?php echo $e($money($i['discount'])); ?></td></tr>
                    <?php } ?>
                    <tr class="grand"><td><?php echo $status === 'paid' ? 'Total paid' : 'Total due'; ?></td><td class="right"><?php echo $e($money($i['total'])); ?></td></tr>
                </table>
            </td>
        </tr>
    </table>

    <?php if (!empty($i['notes'])) { ?>
        <div style="margin-top:18px"><div class="label">Note</div><?php echo nl2br($e($i['notes'])); ?></div>
    <?php } ?>

    <?php if ($status === 'paid') { ?>
        <div class="box" style="margin-top:22px">
            <div class="label">Payment received</div>
            <?php echo $e(isset($methods[$i['method']]) ? $methods[$i['method']] : ucwords(str_replace('_', ' ', $i['method']))); ?>
            <?php if (!empty($i['reference'])) { ?> &middot; Reference: <?php echo $e($i['reference']); ?><?php } ?>
            &middot; <?php echo $e($date(!empty($i['paid_at']) ? $i['paid_at'] : $i['issued_at'])); ?>
        </div>
    <?php } elseif ($status !== 'void') { ?>
        <div class="box" style="margin-top:22px">
            <div class="label" style="margin-bottom:4px">How to pay</div>
            <?php if ($status === 'unpaid') { ?>
                <div style="margin-bottom:6px">Pay online from the invoice page (the link in your email), or by bank transfer:</div>
            <?php } ?>
            <?php if (trim(strip_tags($bank_instructions)) !== '') { ?>
                <div class="prose"><?php echo $bank_instructions; ?></div>
            <?php } else { ?>
                <div class="muted">Contact us at <?php echo $e($seller['email']); ?> for the bank account details.</div>
            <?php } ?>
            <div class="muted" style="margin-top:6px">Use <b><?php echo $e($i['invoice_no']); ?></b> as the payment reference.</div>
        </div>
    <?php } ?>

    <div style="margin-top:30px;border-top:1px solid #e5e7eb;padding-top:10px" class="muted">
        <?php echo nl2br($e($seller['footer'])); ?>
        <?php if ($status === 'draft') { ?><br>This is a pro-forma (draft) invoice for your review: it has no number yet and is not a request for payment.<?php } ?>
    </div>
</div>
