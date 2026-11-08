<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Shown when the school's public website is switched off (Front CMS off), instead of silently
 * sending visitors to the login page. Offers online admission when it is open, and the logins.
 *
 * @var string $school_name
 * @var bool   $admission    online admission is open
 * @var bool   $user_login   student/parent login is enabled
 */
$e = function ($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); };
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title><?php echo $e($school_name); ?></title>
    <style>
        :root { --bg: #f5f6f8; --card: #fff; --text: #1f2933; --muted: #616e7c; --line: #e4e7eb; --accent: #1d4ed8; --accent-text: #fff; }
        @media (prefers-color-scheme: dark) {
            :root { --bg: #111418; --card: #1b1f24; --text: #e4e7eb; --muted: #9aa5b1; --line: #2d333b; --accent: #3b82f6; --accent-text: #fff; }
        }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 16px;
               background: var(--bg); color: var(--text); font: 16px/1.5 system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif; }
        main { width: 100%; max-width: 460px; background: var(--card); border: 1px solid var(--line); border-radius: 12px; padding: 32px 24px; text-align: center; }
        h1 { margin: 0 0 8px; font-size: 22px; line-height: 1.3; }
        p { margin: 0 0 24px; color: var(--muted); }
        .actions { display: flex; flex-direction: column; gap: 10px; }
        a.btn { display: block; padding: 12px 16px; border-radius: 8px; border: 1px solid var(--line); color: var(--text); text-decoration: none; font-weight: 600; }
        a.btn:hover, a.btn:focus-visible { border-color: var(--accent); }
        a.btn.primary { background: var(--accent); border-color: var(--accent); color: var(--accent-text); }
    </style>
</head>
<body>
<main>
    <h1><?php echo $e($school_name); ?></h1>
    <p>The school's website is not available at the moment.</p>
    <div class="actions">
        <?php if ($admission) { ?>
            <a class="btn primary" href="<?php echo $e(site_url('online_admission')); ?>">Apply for admission</a>
        <?php } ?>
        <?php if ($user_login) { ?>
            <a class="btn<?php echo $admission ? '' : ' primary'; ?>" href="<?php echo $e(site_url('site/userlogin')); ?>">Student and parent login</a>
        <?php } ?>
        <a class="btn" href="<?php echo $e(site_url('site/login')); ?>">Staff login</a>
    </div>
</main>
</body>
</html>
