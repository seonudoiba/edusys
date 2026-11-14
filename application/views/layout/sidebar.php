<aside class="main-sidebar" id="alert2">
    <?php if ($this->rbac->hasPrivilege('student', 'can_view')) { ?>
        <form class="navbar-form navbar-left search-form2" role="search"
            action="<?php echo site_url('admin/admin/search'); ?>" method="POST">
            <?php echo $this->customlib->getCSRF(); ?>
            <div class="input-group ">

                <input type="text" name="search_text" class="form-control search-form"
                    placeholder="<?php echo $this->lang->line('search_by_student_name'); ?>">
                <span class="input-group-btn">
                    <button type="submit" name="search" id="search-btn"
                        style="padding: 3px 12px !important;border-radius: 0px 30px 30px 0px; background: #fff;"
                        class="btn btn-flat"><i class="fa fa-search"></i></button>
                </span>
            </div>
        </form>
    <?php } ?>
    <section class="sidebar" id="sibe-box">
        <?php $this->load->view('layout/top_sidemenu'); ?>

        <ul class="sidebar-menu verttop">
            <?php $on_dashboard = $this->router->fetch_class() === 'admin' && $this->router->fetch_method() === 'dashboard'; ?>
            <li class="<?php echo $on_dashboard ? 'active' : ''; ?>">
                <a href="<?php echo site_url('dashboard'); ?>"<?php echo $on_dashboard ? ' aria-current="page"' : ''; ?>>
                    <i class="fa fa-tachometer"></i> <span><?php echo $this->lang->line('dashboard') ?: 'Dashboard'; ?></span>
                </a>
            </li>

            <!-- //==================sidebar dynamic======================= -->
            <?php
            $side_list = side_menu_list(1);

            // The current page's entry: the one whose controller and methods list this page; otherwise the
            // one whose address is the longest start of this page's address (admin/fees/charge/... is under
            // admin/fees). Its menu stays open, so staff see where they are.
            $active_sub = null;
            $active_menu = null;
            $best = -1;
            $here = trim($this->uri->uri_string(), '/');
            foreach ((array) $side_list as $m) {
                foreach (!empty($m->submenus) ? $m->submenus : array() as $s) {
                    if (activate_submenu($s->activate_controller, explode(',', (string) $s->activate_methods)) === 'active') {
                        $active_sub = $s->id;
                        $active_menu = $m->id;
                        break 2;
                    }
                    $u = trim((string) $s->url, '/');
                    if ($u !== '' && ($here === $u || strpos($here, $u . '/') === 0) && strlen($u) > $best) {
                        $best = strlen($u);
                        $active_sub = $s->id;
                        $active_menu = $m->id;
                    }
                }
            }

            if (!empty($side_list)) {
                foreach ($side_list as $side_list_key => $side_list_value) {

                    $module_permission = access_permission_sidebar_remove_pipe($side_list_value->access_permissions);
                    $module_access = false;
                    if (!empty($module_permission)) {
                        foreach ($module_permission as $m_permission_key => $m_permission_value) {
                            $cat_permission = access_permission_remove_comma($m_permission_value);

                            if ($this->rbac->hasPrivilege($cat_permission[0], $cat_permission[1])) {
                                $module_access = true;
                                break;
                            }
                        }
                    }
                    if ($module_access) {
                        if ($this->module_lib->hasModule($side_list_value->short_code) && $this->module_lib->hasActive($side_list_value->short_code)) {

                            ?>

                            <li class="treeview <?php echo $side_list_value->id == $active_menu ? 'active' : ($active_menu === null ? activate_main_menu($side_list_value->activate_menu) : ''); ?>">

                                <a href="#">
                                    <i class="<?php echo $side_list_value->icon; ?>"></i>
                                    <span><?php echo $this->lang->line($side_list_value->lang_key) ?: html_escape($side_list_value->menu); ?></span> <i
                                        class="fa fa-angle-left pull-right"></i>
                                </a>

                                <?php
                                if (!empty($side_list_value->submenus)) {
                                    ?>
                                    <ul class="treeview-menu">
                                        <?php
                                        foreach ($side_list_value->submenus as $submenu_key => $submenu_value) {

                                            $sidebar_permission = access_permission_sidebar_remove_pipe($submenu_value->access_permissions);
                                            $sidebar_access = false;

                                            if (!empty($sidebar_permission)) {
                                                foreach ($sidebar_permission as $sidebar_permission_key => $sidebar_permission_value) {
                                                    $sidebar_cat_permission = access_permission_remove_comma($sidebar_permission_value);

                                                    if ($submenu_value->addon_permission != "") {
                                                        if (
                                                            $this->rbac->hasPrivilege($sidebar_cat_permission[0], $sidebar_cat_permission[1])
                                                            && $this->auth->addonchk($submenu_value->addon_permission, false)
                                                        ) {
                                                            $sidebar_access = true;
                                                            break;
                                                        }
                                                    } else {
                                                        if ($this->rbac->hasPrivilege($sidebar_cat_permission[0], $sidebar_cat_permission[1])) {
                                                            $sidebar_access = true;
                                                            break;
                                                        }
                                                    }
                                                }
                                            }

                                            if ($sidebar_access) {
                                                if (!empty($submenu_value->permission_group_id)) {
                                                    if (!$this->module_lib->hasActive($submenu_value->short_code)) {
                                                        continue;
                                                    }
                                                }

                                                ?>

                                                <li class="<?php echo $submenu_value->id == $active_sub ? 'active' : ''; ?>">
                                                    <a href="<?php echo site_url($submenu_value->url); ?>"<?php echo $submenu_value->id == $active_sub ? ' aria-current="page"' : ''; ?>><i
                                                            class="fa fa-angle-double-right"></i><?php echo $this->lang->line($submenu_value->lang_key) ?: html_escape($submenu_value->menu); ?></a>
                                                </li>


                                                <?php
                                            }

                                        }

                                        ?>
                                    </ul>

                                    <?php

                                }
                                ?>
                            </li>
                            <?php
                        }
                    }
                }
            }
            ?>
            <!-- //==================sidebar dynamic======================= -->

        </ul>
    </section>
</aside>
<script>
    // Keep the current page's menu entry in view in a long sidebar. The sidebar scrolls with slimScroll
    // (set up by the layout when the page has loaded), so it is moved with slimScroll's scrollTo.
    window.addEventListener('load', function () {
        setTimeout(function () {
            var cur = document.querySelector('.sidebar-menu .treeview-menu > li.active');
            var box = document.getElementById('sibe-box');
            if (!cur || !box || box.scrollHeight <= box.clientHeight) { return; }
            var top = cur.getBoundingClientRect().top - box.getBoundingClientRect().top + box.scrollTop;
            if (top + cur.offsetHeight <= box.clientHeight - 20) { return; } // already in view
            var to = Math.max(0, Math.round(top - box.clientHeight / 3));
            if (window.jQuery && jQuery.fn.slimScroll && jQuery(box).parent().hasClass('slimScrollDiv')) {
                jQuery(box).slimScroll({ scrollTo: to + 'px' });
            } else {
                box.scrollTop = to;
            }
        }, 50);
    });
</script>