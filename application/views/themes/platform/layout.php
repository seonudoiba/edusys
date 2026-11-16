<!DOCTYPE html>
<html lang="en">
<head>
<?php $this->load->view('themes/platform/_head'); ?>
</head>
<body class="<?php echo html_escape(isset($pt_classes) ? $pt_classes : ''); ?>">
    <a class="skip" href="#main">Skip to content</a>
    <?php echo $header; ?>
    <main id="main"><?php echo $content; ?></main>
    <?php echo $footer; ?>
</body>
</html>
