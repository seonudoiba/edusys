<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Result checker PINs</title>
    <style>
        *{box-sizing:border-box}
        body{margin:0;padding:14px;font-family:"Segoe UI",Roboto,Arial,sans-serif;color:#0f172a;background:#fff}
        .bar{display:flex;gap:10px;align-items:center;margin-bottom:14px}
        .bar button{background:<?php echo html_escape($brand['color']); ?>;color:#fff;border:0;border-radius:8px;padding:9px 16px;font-weight:600;cursor:pointer}
        .grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px}
        .card{border:1.5px dashed #94a3b8;border-radius:10px;padding:10px 12px;page-break-inside:avoid;break-inside:avoid}
        .top{display:flex;align-items:center;gap:8px;border-bottom:2px solid <?php echo html_escape($brand['color']); ?>;padding-bottom:6px;margin-bottom:6px}
        .top img{height:30px;width:auto;max-width:60px;object-fit:contain}
        .top b{font-size:12px;color:<?php echo html_escape($brand['color']); ?>;line-height:1.2}
        .lbl{font-size:9px;text-transform:uppercase;letter-spacing:.08em;color:#64748b}
        .pin{font-family:Consolas,"Courier New",monospace;font-size:19px;font-weight:800;letter-spacing:.08em;margin:2px 0 4px}
        .row{display:flex;justify-content:space-between;font-size:10px;color:#334155}
        .how{font-size:9px;color:#475569;margin-top:5px;line-height:1.35}
        @media print{.bar{display:none}body{padding:0}.card,.top{-webkit-print-color-adjust:exact;print-color-adjust:exact}}
    </style>
</head>
<body>
    <div class="bar"><button onclick="window.print()">Print</button><span><?php echo count($pins); ?> cards · 3 per row</span></div>
    <div class="grid">
        <?php foreach ($pins as $p) { if (!$p['is_active']) { continue; } ?>
            <div class="card">
                <div class="top"><?php if ($brand['logo']) { ?><img src="<?php echo html_escape($brand['logo']); ?>" alt=""><?php } ?><b><?php echo html_escape($brand['name']); ?><br><span style="color:#475569;font-weight:600">Result checker card</span></b></div>
                <div class="lbl">PIN</div>
                <div class="pin"><?php echo html_escape(implode(' ', str_split($p['pin'], 4))); ?></div>
                <div class="row"><span>Serial: <b><?php echo html_escape($p['serial_no']); ?></b></span><span>Uses: <?php echo (int) $p['max_uses']; ?></span></div>
                <div class="how">Visit <b><?php echo html_escape(preg_replace('#^https?://#', '', $checker_url)); ?></b>, choose the exam, enter the admission number and this PIN. The PIN works for one student only.</div>
            </div>
        <?php } ?>
    </div>
</body>
</html>
