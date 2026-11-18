<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Invoices before payment: `platform_invoices` gets a status (unpaid, paid, void), a due date, the
 * date it was paid, the bill-to phone and address, itemised lines (JSON) and a note. Invoices
 * issued so far were receipts of payments: they are paid. Adds the `invoice_issued` email (the
 * invoice sent to the owner, with the link to view, print and pay it).
 *
 * Hand-written migration.
 */
class Migration_Add_unpaid_invoices extends CI_Migration
{
    public function up()
    {
        $cols = array_column($this->db->query('SHOW COLUMNS FROM `platform_invoices`')->result_array(), 'Field');
        $add = array(
            'status' => "varchar(10) NOT NULL DEFAULT 'paid' AFTER `token`",
            'due_date' => 'date DEFAULT NULL AFTER `status`',
            'paid_at' => 'datetime DEFAULT NULL AFTER `due_date`',
            'bill_to_phone' => 'varchar(60) DEFAULT NULL AFTER `bill_to_email`',
            'bill_to_address' => 'varchar(500) DEFAULT NULL AFTER `bill_to_phone`',
            'lines' => 'text DEFAULT NULL AFTER `description`',
            'notes' => 'text DEFAULT NULL AFTER `lines`',
        );
        foreach ($add as $col => $def) {
            if (!in_array($col, $cols, true)) {
                $this->db->query("ALTER TABLE `platform_invoices` ADD COLUMN `$col` $def");
            }
        }
        $this->db->query("UPDATE `platform_invoices` SET `paid_at` = `issued_at` WHERE `status` = 'paid' AND `paid_at` IS NULL");

        if ($this->db->where('template_key', 'invoice_issued')->count_all_results('platform_email_templates') === 0) {
            $this->db->insert('platform_email_templates', array(
                'template_key' => 'invoice_issued',
                'name' => 'Invoice (to the owner, before payment)',
                'recipient' => 'owner',
                'subject' => 'Invoice {invoice_no}: {school_name}',
                'body' => "<p>Dear {owner_name},</p>\n<p>Please find invoice <b>{invoice_no}</b> for <b>{school_name}</b>: <b>{amount}</b>, due on {due_date}.</p>\n"
                    . "<p><a href=\"{invoice_url}\">View, print or pay the invoice</a> (a PDF can be downloaded from the same page).</p>\n"
                    . "<p>Questions? Write to {support_email} or call {support_phone}.</p>\n<p>The {platform_name} team</p>",
                'placeholders' => '{owner_name}, {school_name}, {amount}, {invoice_no}, {due_date}, {invoice_url}, {platform_name}, {support_email}, {support_phone}',
                'is_active' => 1,
            ));
        }
    }

    public function down()
    {
        $this->db->where('template_key', 'invoice_issued')->delete('platform_email_templates');
        $this->db->where('status <>', 'paid')->delete('platform_invoices');
        foreach (array('status', 'due_date', 'paid_at', 'bill_to_phone', 'bill_to_address', 'lines', 'notes') as $col) {
            $this->db->query("ALTER TABLE `platform_invoices` DROP COLUMN `$col`");
        }
    }
}
