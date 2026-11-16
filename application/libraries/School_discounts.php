<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * A discount the platform gives one school (Schools > a school > Subscription), applied to what the
 * school's owner pays from their account: the subscription after the setup fee, renewals and
 * top-ups (controllers/Subscription.php edit() and topup()).
 *
 *   type     percent (of the payment) or amount (naira off, never below zero)
 *   applies  once (the next payment, then it is used up) or always (every payment until removed)
 *
 * A school has at most one active discount; adding one replaces the previous. A "once" discount is
 * used up when the payment it was applied to is confirmed (Subscription_activation).
 * Platform table: platform_school_discounts (migration 299).
 */
class School_discounts
{
    const TABLE = 'platform_school_discounts';

    /** @var CI_Controller */
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    /** The school's active discount, or null. */
    public function active($school_id)
    {
        return $this->CI->db->where(array('school_id' => (int) $school_id, 'is_active' => 1))->order_by('id', 'desc')->limit(1)->get(self::TABLE)->row_array() ?: null;
    }

    /** Every discount the school has had, newest first. */
    public function history($school_id)
    {
        return $this->CI->db->where('school_id', (int) $school_id)->order_by('id', 'desc')->get(self::TABLE)->result_array();
    }

    /** Naira off $total. */
    public static function amount(?array $discount = null, $total = 0.0)
    {
        if (!$discount || $total <= 0) {
            return 0.0;
        }
        $off = $discount['type'] === 'percent' ? $total * min(100, (float) $discount['value']) / 100 : (float) $discount['value'];
        return round(min($total, max(0, $off)), 2);
    }

    /** "10% off the next payment", "NGN 50,000 off every payment". */
    public static function describe(array $discount)
    {
        $what = $discount['type'] === 'percent' ? rtrim(rtrim(number_format((float) $discount['value'], 2), '0'), '.') . '%' : '₦' . number_format((float) $discount['value']);
        return $what . ' off ' . ($discount['applies'] === 'always' ? 'every payment' : 'the next payment');
    }

    /**
     * Gives the school a discount (replacing an active one).
     * @throws InvalidArgumentException with a message for the admin
     */
    public function add($school_id, $type, $value, $applies, $note, $staff_id)
    {
        $value = round((float) $value, 2);
        if (!in_array($type, array('percent', 'amount'), true)) {
            throw new InvalidArgumentException('Choose a percentage or an amount.');
        }
        if ($value <= 0 || ($type === 'percent' && $value > 100)) {
            throw new InvalidArgumentException($type === 'percent' ? 'Enter a percentage between 0 and 100.' : 'Enter an amount above zero.');
        }
        $applies = $applies === 'always' ? 'always' : 'once';
        $db = $this->CI->db;
        $db->where(array('school_id' => (int) $school_id, 'is_active' => 1))->update(self::TABLE, array('is_active' => 0));
        $db->insert(self::TABLE, array(
            'school_id' => (int) $school_id, 'type' => $type, 'value' => $value, 'applies' => $applies,
            'note' => mb_substr(trim(strip_tags((string) $note)), 0, 255) ?: null, 'is_active' => 1, 'created_by' => (int) $staff_id ?: null,
        ));
        return (int) $db->insert_id();
    }

    /** Stops the school's active discount. */
    public function remove($school_id)
    {
        $this->CI->db->where(array('school_id' => (int) $school_id, 'is_active' => 1))->update(self::TABLE, array('is_active' => 0));
        return $this->CI->db->affected_rows() > 0;
    }

    /** After a payment with the discount is confirmed: a "once" discount is used up. */
    public function used($discount_id, $subscription_id)
    {
        $db = $this->CI->db;
        $row = $db->where('id', (int) $discount_id)->get(self::TABLE)->row_array();
        if (!$row) {
            return;
        }
        $set = array('used_at' => date('Y-m-d H:i:s'), 'used_subscription_id' => (int) $subscription_id);
        if ($row['applies'] === 'once') {
            $set['is_active'] = 0;
        }
        $db->where('id', (int) $discount_id)->update(self::TABLE, $set);
    }
}
