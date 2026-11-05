<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/
$hook['pre_system'][] = array(
    'class'    => 'maintenance_hook',
    'function' => 'offline_check',
    'filename' => 'maintenance_hook.php',
    'filepath' => 'hooks'
);

// Resolve the school for this request (subdomain -> school) before any controller runs.
// The school's plan limits (students, staff, storage), once the controller exists.
$hook['post_controller_constructor'][] = array(
    'class'    => 'Plan_policy_hook',
    'function' => 'enforce',
    'filename' => 'Plan_policy_hook.php',
    'filepath' => 'hooks'
);
$hook['pre_controller'][] = array(
    'class'    => 'Tenant_hook',
    'function' => 'resolve',
    'filename' => 'Tenant_hook.php',
    'filepath' => 'hooks'
);