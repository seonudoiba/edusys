<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Manual (bank transfer) payments for subscriptions: table `subscription_bank_transfers`.
 *
 * Receipts are stored under uploads/subscription_receipts/ with random names. That folder
 * refuses direct web access (.htaccess written on first use); the platform admin downloads
 * receipts through admin/subscription_bank_transfers/receipt/<id>.
 */
class Subscription_bank_transfer_model extends CI_Model
{
    const TABLE = 'subscription_bank_transfers';
    const RECEIPT_DIR = 'uploads/subscription_receipts/';
    const MAX_RECEIPT_BYTES = 5242880; // 5 MB

    /** extension => MIME types accepted for it */
    private $receipt_types = array(
        'jpg' => array('image/jpeg'),
        'jpeg' => array('image/jpeg'),
        'png' => array('image/png'),
        'pdf' => array('application/pdf'),
    );

    /** Starts a bank transfer for a subscription (or returns the one it has). Returns its token. */
    public function create($subscription_id)
    {
        $existing = $this->db->where('subscription_id', (int) $subscription_id)->get(self::TABLE)->row_array();
        if ($existing) {
            return $existing['token'];
        }
        $token = bin2hex(random_bytes(20));
        $this->db->insert(self::TABLE, array(
            'subscription_id' => (int) $subscription_id,
            'token' => $token,
            'status' => 'awaiting_receipt',
        ));
        return $token;
    }

    public function get_by_token($token)
    {
        if (!preg_match('/^[0-9a-f]{40}$/', $token)) {
            return null;
        }
        return $this->db->where('token', $token)->get(self::TABLE)->row_array();
    }

    /** One transfer with its subscription and plan (admin review page). */
    public function get_detail($id)
    {
        return $this->db->select('t.*, s.school_name, s.owner_name, s.email_address, s.phone_number, s.total, s.price, s.discount_amt, s.payment_status, s.school_id AS registered_school_id, s.created_at AS subscribed_at, p.title AS plan_title, p.days AS plan_days')
            ->from(self::TABLE . ' t')
            ->join('subscription s', 's.id = t.subscription_id')
            ->join('plan p', 'p.plan_id = s.plan_id', 'left')
            ->where('t.id', (int) $id)
            ->get()->row_array();
    }

    /** Transfers for the admin list, newest first; $status = null for all. */
    public function get_list($status = null)
    {
        $this->db->select('t.id, t.status, t.amount, t.payment_date, t.reference, t.submitted_at, t.reviewed_at, s.school_name, s.owner_name, s.email_address, s.total, p.title AS plan_title')
            ->from(self::TABLE . ' t')
            ->join('subscription s', 's.id = t.subscription_id')
            ->join('plan p', 'p.plan_id = s.plan_id', 'left');
        if ($status !== null) {
            $this->db->where('t.status', $status);
        }
        return $this->db->order_by('t.submitted_at IS NULL', '', false)->order_by('t.submitted_at', 'desc')->order_by('t.id', 'desc')->get()->result_array();
    }

    public function count_pending()
    {
        return $this->db->where('status', 'pending')->count_all_results(self::TABLE);
    }

    /** Records the school's payment details and receipt; the transfer waits for review. */
    public function submit($id, array $data)
    {
        $old = $this->db->select('receipt_file')->where('id', (int) $id)->get(self::TABLE)->row_array();
        $this->db->where('id', (int) $id)->update(self::TABLE, array(
            'payer_name' => $data['payer_name'],
            'payer_bank' => $data['payer_bank'],
            'payment_date' => $data['payment_date'],
            'amount' => $data['amount'],
            'reference' => $data['reference'] !== '' ? $data['reference'] : null,
            'receipt_file' => $data['receipt_file'],
            'receipt_name' => $data['receipt_name'],
            'status' => 'pending',
            'submitted_at' => date('Y-m-d H:i:s'),
            'reviewed_at' => null,
            'reviewed_by' => null,
            'review_note' => null,
        ));
        if ($old && $old['receipt_file'] && $old['receipt_file'] !== $data['receipt_file']) {
            $this->delete_receipt_file($old['receipt_file']);
        }
    }

    /**
     * Marks a pending transfer approved or rejected. Returns false when it isn't pending any more
     * (another admin reviewed it first), so it is never approved twice.
     */
    public function review($id, $status, $staff_id, $note = null)
    {
        $this->db->where(array('id' => (int) $id, 'status' => 'pending'))->update(self::TABLE, array(
            'status' => $status,
            'reviewed_at' => date('Y-m-d H:i:s'),
            'reviewed_by' => (int) $staff_id,
            'review_note' => $note,
        ));
        return $this->db->affected_rows() === 1;
    }

    /** Why the uploaded receipt can't be accepted, or null when it can. */
    public function receipt_upload_error($field)
    {
        if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
            return 'Upload the payment receipt.';
        }
        $file = $_FILES[$field];
        if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
            return 'The receipt could not be uploaded. Please try again.';
        }
        if ($file['size'] <= 0 || $file['size'] > self::MAX_RECEIPT_BYTES) {
            return 'The receipt must be smaller than 5 MB.';
        }
        $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!isset($this->receipt_types[$ext]) || !in_array($mime, $this->receipt_types[$ext], true)) {
            return 'The receipt must be a JPG, PNG or PDF file.';
        }
        return null;
    }

    /** Saves the uploaded receipt under a random name. Returns ['file', 'name'] or null. */
    public function store_receipt($field)
    {
        $dir = $this->receipt_dir();
        if ($dir === null) {
            return null;
        }
        $ext = strtolower(pathinfo((string) $_FILES[$field]['name'], PATHINFO_EXTENSION));
        $ext = $ext === 'jpeg' ? 'jpg' : $ext;
        $file = bin2hex(random_bytes(16)) . '.' . $ext;
        if (!move_uploaded_file($_FILES[$field]['tmp_name'], $dir . $file)) {
            return null;
        }
        $name = preg_replace('/[^\w.\- ]+/u', '_', basename((string) $_FILES[$field]['name']));
        return array('file' => $file, 'name' => mb_substr($name, 0, 255));
    }

    /** Absolute path of a stored receipt, or null when it is missing. */
    public function receipt_path($file)
    {
        if (!preg_match('/^[0-9a-f]{32}\.(jpg|png|pdf)$/', (string) $file)) {
            return null;
        }
        $path = upload_abs_root() . 'subscription_receipts/' . $file;
        return is_file($path) ? $path : null;
    }

    public function receipt_mime($file)
    {
        $ext = pathinfo((string) $file, PATHINFO_EXTENSION);
        return $ext === 'pdf' ? 'application/pdf' : ($ext === 'png' ? 'image/png' : 'image/jpeg');
    }

    private function delete_receipt_file($file)
    {
        $path = $this->receipt_path($file);
        if ($path !== null) {
            @unlink($path);
        }
    }

    /** The receipts folder, created on first use with direct web access refused. */
    private function receipt_dir()
    {
        $dir = upload_abs_root() . 'subscription_receipts/';
        if (!is_dir($dir) && !@mkdir($dir, 0775, true)) {
            log_message('error', 'Cannot create ' . $dir);
            return null;
        }
        if (!is_file($dir . '.htaccess')) {
            file_put_contents($dir . '.htaccess', "# Receipts are served only through admin/subscription_bank_transfers/receipt.\nRequire all denied\n");
        }
        if (!is_file($dir . 'index.html')) {
            file_put_contents($dir . 'index.html', '');
        }
        return $dir;
    }
}
