<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admission form fees paid by bank transfer: table `admission_bank_transfers`.
 *
 * The applicant uploads the receipt from their application page; the school approves or rejects it
 * on the application (admin/onlinestudent/edit). Approving marks the application paid once.
 * Receipts are stored under the school's uploads/s<id>/admission_receipts/ with random names; that
 * folder refuses direct web access, staff open receipts through admin/onlinestudent/transfer_receipt.
 */
class Admission_bank_transfer_model extends CI_Model
{
    const TABLE = 'admission_bank_transfers';
    const DIR = 'admission_receipts/';
    const MAX_RECEIPT_BYTES = 5242880; // 5 MB

    private $receipt_types = array(
        'jpg' => array('image/jpeg'),
        'jpeg' => array('image/jpeg'),
        'png' => array('image/png'),
        'pdf' => array('application/pdf'),
    );

    /** The application's latest transfer, or null. */
    public function latest($admission_id)
    {
        return $this->db->where('online_admission_id', (int) $admission_id)->order_by('id', 'desc')->limit(1)->get(self::TABLE)->row_array() ?: null;
    }

    public function get($id)
    {
        return $this->db->where('id', (int) $id)->get(self::TABLE)->row_array() ?: null;
    }

    /** Transfers waiting for the school, with the applicant (applications list). */
    public function pending()
    {
        return $this->db->select('t.id, t.amount, t.currency, t.payer_name, t.payment_date, t.created_at, a.id AS admission_id, a.reference_no, a.firstname, a.lastname')
            ->from(self::TABLE . ' t')->join('online_admissions a', 'a.id = t.online_admission_id')
            ->where('t.status', 'pending')->order_by('t.created_at')->get()->result_array();
    }

    /** Records a transfer and marks the application's payment as being confirmed. */
    public function add($admission_id, array $data)
    {
        $this->db->insert(self::TABLE, array(
            'online_admission_id' => (int) $admission_id,
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'payer_name' => $data['payer_name'],
            'payer_bank' => $data['payer_bank'],
            'payment_date' => $data['payment_date'],
            'reference' => $data['reference'] !== '' ? $data['reference'] : null,
            'receipt_file' => $data['receipt_file'],
            'receipt_name' => $data['receipt_name'],
            'status' => 'pending',
        ));
        $this->db->where('id', (int) $admission_id)->update('online_admissions', array('paid_status' => 2));
    }

    /**
     * Approves or rejects a pending transfer. Returns false when it isn't pending any more (someone
     * reviewed it first), so it's never approved twice.
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
        $path = upload_abs_root() . self::DIR . $file;
        return is_file($path) ? $path : null;
    }

    public function receipt_mime($file)
    {
        $ext = pathinfo((string) $file, PATHINFO_EXTENSION);
        return $ext === 'pdf' ? 'application/pdf' : ($ext === 'png' ? 'image/png' : 'image/jpeg');
    }

    /** The receipts folder, created on first use with direct web access refused. */
    private function receipt_dir()
    {
        $dir = upload_abs_root() . self::DIR;
        if (!is_dir($dir) && !@mkdir($dir, 0775, true)) {
            log_message('error', 'Cannot create ' . $dir);
            return null;
        }
        if (!is_file($dir . '.htaccess')) {
            file_put_contents($dir . '.htaccess', "# Receipts are served only through admin/onlinestudent/transfer_receipt.\nRequire all denied\n");
        }
        if (!is_file($dir . 'index.html')) {
            file_put_contents($dir . 'index.html', '');
        }
        return $dir;
    }
}
