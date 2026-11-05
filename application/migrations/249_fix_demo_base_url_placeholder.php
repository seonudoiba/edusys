<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Replaces the "{{BASEURL}}" placeholder the demo export left in front-website menu links and
 * news/event images with "/" (root-relative, so it works on every school's host). Schools set up
 * from the demo default data before the generator did this showed links like
 * "/%7B%7BBASEURL%7D%7Dexamresult".
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Fix_demo_base_url_placeholder extends CI_Migration
{
    private $columns = array(
        'front_cms_menu_items' => 'ext_url_link',
        'front_cms_programs' => 'feature_image',
    );

    public function up()
    {
        $columns = $this->columns;
        $this->db->without_tenant(function ($db) use ($columns) {
            foreach ($columns as $table => $column) {
                $db->set($column, "REPLACE(`$column`, '{{BASEURL}}', '/')", false)
                    ->like($column, '{{BASEURL}}')
                    ->update($table);
            }
        });
    }

    public function down()
    {
        // Nothing to restore: the placeholder never worked.
    }
}
