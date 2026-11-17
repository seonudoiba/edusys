<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * What a school's plan allows, what it uses, and where it stands on expiry.
 *
 * The plan is the plan of the school's latest paid subscription; a school without one (added
 * by the platform, nothing recorded) has no limits and no expiry. Limits of 0 mean no limit; a
 * plan whose `modules` is NULL includes every module.
 *
 * Expiry (Platform settings): after the end date the school is in its grace period
 * (grace_days); after that it has lapsed, and the expiry policy applies: "warn" (banners and
 * emails only) or "disable" (platform_cron disables it; renewing enables it again).
 */
class Plan_limits
{
    const STORAGE_CACHE_SECONDS = 600;
    /** Modules that are never switched off by a plan (the platform's own and the system ones). */
    const NEVER_LIMITED = array('school_information', 'plan_list', 'subscription', 'platform_admin', 'system_settings');

    /** @var CI_Controller */
    private $CI;
    private $memo = array();

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->database();
        $this->CI->load->library('Platform_settings');
    }

    /**
     * @return array plan, subscription, modules (null = all), max_students, max_staff,
     *               max_storage_mb, expires_at (timestamp|null), days_left, grace_ends, state
     *               (none, active, grace, lapsed, onboarding: only the setup fee is paid, see onboarding())
     */
    public function current($school_id)
    {
        $school_id = (int) $school_id;
        if (isset($this->memo[$school_id])) {
            return $this->memo[$school_id];
        }
        $sub = $this->CI->db->select('subscription.*')
            ->where(array('school_id' => $school_id, 'payment_status' => 1))
            ->order_by('id', 'desc')->limit(1)->get('subscription')->row_array();
        $plan = $sub ? $this->CI->db->where('plan_id', (int) $sub['plan_id'])->get('plan')->row_array() : null;
        $out = array('subscription' => $sub, 'plan' => $plan, 'modules' => null, 'max_students' => 0, 'max_staff' => 0,
            'max_storage_mb' => 0, 'expires_at' => null, 'days_left' => null, 'grace_ends' => null, 'state' => 'none');
        if ($plan) {
            $mods = json_decode((string) $plan['modules'], true);
            $out['modules'] = is_array($mods) ? $mods : null;
            $out['max_students'] = (int) $plan['max_students'];
            $out['max_staff'] = (int) $plan['max_staff'];
            $out['max_storage_mb'] = (int) $plan['max_storage_mb'];
        }
        // Per-student subscriptions: the limit is what was paid for.
        if ($sub && !empty($sub['student_count'])) {
            $out['max_students'] = (int) $sub['student_count'];
        }
        if ($sub && isset($sub['kind']) && $sub['kind'] === 'setup') {
            $out['state'] = 'onboarding'; // no end date until the first subscription payment
        } elseif ($sub) {
            $expires = strtotime($sub['created_at']) + (int) $sub['days'] * 86400;
            $grace = max(0, (int) $this->CI->platform_settings->get('grace_days'));
            $out['expires_at'] = $expires;
            $out['days_left'] = (int) floor(($expires - time()) / 86400);
            $out['grace_ends'] = $expires + $grace * 86400;
            $out['state'] = time() <= $expires ? 'active' : (time() <= $out['grace_ends'] ? 'grace' : 'lapsed');
        }
        return $this->memo[$school_id] = $out;
    }

    /**
     * True when the school has paid only the setup fee (registration "setup fee now, subscription
     * later"): staff can set it up, students and parents can't sign in until the subscription is paid.
     */
    public function onboarding($school_id)
    {
        return $this->current($school_id)['state'] === 'onboarding';
    }

    public function usage($school_id)
    {
        $school_id = (int) $school_id;
        return $this->CI->db->without_tenant(function ($db) use ($school_id) {
            return array(
                'students' => (int) $db->where(array('school_id' => $school_id, 'is_active' => 'yes'))->count_all_results('students'),
                'staff' => (int) $db->where(array('school_id' => $school_id, 'is_active' => 1))->count_all_results('staff'),
                'storage_bytes' => $this->storage_bytes($school_id),
            );
        });
    }

    /** Size of uploads/s<id>/ (cached for a few minutes; uploads add to it as they happen). */
    public function storage_bytes($school_id, $fresh = false)
    {
        $cache = APPPATH . 'cache/storage_s' . (int) $school_id . '.txt';
        if (!$fresh && is_file($cache) && filemtime($cache) > time() - self::STORAGE_CACHE_SECONDS) {
            return (int) file_get_contents($cache);
        }
        // The school's own files only: the default images and demo media every school starts with
        // (copies of uploads/_shared/) don't count.
        $bytes = 0;
        $dir = str_replace('\\', '/', FCPATH . 'uploads/s' . (int) $school_id);
        $shared = FCPATH . 'uploads/_shared/';
        if (is_dir($dir)) {
            $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
            foreach ($it as $f) {
                if (!$f->isFile()) {
                    continue;
                }
                $rel = substr(str_replace('\\', '/', $f->getPathname()), strlen($dir) + 1);
                if (is_file($shared . $rel) && filesize($shared . $rel) === $f->getSize()) {
                    continue;
                }
                $bytes += $f->getSize();
            }
        }
        if (!is_dir(dirname($cache))) {
            @mkdir(dirname($cache), 0755, true);
        }
        @file_put_contents($cache, (string) $bytes);
        return $bytes;
    }

    /**
     * How many more active students (or staff) the school can have, or null when there is no limit.
     * @param string $what students | staff
     */
    public function places_left($school_id, $what)
    {
        $c = $this->current($school_id);
        $max = $what === 'students' ? $c['max_students'] : $c['max_staff'];
        if ($max <= 0) {
            return null;
        }
        return max(0, $max - $this->usage($school_id)[$what]);
    }

    /**
     * Why $count more students or staff can't be added, or null when they can.
     * @param string $what students | staff
     */
    public function add_error($school_id, $what, $count = 1)
    {
        $c = $this->current($school_id);
        $max = $what === 'students' ? $c['max_students'] : $c['max_staff'];
        if ($max <= 0) {
            return null;
        }
        $used = $this->usage($school_id)[$what];
        if ($used + $count <= $max) {
            return null;
        }
        $left = max(0, $max - $used);
        if ($what === 'students' && !empty($c['subscription']['student_count'])) {
            return 'Your subscription covers ' . number_format($max) . ' students and you have ' . number_format($used)
                . ($count > 1 ? ", so these $count can't all be added ($left left)" : '')
                . '. Your school owner can pay for more students (a top-up) at ' . $this->renew_url() . ', or deactivate some students first.';
        }
        $label = $what === 'students' ? 'active students' : 'active staff';
        return "Your plan ({$c['plan']['title']}) allows $max $label; you have $used"
            . ($count > 1 ? " and this adds $count ($left left)" : '') . '. Upgrade the plan, or deactivate some first.';
    }

    /** Why an upload of $bytes can't be stored, or null when it can. */
    public function storage_error($school_id, $bytes)
    {
        $c = $this->current($school_id);
        if ($c['max_storage_mb'] <= 0) {
            return null;
        }
        $limit = $c['max_storage_mb'] * 1048576;
        $used = $this->storage_bytes($school_id);
        if ($used + $bytes <= $limit) {
            return null;
        }
        return sprintf('Your plan (%s) includes %d MB of file storage and %.1f MB is used. Delete some files or upgrade the plan.',
            $c['plan']['title'], $c['max_storage_mb'], $used / 1048576);
    }

    /** Whether the school's plan includes a module (permission_group short_code). */
    public function module_allowed($school_id, $short_code)
    {
        $modules = $this->current($school_id)['modules'];
        return $modules === null || in_array($short_code, $modules, true) || in_array($short_code, self::NEVER_LIMITED, true);
    }

    /**
     * Switches the school's modules to match its plan: modules the plan excludes off; with
     * $enable_included the included ones on too (school set up, plan changed).
     * @return int modules changed
     */
    public function apply_modules($school_id, $enable_included = false)
    {
        $school_id = (int) $school_id;
        unset($this->memo[$school_id]);
        $modules = $this->current($school_id)['modules'];
        if ($modules === null && !$enable_included) {
            return 0;
        }
        return $this->CI->db->without_tenant(function ($db) use ($school_id, $modules, $enable_included) {
            $changed = 0;
            $groups = $db->select('id, short_code, is_active, system')->where('school_id', $school_id)->get('permission_group')->result_array();
            foreach ($groups as $g) {
                if ((int) $g['system'] === 1 || in_array($g['short_code'], self::NEVER_LIMITED, true)) {
                    continue;
                }
                $included = $modules === null || in_array($g['short_code'], $modules, true);
                $want = $included ? ($enable_included ? 1 : (int) $g['is_active']) : 0;
                if ($want !== (int) $g['is_active']) {
                    $db->where('id', (int) $g['id'])->update('permission_group', array('is_active' => $want));
                    $db->where(array('group_id' => (int) $g['id'], 'school_id' => $school_id))->update('permission_student', array('student' => $want, 'parent' => $want));
                    $changed++;
                }
            }
            return $changed;
        });
    }

    /**
     * Notices for the school's admins: [level (warning|danger|info), message, show renew link].
     * The subscription ends within 14 days, has ended (grace, lapsed), or a limit is at 90%.
     */
    public function notices($school_id)
    {
        $c = $this->current($school_id);
        $out = array();
        if ($c['state'] === 'active' && $c['days_left'] !== null && $c['days_left'] <= 14) {
            $out[] = array('warning', sprintf('Your subscription (%s) ends on %s, in %d day%s.', $c['plan']['title'], date('d M Y', $c['expires_at']), $c['days_left'], $c['days_left'] == 1 ? '' : 's'), true);
        } elseif ($c['state'] === 'grace') {
            $disable = $this->CI->platform_settings->get('expiry_action') === 'disable';
            $out[] = array('danger', sprintf('Your subscription ended on %s.%s', date('d M Y', $c['expires_at']),
                $disable ? ' Renew before ' . date('d M Y', $c['grace_ends']) . ', when the school will be disabled.' : ' Please renew it.'), true);
        } elseif ($c['state'] === 'lapsed') {
            $out[] = array('danger', sprintf('Your subscription ended on %s. Please renew it.', date('d M Y', $c['expires_at'])), true);
        }
        if ($c['plan']) {
            $u = $this->usage($school_id);
            $checks = array(
                array($u['students'], $c['max_students'], 'active students'),
                array($u['staff'], $c['max_staff'], 'active staff'),
                array(round($u['storage_bytes'] / 1048576), $c['max_storage_mb'], 'MB of file storage'),
            );
            foreach ($checks as $k) {
                if ($k[1] > 0 && $k[0] >= 0.9 * $k[1]) {
                    $out[] = array($k[0] >= $k[1] ? 'danger' : 'info', sprintf('Your plan (%s) includes %s %s; %s used.', $c['plan']['title'], number_format($k[1]), $k[2], number_format($k[0])), true);
                }
            }
        }
        return $out;
    }

    /** The owner's account on the platform, where they renew or change plan. */
    public function renew_url()
    {
        $this->CI->load->library('School_provisioner');
        $platform = $this->CI->db->where('is_platform', 1)->get('school')->row_array();
        return $platform ? $this->CI->school_provisioner->url_for($platform) . 'course/guestlogin' : '';
    }

    /** The modules a plan can include: the platform school's module list (the same in every school). */
    public function all_modules()
    {
        return $this->CI->db->without_tenant(function ($db) {
            $platform = $db->select('id')->where('is_platform', 1)->get('school')->row();
            if (!$platform) {
                return array();
            }
            return $db->select('name, short_code')->where('school_id', (int) $platform->id)
                ->where('system', 0)->where_not_in('short_code', Plan_limits::NEVER_LIMITED)
                ->order_by('name')->get('permission_group')->result_array();
        });
    }
}
