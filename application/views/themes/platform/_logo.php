<?php /* The logo set in Front CMS > Settings; the name only if none is set. */
if (!empty($front_setting->logo)) { ?><img src="<?php echo html_escape(base_url(ltrim(str_replace('./', '', $front_setting->logo), '/'))); ?>" alt="EduSYS"><?php } else { ?>EduSYS<?php }
