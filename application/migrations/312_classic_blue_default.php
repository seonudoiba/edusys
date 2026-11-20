<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Classic Blue is the default website theme (and so the admin colour). New schools start with it
 * (default seed); schools still on the old default, Rose, that never changed their design move to it.
 * A school that chose or customised a theme keeps it. Front CMS > Website Design changes it any time.
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Classic_blue_default extends CI_Migration
{
    public function up()
    {
        $this->db->without_tenant(function ($db) {
            $db->where('theme', 'material_pink')->group_start()->where('theme_options IS NULL', null, false)->or_where('theme_options', '')->group_end()
                ->where('school_id IN (SELECT id FROM school WHERE is_platform = 0)', null, false) // tenancy-lint: ok all schools
                ->update('front_cms_settings', array('theme' => 'default'));
        });
    }

    public function down()
    {
        // which schools were moved is not recorded; nothing to undo
    }
}
