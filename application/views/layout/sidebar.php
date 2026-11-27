<?php
$this->load->helper('nav_label');
if (!function_exists('sidebar_submenu_icon')) {
    /** An icon for a sidebar sub-menu entry: by its language key, else by words in its URL. */
    function sidebar_submenu_icon($key, $url)
    {
        static $by_key = array(
            'backup_restore' => 'fa-database', 'file_types' => 'fa-file-o', 'design_admit_card' => 'fa-id-card-o', 'manage_alumini' => 'fa-graduation-cap',
            'events' => 'fa-calendar', 'approve_leave' => 'fa-check-square-o', 'audit_trail_report' => 'fa-history', 'platform_backups' => 'fa-database',
            'plan' => 'fa-tags', 'coupons' => 'fa-ticket', 'invoices' => 'fa-file-text-o', 'reports' => 'fa-bar-chart', 'book_list' => 'fa-book',
            'captcha_setting' => 'fa-shield', 'student_certificate' => 'fa-certificate', 'complain' => 'fa-commenting-o', 'setting' => 'fa-cog',
            'live_class_report' => 'fa-line-chart', 'live_meeting' => 'fa-video-camera', 'live_meeting_report' => 'fa-bar-chart', 'live_class' => 'fa-desktop',
            'content_share_list' => 'fa-share-alt', 'upload_content' => 'fa-cloud-upload', 'content_type' => 'fa-folder-open-o', 'currency' => 'fa-money',
            'custom_fields' => 'fa-sliders', 'department' => 'fa-sitemap', 'designation' => 'fa-id-badge', 'disable_reason' => 'fa-ban',
            'postal_dispatch' => 'fa-paper-plane-o', 'website_enquiries' => 'fa-globe', 'admission_enquiry' => 'fa-question-circle-o',
            'exam_group' => 'fa-object-group', 'exam_result' => 'fa-trophy', 'print_admit_card' => 'fa-print', 'examinations' => 'fa-pencil-square-o',
            'print_marksheet' => 'fa-print', 'exam_schedule' => 'fa-calendar-check-o', 'add_expense' => 'fa-minus-circle', 'search_expense' => 'fa-search',
            'expense_head' => 'fa-list-alt', 'fees_discount' => 'fa-percent', 'fees_group' => 'fa-object-group', 'fees_master' => 'fa-sitemap',
            'fees_reminder' => 'fa-bell-o', 'fees_overview' => 'fa-pie-chart', 'fees_discounts_balances' => 'fa-balance-scale', 'fees_collections' => 'fa-inbox',
            'fees_create_fees' => 'fa-plus-square-o', 'fees_carry_forward' => 'fa-share', 'fees_type' => 'fa-tag', 'banner_images' => 'fa-picture-o',
            'website_design' => 'fa-paint-brush', 'event' => 'fa-calendar', 'gallery' => 'fa-camera-retro', 'media_manager' => 'fa-folder-open',
            'menus' => 'fa-bars', 'news' => 'fa-newspaper-o', 'pages' => 'fa-file-text-o', 'front_cms_setting' => 'fa-cog', 'setup_two_fa' => 'fa-lock',
            'phone_call_log' => 'fa-phone', 'generate_certificate' => 'fa-certificate', 'generate_id_card' => 'fa-id-card', 'generate_staff_id_card' => 'fa-id-card-o',
            'marks_grade' => 'fa-star-half-o', 'hostel' => 'fa-building', 'hostel_rooms' => 'fa-bed', 'add_income' => 'fa-plus-circle', 'search_income' => 'fa-search',
            'income_head' => 'fa-list-alt', 'issue_item' => 'fa-sign-out', 'add_item' => 'fa-cube', 'item_category' => 'fa-th-large', 'add_item_stock' => 'fa-cubes',
            'item_store' => 'fa-archive', 'item_supplier' => 'fa-truck', 'languages' => 'fa-language', 'approve_leave_request' => 'fa-check-square-o',
            'leave_type' => 'fa-list-ul', 'copy_old_lesson' => 'fa-copy', 'lesson' => 'fa-book', 'topic' => 'fa-list-ol', 'send_email' => 'fa-envelope-o',
            'send_sms' => 'fa-mobile', 'email_template' => 'fa-envelope-square', 'email_sms_log' => 'fa-history', 'schedule_email_sms_log' => 'fa-clock-o',
            'sms_template' => 'fa-comment-o', 'marks_division' => 'fa-columns', 'design_marksheet' => 'fa-paint-brush', 'issue_return' => 'fa-exchange',
            'add_student' => 'fa-user-plus', 'add_staff_member' => 'fa-user-plus', 'modules' => 'fa-puzzle-piece', 'notice_board' => 'fa-bullhorn',
            'notification_setting' => 'fa-bell', 'offline_bank_payments' => 'fa-university', 'online_admission' => 'fa-wpforms', 'online_exam' => 'fa-laptop',
            'online_examinations' => 'fa-bar-chart', 'payment_methods' => 'fa-credit-card', 'payroll' => 'fa-money', 'pickup_point' => 'fa-map-marker',
            'route_pickup_point' => 'fa-map-signs', 'student_transport_fees' => 'fa-bus', 'plan_type' => 'fa-tags', 'email_templates' => 'fa-envelope-square',
            'email_log' => 'fa-history', 'platform_settings_health' => 'fa-heartbeat', 'print_headerfooter' => 'fa-print', 'attendance' => 'fa-check-square-o',
            'question_bank' => 'fa-database', 'postal_receive' => 'fa-inbox', 'roles_permissions' => 'fa-key', 'room_type' => 'fa-bed', 'routes' => 'fa-road',
            'transport' => 'fa-bus', 'student_house' => 'fa-home', 'schools' => 'fa-university', 'school_create' => 'fa-plus', 'waiting_for_approval' => 'fa-hourglass-half',
            'sidebar_menu' => 'fa-bars', 'platform_staff' => 'fa-users', 'staff_directory' => 'fa-address-book-o', 'disabled_staff' => 'fa-user-times',
            'apply_leave' => 'fa-plane', 'teachers_rating' => 'fa-star', 'staff_attendance' => 'fa-calendar-check-o', 'staff_id_card' => 'fa-id-badge',
            'promote_students' => 'fa-level-up', 'student_attendance' => 'fa-check-square-o', 'attendance_by_date' => 'fa-calendar', 'student_id_card' => 'fa-id-card',
            'subjects' => 'fa-book', 'period_attendance' => 'fa-clock-o', 'period_attendance_by_date' => 'fa-calendar', 'subject_group' => 'fa-object-group',
            'bank_transfer_payments' => 'fa-university', 'subscription' => 'fa-refresh', 'support_tickets' => 'fa-life-ring', 'support_desk' => 'fa-headphones',
            'manage_lesson_plan' => 'fa-tasks', 'manage_syllabus_status' => 'fa-check-circle-o', 'system_fields' => 'fa-th-list',
            'assign_class_teacher' => 'fa-user-circle-o', 'assign_subject_teachers' => 'fa-users', 'class_timetable' => 'fa-table', 'teachers_timetable' => 'fa-calendar-o',
            'system_update' => 'fa-cloud-download', 'login_history' => 'fa-sign-in', 'user_log' => 'fa-history', 'users' => 'fa-users', 'vehicles' => 'fa-car',
            'assign_vehicle' => 'fa-random', 'video_tutorial' => 'fa-youtube-play', 'visitor_book' => 'fa-address-book', 'setup_front_office' => 'fa-cogs',
            'incidents' => 'fa-exclamation-triangle', 'assign_incident' => 'fa-flag', 'report_behaviour' => 'fa-bullhorn', 'assign_students_transport' => 'fa-bus', 'assign_students_hostel' => 'fa-bed', 'student_categories' => 'fa-tags', 'assessment' => 'fa-check-square',
            'exam' => 'fa-file-text-o', 'exam_grade' => 'fa-star-half-o', 'observation' => 'fa-eye', 'assign_observation' => 'fa-binoculars',
            'observation_parameter' => 'fa-sliders', 'template' => 'fa-clone', 'term' => 'fa-calendar-o', 'class' => 'fa-th-large', 'email_setting' => 'fa-envelope',
            'finance' => 'fa-money', 'add_homework' => 'fa-pencil', 'daily_assignment' => 'fa-pencil-square', 'homework' => 'fa-book', 'online_course' => 'fa-play-circle',
            'course_category' => 'fa-folder', 'online_course_report' => 'fa-bar-chart', 'offline_payment' => 'fa-university', 'alumni' => 'fa-graduation-cap',
            'human_resource' => 'fa-users', 'inventory' => 'fa-cubes', 'lesson_plan' => 'fa-tasks', 'library' => 'fa-book', 'student_information' => 'fa-user',
            'general_setting' => 'fa-cogs', 'fees_setting' => 'fa-money', 'sections' => 'fa-th', 'session_setting' => 'fa-calendar-o', 'sms_setting' => 'fa-mobile',
            'bulk_delete' => 'fa-trash-o', 'login_credentials_send' => 'fa-key', 'student_admission' => 'fa-user-plus', 'disabled_students' => 'fa-user-times',
            'multi_class_student' => 'fa-users', 'student_profile_update' => 'fa-pencil-square-o', 'student_details' => 'fa-search', 'collect_fees' => 'fa-money',
            'search_due_fees' => 'fa-exclamation-circle', 'search_fees_payment' => 'fa-search-plus',
            'class_results' => 'fa-th-list', 'cbt_by_class' => 'fa-th-large', 'comment_bank' => 'fa-commenting-o',
            'broadsheet' => 'fa-table', 'result_checker_pins' => 'fa-key', 'promotion_engine' => 'fa-level-up',
        );
        static $by_word = array('report' => 'fa-bar-chart', 'setting' => 'fa-cog', 'attend' => 'fa-check-square-o', 'fee' => 'fa-money', 'exam' => 'fa-file-text-o',
            'student' => 'fa-user', 'staff' => 'fa-id-badge', 'mail' => 'fa-envelope-o', 'sms' => 'fa-mobile', 'book' => 'fa-book', 'search' => 'fa-search',
            'add' => 'fa-plus-circle', 'list' => 'fa-list', 'print' => 'fa-print', 'class' => 'fa-th-large', 'time' => 'fa-clock-o');
        $key = (string) $key;
        if (isset($by_key[$key])) {
            return $by_key[$key];
        }
        $hay = strtolower($key . ' ' . $url);
        foreach ($by_word as $w => $icon) {
            if (strpos($hay, $w) !== false) {
                return $icon;
            }
        }
        return 'fa-circle-o';
    }
}
?>
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
                    // methods may also name other pages by address ("studentfee/addfee"): an entry that
                    // took over another one (Collect fees, Discounts) stays lit on those pages
                    foreach (explode(',', (string) $s->activate_methods) as $page) {
                        $page = trim($page);
                        if (strpos($page, '/') !== false && ($here === $page || strpos($here, $page . '/') === 0)) {
                            $active_sub = $s->id;
                            $active_menu = $m->id;
                            break 3;
                        }
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
                                    <span><?php echo html_escape(nav_menu_label($side_list_value->lang_key) ?: ($this->lang->line($side_list_value->lang_key) ?: $side_list_value->menu)); ?></span> <i
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
                                                            class="fa <?php echo sidebar_submenu_icon($submenu_value->lang_key, $submenu_value->url); ?>" aria-hidden="true"></i><?php echo html_escape(nav_entry_label($submenu_value->url) ?: ($this->lang->line($submenu_value->lang_key) ?: $submenu_value->menu)); ?></a>
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