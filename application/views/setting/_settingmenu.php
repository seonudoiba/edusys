<div class="col-md-2">
    <div class="box border0">
        <ul class="tablists">
            <li class="<?php echo set_SubSubmenu('schsettings/index'); ?>">
                <a class="<?php echo set_SubSubmenu('schsettings/index'); ?>" href="<?php echo site_url('schsettings') ?>"><?php echo $this->lang->line('general_setting'); ?></a>
            </li>            
            <li class="<?php echo set_SubSubmenu('schsettings/logo'); ?>">
                <a class="<?php echo set_SubSubmenu('schsettings/logo'); ?>" href="<?php echo site_url('schsettings/logo') ?>"><?php echo $this->lang->line('logo'); ?></a>
            </li>


            <li class="<?php echo set_SubSubmenu('schsettings/login_page_background'); ?>">
                <a class="<?php echo set_SubSubmenu('schsettings/login_page_background'); ?>" href="<?php echo site_url('schsettings/login_page_background') ?>"><?php echo $this->lang->line('login_page_background'); ?></a>
            </li> 
            
            
            <li class="<?php echo set_SubSubmenu('schsettings/backendtheme'); ?>">
                <a class="<?php echo set_SubSubmenu('schsettings/backendtheme'); ?>" href="<?php echo site_url('schsettings/backendtheme') ?>"><?php echo $this->lang->line('backend_theme'); ?></a>
            </li>

            <li class="<?php echo set_SubSubmenu('schsettings/maintenance'); ?>">
                <a class="<?php echo set_SubSubmenu('schsettings/maintenance'); ?>" href="<?php echo site_url('schsettings/maintenance') ?>"><?php echo $this->lang->line('maintenance'); ?></a>
            </li>
        </ul>
    </div>
</div><!--./col-md-3--> 