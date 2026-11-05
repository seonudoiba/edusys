<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Discount codes for sign-ups (platform_coupons): lookup, validity and the discount they give.
 */
class Platform_coupons
{
    /** @var CI_Controller */
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->database();
    }

    public static function normalize($code)
    {
        return strtoupper(preg_replace('/\s+/', '', (string) $code));
    }

    public function find($code)
    {
        $code = self::normalize($code);
        return $code === '' ? null : $this->CI->db->where('code', $code)->get('platform_coupons')->row_array();
    }

    /** Why the coupon can't be used for this plan, or null when it can. */
    public function error($coupon, array $plan)
    {
        if (!$coupon || !(int) $coupon['is_active']) {
            return 'This coupon code is not valid.';
        }
        $today = date('Y-m-d');
        if ($coupon['starts_on'] && $today < $coupon['starts_on']) {
            return 'This coupon can be used from ' . date('d M Y', strtotime($coupon['starts_on'])) . '.';
        }
        if ($coupon['ends_on'] && $today > $coupon['ends_on']) {
            return 'This coupon has expired.';
        }
        if ((int) $coupon['max_uses'] > 0 && (int) $coupon['used_count'] >= (int) $coupon['max_uses']) {
            return 'This coupon has been used up.';
        }
        $plans = json_decode((string) $coupon['plan_ids'], true);
        if (is_array($plans) && !in_array((int) $plan['plan_id'], array_map('intval', $plans), true)) {
            return 'This coupon is not valid for the ' . $plan['title'] . '.';
        }
        return null;
    }

    /** The discount on $amount (never more than $amount). */
    public function discount(array $coupon, $amount)
    {
        $amount = (float) $amount;
        $off = $coupon['discount_type'] === 'percent'
            ? round($amount * min(100, (float) $coupon['value']) / 100, 2)
            : (float) $coupon['value'];
        return max(0.0, min($amount, $off));
    }
}
