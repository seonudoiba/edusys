<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo html_escape($gateway_name); ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #ededed; color: #333; text-align: center; padding: 60px 16px; }
        .box { background: #fff; max-width: 420px; margin: 0 auto; padding: 28px; border-radius: 6px; }
        button { background: #0084b4; color: #fff; border: 0; padding: 10px 22px; border-radius: 4px; font-size: 15px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="box">
        <p>Taking you to <b><?php echo html_escape($gateway_name); ?></b> to complete the payment…</p>
        <form id="gateway_form" method="post" action="<?php echo html_escape($action); ?>">
            <?php foreach ($fields as $name => $value) { ?>
                <input type="hidden" name="<?php echo html_escape($name); ?>" value="<?php echo html_escape($value); ?>">
            <?php } ?>
            <noscript><p>JavaScript is off: click the button to continue.</p></noscript>
            <button type="submit">Continue to <?php echo html_escape($gateway_name); ?></button>
        </form>
    </div>
    <script>document.getElementById('gateway_form').submit();</script>
</body>
</html>
