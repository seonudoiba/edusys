<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Online exams' "Auto Result Publish Date": once it has passed, the exam's results are published
 * (publish_result = 1), as if the teacher had ticked Publish Result, so every page (the students'
 * exam lists and result pages, the staff list, rank generation, report-card scores) sees it.
 *
 * publish_due() is cheap and runs when the exam pages open; run() (the 5-minute cron, Cron::
 * student_attendance) also sends the "result published" email/SMS once per exam, like a manual
 * publish does. Quizzes show their result at once and have no date.
 */
class Exam_auto_publish
{
    /** @var CI_Controller */
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    /** Publishes the results of the exams whose date has passed. Returns how many. */
    public function publish_due()
    {
        $db = $this->CI->db;
        $this->due($db)->where('publish_result', 0)->update('onlineexam', array('publish_result' => 1));
        return (int) $db->affected_rows();
    }

    /** publish_due(), then the result notification for each exam published by its date. */
    public function run()
    {
        $this->publish_due();
        $db = $this->CI->db;
        // Only dates passed in the last 2 days: exams published by their date before this ran
        // (or long ago) get no late message.
        $exams = $this->due($db)->where('auto_publish_date >=', date('Y-m-d H:i:s', time() - 2 * 86400))
            ->where('publish_result', 1)->where('publish_result_notification', 0)->get('onlineexam')->result_array();
        if (!$exams) {
            return 0;
        }
        $this->CI->load->library('mailsmsconf');
        $sent = 0;
        foreach ($exams as $exam) {
            // Claimed first, so two runs at once don't send it twice.
            $db->where(array('id' => (int) $exam['id'], 'publish_result_notification' => 0))->update('onlineexam', array('publish_result_notification' => 1));
            if ($db->affected_rows() !== 1) {
                continue;
            }
            $this->CI->mailsmsconf->mailsms('online_examination_publish_result', array(
                'exam_id' => (int) $exam['id'],
                'exam_title' => $exam['exam'],
                'attempt' => $exam['attempt'],
                'time_duration' => $exam['duration'],
                'passing_percentage' => $exam['passing_percentage'],
                'exam_from' => $exam['exam_from'],
                'exam_to' => $exam['exam_to'],
            ));
            $sent++;
        }
        return $sent;
    }

    /** Not quizzes, with a real date (old rows may hold 0000-00-00) that has passed. */
    private function due($db)
    {
        return $db->where('is_quiz', 0)
            ->where('auto_publish_date >', '2000-01-01 00:00:00')
            ->where('auto_publish_date <=', date('Y-m-d H:i:s'));
    }
}
