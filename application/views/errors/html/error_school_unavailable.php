<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// Shown by hooks/Tenant_hook.php when a school exists but is disabled (school.status).
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $heading; ?></title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; text-align: center; color: #333; }
        .container { width: 80%; max-width: 640px; margin: 50px auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        h1 { color: #e74c3c; font-size: 30px; }
        p { font-size: 18px; color: #555; }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo $heading; ?></h1>
        <?php echo $message; ?>
    </div>
</body>
</html>
