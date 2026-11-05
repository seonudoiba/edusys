<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Support tickets between schools and the platform team (platform_support_tickets and
 * platform_support_messages; platform tables, so every query names the school explicitly).
 *
 *   status  open      waiting for the platform team
 *           answered  the platform replied; waiting for the school
 *           closed    done (either side can reopen it by replying)
 *
 * Schools use admin/support (Help & Support), the platform team admin/support_desk.
 */
class Support_tickets
{
    const CATEGORIES = array('general' => 'General question', 'billing' => 'Billing and subscription', 'technical' => 'Technical problem', 'feature' => 'Feature request', 'account' => 'Account and access');
    const PRIORITIES = array('low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent');
    const STATUSES = array('open' => array('Open', 'label-warning'), 'answered' => array('Answered', 'label-info'), 'closed' => array('Closed', 'label-default'));

    /** @var CI_Controller */
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->database();
        $this->CI->load->library(array('Platform_mailer', 'Platform_settings', 'School_provisioner'));
    }

    /** @return int the new ticket id */
    public function open($school_id, array $staff, $subject, $category, $priority, $message)
    {
        $db = $this->CI->db;
        $now = date('Y-m-d H:i:s');
        $db->trans_start();
        $db->insert('platform_support_tickets', array(
            'school_ref' => (int) $school_id,
            'subject' => $subject,
            'category' => isset(self::CATEGORIES[$category]) ? $category : 'general',
            'priority' => isset(self::PRIORITIES[$priority]) ? $priority : 'normal',
            'status' => 'open',
            'opened_by_staff' => (int) $staff['id'],
            'opened_by_name' => (string) $staff['name'],
            'opened_by_email' => (string) $staff['email'],
            'last_reply_at' => $now,
        ));
        $id = (int) $db->insert_id();
        $db->insert('platform_support_messages', array('ticket_id' => $id, 'author' => 'school', 'author_name' => (string) $staff['name'], 'message' => $message));
        $db->trans_complete();
        $this->notify_platform($this->ticket($id), (string) $staff['name'], $message);
        return $id;
    }

    /** A reply from the school (reopens a closed or answered ticket). */
    public function school_reply(array $ticket, $name, $message)
    {
        $this->add($ticket, 'school', $name, $message, 'open');
        $this->notify_platform($ticket, $name, $message);
    }

    /** A reply from the platform team; emails the person who opened the ticket. */
    public function platform_reply(array $ticket, $name, $message, $close = false)
    {
        $this->add($ticket, 'platform', $name, $message, $close ? 'closed' : 'answered');
        $school = $this->school($ticket['school_ref']);
        if ($school && $ticket['opened_by_email'] !== '') {
            $this->CI->platform_mailer->send('support_reply', $ticket['opened_by_email'], array(
                'name' => $ticket['opened_by_name'],
                'ticket_id' => $ticket['id'],
                'subject' => $ticket['subject'],
                'message' => $message,
                'ticket_url' => $this->CI->school_provisioner->url_for($school) . 'admin/support/view/' . (int) $ticket['id'],
            ), (int) $ticket['school_ref']);
        }
    }

    public function set_status(array $ticket, $status)
    {
        if (isset(self::STATUSES[$status])) {
            $this->CI->db->where('id', (int) $ticket['id'])->update('platform_support_tickets', array('status' => $status));
        }
    }

    /** A ticket, limited to one school when $school_id is given. */
    public function ticket($id, $school_id = null)
    {
        $db = $this->CI->db->where('t.id', (int) $id);
        if ($school_id !== null) {
            $db->where('t.school_ref', (int) $school_id);
        }
        return $db->select('t.*, s.school_name, s.subdomain')->from('platform_support_tickets t')
            ->join('school s', 's.id = t.school_ref', 'left')->get()->row_array();
    }

    public function messages($ticket_id)
    {
        return $this->CI->db->where('ticket_id', (int) $ticket_id)->order_by('id')->get('platform_support_messages')->result_array();
    }

    /** Tickets with filters: school_ref, status, q (subject or school name). */
    public function search(array $f, $limit = 200)
    {
        $db = $this->CI->db->select('t.*, s.school_name, s.subdomain, (SELECT COUNT(*) FROM platform_support_messages m WHERE m.ticket_id = t.id) AS message_count', false)
            ->from('platform_support_tickets t')->join('school s', 's.id = t.school_ref', 'left');
        if (!empty($f['school_ref'])) {
            $db->where('t.school_ref', (int) $f['school_ref']);
        }
        if (!empty($f['status']) && isset(self::STATUSES[$f['status']])) {
            $db->where('t.status', $f['status']);
        }
        if (!empty($f['q'])) {
            $db->group_start()->like('t.subject', $f['q'])->or_like('s.school_name', $f['q'])->group_end();
        }
        return $db->order_by("FIELD(t.status, 'open', 'answered', 'closed')", '', false)
            ->order_by('t.last_reply_at', 'DESC')->limit($limit)->get()->result_array();
    }

    /** Ticket counts per status (for one school when given). */
    public function counts($school_id = null)
    {
        $db = $this->CI->db->select('status, COUNT(*) AS n')->group_by('status');
        if ($school_id !== null) {
            $db->where('school_ref', (int) $school_id);
        }
        $out = array_fill_keys(array_keys(self::STATUSES), 0);
        foreach ($db->get('platform_support_tickets')->result_array() as $r) {
            $out[$r['status']] = (int) $r['n'];
        }
        return $out;
    }

    private function add(array $ticket, $author, $name, $message, $status)
    {
        $db = $this->CI->db;
        $db->trans_start();
        $db->insert('platform_support_messages', array('ticket_id' => (int) $ticket['id'], 'author' => $author, 'author_name' => (string) $name, 'message' => $message));
        $db->where('id', (int) $ticket['id'])->update('platform_support_tickets', array('status' => $status, 'last_reply_at' => date('Y-m-d H:i:s')));
        $db->trans_complete();
    }

    private function notify_platform(array $ticket, $name, $message)
    {
        $to = $this->CI->platform_settings->get('notify_email');
        $this->CI->platform_mailer->send('support_new_message', $to, array(
            'ticket_id' => $ticket['id'],
            'subject' => $ticket['subject'],
            'school_name' => $ticket['school_name'],
            'author_name' => $name,
            'message' => $message,
            'ticket_url' => $this->CI->school_provisioner->url_for(array('is_platform' => 1)) . 'admin/support_desk/view/' . (int) $ticket['id'],
        ), (int) $ticket['school_ref']);
    }

    private function school($id)
    {
        return $this->CI->db->select('id, school_name, subdomain, is_platform')->where('id', (int) $id)->get('school')->row_array();
    }
}
