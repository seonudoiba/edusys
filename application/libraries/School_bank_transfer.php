<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * A school's bank details for manual payments (System Settings > Payment Methods > Bank transfer),
 * kept as JSON in sch_settings.bank_transfer:
 *   bank_name, account_name, account_number, note   shown to payers
 *   admission  0|1   applicants can pay the admission form fee by transfer (receipt, school approves)
 * School fees by transfer are the existing Offline Bank Payments (sch_settings.is_offline_fee_payment).
 */
class School_bank_transfer
{
    private $CI;
    private $row;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    private function row()
    {
        if ($this->row === null) {
            $this->row = $this->CI->db->select('id, bank_transfer, is_offline_fee_payment')->order_by('id')->limit(1)->get('sch_settings')->row_array() ?: array();
        }
        return $this->row;
    }

    /** The details, always with every key. */
    public function details()
    {
        $row = $this->row();
        $d = json_decode(isset($row['bank_transfer']) ? (string) $row['bank_transfer'] : '', true);
        $d = is_array($d) ? $d : array();
        $out = array();
        foreach (array('bank_name' => 120, 'account_name' => 160, 'account_number' => 40, 'note' => 1000) as $key => $max) {
            $out[$key] = isset($d[$key]) ? mb_substr(trim(strip_tags((string) $d[$key])), 0, $max) : '';
        }
        $out['admission'] = !empty($d['admission']) ? 1 : 0;
        $out['school_fees'] = !empty($row['is_offline_fee_payment']) ? 1 : 0;
        return $out;
    }

    /** True when the bank details are complete. */
    public function complete()
    {
        $d = $this->details();
        return $d['bank_name'] !== '' && $d['account_name'] !== '' && $d['account_number'] !== '';
    }

    /** Applicants can pay the admission form fee by transfer. */
    public function for_admission()
    {
        $d = $this->details();
        return $d['admission'] && $this->complete();
    }

    public function save(array $d)
    {
        $row = $this->row();
        $keep = array();
        foreach (array('bank_name', 'account_name', 'account_number', 'note') as $key) {
            $keep[$key] = trim(strip_tags((string) (isset($d[$key]) ? $d[$key] : '')));
        }
        $keep['admission'] = !empty($d['admission']) ? 1 : 0;
        $this->CI->db->where('id', $row['id'])->update('sch_settings', array(
            'bank_transfer' => json_encode($keep),
            'is_offline_fee_payment' => !empty($d['school_fees']) ? 1 : 0,
        ));
        $this->row = null;
    }
}
