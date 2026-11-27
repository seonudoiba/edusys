<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Every CBT has a subject (admin/onlineexam now requires one; "None (several subjects)" is gone).
 * A CBT that had none gets the subject most of its questions belong to, else its school's first
 * subject; always a subject of the CBT's own school. Then onlineexam.subject_id is NOT NULL.
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Onlineexam_subject_not_null extends CI_Migration
{
    public function up()
    {
        $this->db->without_tenant(function ($db) {
            $exams = $db->select('id, school_id')->group_start()->where('subject_id IS NULL', null, false)->or_where('subject_id', 0)->group_end()
                ->get('onlineexam')->result_array();
            foreach ($exams as $e) {
                $row = $db->select('q.subject_id, COUNT(*) AS n', false)->from('onlineexam_questions oq')
                    ->join('questions q', 'q.id = oq.question_id')
                    ->where(array('oq.onlineexam_id' => (int) $e['id'], 'q.school_id' => (int) $e['school_id']))->where('q.subject_id >', 0)
                    ->group_by('q.subject_id')->order_by('n', 'DESC')->limit(1)->get()->row_array();
                $subject = $row ? (int) $row['subject_id'] : 0;
                if (!$subject) {
                    $first = $db->select('id')->where('school_id', (int) $e['school_id'])->order_by('id')->limit(1)->get('subjects')->row_array();
                    $subject = $first ? (int) $first['id'] : 0;
                }
                $db->where('id', (int) $e['id'])->update('onlineexam', array('subject_id' => $subject));
            }
        });
        $this->db->query('UPDATE `onlineexam` SET `subject_id` = 0 WHERE `subject_id` IS NULL'); // tenancy-lint: ok schema change (a school without subjects)
        $this->db->query('ALTER TABLE `onlineexam` MODIFY `subject_id` INT(11) NOT NULL DEFAULT 0'); // tenancy-lint: ok schema change
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `onlineexam` MODIFY `subject_id` INT(11) NULL DEFAULT NULL'); // tenancy-lint: ok schema change
    }
}
