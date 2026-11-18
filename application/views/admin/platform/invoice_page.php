<?php
/*
 * The invoice as a web page: a toolbar (Print, Download PDF, pay online / by bank transfer when
 * unpaid) above the document. $document, $invoice, $status, $opts: pdf_url, pdf_post (fields to
 * POST for a draft's PDF), pay_url, online_methods [key => name], notice, back_url.
 */
$e = function ($v) { return html_escape((string) $v); };
$i = $invoice;
$o = $opts;
$title = $i['invoice_no'] === 'DRAFT' ? 'Pro-forma invoice' : 'Invoice ' . $i['invoice_no'];
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo $e($title); ?></title>
    <style>
        :root { --bg: #eef1f5; --paper: #fff; --ink: #1f2937; --muted: #6b7280; --accent: #0b5394; --line: #e5e7eb; }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--bg); color: var(--ink); font-family: -apple-system, "Segoe UI", Roboto, Arial, sans-serif; }
        .toolbar { max-width: 860px; margin: 0 auto; padding: 18px 16px 0; display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
        .toolbar .grow { flex: 1 1 auto; }
        .btn { display: inline-flex; align-items: center; gap: 6px; border: 1px solid var(--line); background: #fff; color: var(--ink); padding: 9px 14px; border-radius: 6px;
               font-size: 14px; text-decoration: none; cursor: pointer; font-family: inherit; }
        .btn:hover { border-color: #c7cdd6; }
        .btn-primary { background: var(--accent); border-color: var(--accent); color: #fff; }
        .btn-primary:hover { filter: brightness(1.08); }
        .notice { max-width: 860px; margin: 12px auto 0; padding: 0 16px; }
        .notice div { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 10px 14px; border-radius: 6px; font-size: 14px; }
        .paper { max-width: 860px; margin: 14px auto 32px; background: var(--paper); padding: 36px 40px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,.08), 0 8px 24px rgba(0,0,0,.06); }
        .paper img { max-width: 100%; }
        form.inline { display: inline; margin: 0; }
        select.btn { padding-right: 10px; }
        @media (max-width: 600px) { .paper { padding: 22px 16px; margin: 12px 8px 24px; } .inv table, .inv td { font-size: 9pt; } }
        @media print {
            body { background: #fff; }
            .toolbar, .notice { display: none !important; }
            .paper { box-shadow: none; margin: 0; padding: 0; max-width: none; }
            @page { margin: 14mm; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <?php if (!empty($o['back_url'])) { ?><a class="btn" href="<?php echo $e($o['back_url']); ?>">&larr; Back</a><?php } ?>
        <span class="grow"></span>
        <button class="btn" type="button" onclick="window.print()">&#128424; Print</button>
        <?php if (!empty($o['pdf_post'])) { ?>
            <form class="inline" method="post" action="<?php echo $e($o['pdf_url']); ?>">
                <?php foreach ($o['pdf_post'] as $k => $v) { foreach ((array) $v as $vv) { ?>
                    <input type="hidden" name="<?php echo $e($k) . (is_array($v) ? '[]' : ''); ?>" value="<?php echo $e($vv); ?>">
                <?php } } ?>
                <button class="btn" type="submit">&#11015; Download PDF</button>
            </form>
        <?php } elseif (!empty($o['pdf_url'])) { ?>
            <a class="btn" href="<?php echo $e($o['pdf_url']); ?>">&#11015; Download PDF</a>
        <?php } ?>
        <?php if ($status === 'unpaid' && !empty($o['pay_url'])) { ?>
            <?php foreach ((array) (isset($o['online_methods']) ? $o['online_methods'] : array()) as $key => $name) { ?>
                <form class="inline" method="post" action="<?php echo $e($o['pay_url']); ?>">
                    <input type="hidden" name="method" value="<?php echo $e($key); ?>">
                    <button class="btn btn-primary" type="submit">Pay online (<?php echo $e($name); ?>)</button>
                </form>
            <?php } ?>
            <form class="inline" method="post" action="<?php echo $e($o['pay_url']); ?>">
                <input type="hidden" name="method" value="bank_transfer">
                <button class="btn <?php echo empty($o['online_methods']) ? 'btn-primary' : ''; ?>" type="submit">Paid by bank transfer? Send the receipt</button>
            </form>
        <?php } ?>
    </div>
    <?php if (!empty($o['notice'])) { ?><div class="notice"><div><?php echo $e($o['notice']); ?></div></div><?php } ?>
    <div class="paper"><?php echo $document; ?></div>
</body>
</html>
