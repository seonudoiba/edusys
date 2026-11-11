<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * What a school pays. A rate per student per term, set by the size band of the school
 * (platform_plan_bands), and a one-time setup fee when it joins:
 *
 *   per term    students x rate
 *   per year    students x rate x 3 terms  (same price, paid at once)
 *   first time  + setup fee
 *
 * A school's limit is the student count it paid for. Passing it means a top-up: the extra students
 * x its current rate x the terms left in the period already paid for, with the same end date.
 * Renewing never charges the setup fee; the band is decided at the start of each period.
 * Bands without a rate (the largest schools) go through a quote agreed with the platform.
 */
class Plan_pricing
{
    const TERMS_PER_YEAR = 3;
    const TERM_DAYS = 122;
    const YEAR_DAYS = 365;
    const PERIODS = array('term' => 'Termly', 'year' => 'Yearly');
    const QUOTE_VALID_DAYS = 30;

    /** @var CI_Controller */
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->database();
    }

    /** The period selected first on the public pages (admin/plan): 'term' or 'year'. */
    public function default_period()
    {
        $this->CI->load->library('Platform_settings');
        $p = (string) $this->CI->platform_settings->get('default_billing_period');
        return isset(self::PERIODS[$p]) ? $p : 'term';
    }

    /** All bands, smallest first. */
    public function bands()
    {
        return $this->CI->db->order_by('min_students', 'asc')->get('platform_plan_bands')->result_array();
    }

    /** The band for a number of students, or null. */
    public function band_for($students)
    {
        foreach ($this->bands() as $b) {
            if ((int) $students >= (int) $b['min_students'] && ($b['max_students'] === null || (int) $students <= (int) $b['max_students'])) {
                return $b;
            }
        }
        return null;
    }

    /** The plan all band subscriptions belong to. */
    public function plan()
    {
        return $this->CI->db->where('is_band', 1)->limit(1)->get('plan')->row_array();
    }

    /** An open, unexpired quote by its private token, or null. */
    public function quote($token)
    {
        if (!preg_match('/^[0-9a-f]{40}$/', (string) $token)) {
            return null;
        }
        $q = $this->CI->db->where('token', $token)->get('platform_quotes')->row_array();
        return $q && $q['status'] === 'open' && $q['expires_at'] >= date('Y-m-d') ? $q : null;
    }

    /**
     * The price of a period.
     * @param array|null $quote a quote (its rate and setup fee replace the band's)
     * @param bool $first_time charge the one-time setup fee
     * @return array ok, error, needs_quote, students, period, rate, terms, days, subscription, setup, total
     */
    public function price($students, $period, $first_time = true, array $quote = null, $fixed_rate = null)
    {
        $students = (int) $students;
        $period = isset(self::PERIODS[$period]) ? $period : $this->default_period();
        $terms = $period === 'year' ? self::TERMS_PER_YEAR : 1;
        $out = array('ok' => false, 'error' => null, 'needs_quote' => false, 'students' => $students, 'period' => $period, 'terms' => $terms,
            'days' => $period === 'year' ? self::YEAR_DAYS : self::TERM_DAYS, 'rate' => 0.0, 'setup' => 0.0, 'subscription' => 0.0, 'total' => 0.0);
        if ($students < 1) {
            $out['error'] = 'Enter the number of students.';
            return $out;
        }
        if ($quote) {
            if ($students !== (int) $quote['students']) {
                $out['error'] = 'This quote is for ' . (int) $quote['students'] . ' students.';
                return $out;
            }
            $rate = (float) $quote['rate_per_term'];
            $setup = (float) $quote['setup_fee'];
        } else {
            $band = $this->band_for($students);
            if (!$band) {
                $out['error'] = 'No price is set for this number of students.';
                return $out;
            }
            $setup = (float) $band['setup_fee'];
            if ($fixed_rate !== null) {
                $rate = (float) $fixed_rate; // an agreed rate carried over on renewal
            } elseif ($band['rate_per_term'] === null) {
                $out['needs_quote'] = true;
                $out['error'] = 'Schools with ' . number_format((int) $band['min_students']) . ' or more students get a custom quote.';
                return $out;
            } else {
                $rate = (float) $band['rate_per_term'];
            }
        }
        $out['ok'] = true;
        $out['rate'] = $rate;
        $out['setup'] = $first_time ? $setup : 0.0;
        $out['subscription'] = round($students * $rate * $terms, 2);
        $out['total'] = round($out['subscription'] + $out['setup'], 2);
        return $out;
    }

    /**
     * The price of paying for more students before the period ends.
     * @param array $current the school's latest paid subscription
     * @return array ok, error, students (new total), extra, rate, terms, days, total
     */
    public function topup(array $current, $new_total)
    {
        $paid = (int) $current['student_count'];
        $new_total = (int) $new_total;
        $ends = strtotime($current['created_at']) + (int) $current['days'] * 86400;
        $days = (int) ceil(($ends - time()) / 86400); // rounded up: a top-up never shortens the period
        $out = array('ok' => false, 'error' => null, 'students' => $new_total, 'extra' => $new_total - $paid, 'rate' => (float) $current['rate_per_term'],
            'terms' => 0, 'days' => $days, 'total' => 0.0, 'paid_for' => $paid);
        if ($paid < 1 || (float) $current['rate_per_term'] <= 0) {
            $out['error'] = 'Top-ups need a per-student subscription. Please contact us.';
        } elseif ($days < 1) {
            $out['error'] = 'This subscription has ended: please renew it instead.';
        } elseif ($new_total <= $paid) {
            $out['error'] = 'Enter more than the ' . number_format($paid) . ' students you have paid for.';
        } else {
            $out['terms'] = min(self::TERMS_PER_YEAR, max(1, (int) ceil($days / self::TERM_DAYS)));
            $out['total'] = round($out['extra'] * $out['rate'] * $out['terms'], 2);
            $out['ok'] = true;
        }
        return $out;
    }

    /** One line for invoices and the billing pages. */
    public static function describe(array $sub, $symbol = 'NGN ')
    {
        if (empty($sub['student_count'])) {
            return null;
        }
        $n = number_format((int) $sub['student_count']);
        $rate = $symbol . number_format((float) $sub['rate_per_term']);
        $terms = (int) $sub['terms'];
        $what = $terms . ' term' . ($terms === 1 ? '' : 's');
        if (isset($sub['kind']) && $sub['kind'] === 'topup') {
            return "EduSYS top-up to $n students at $rate per student per term, $what";
        }
        $line = "EduSYS subscription: $n students at $rate per student per term, $what";
        return (float) $sub['setup_fee'] > 0 ? $line . ', plus ' . $symbol . number_format((float) $sub['setup_fee']) . ' implementation and training' : $line;
    }
}
