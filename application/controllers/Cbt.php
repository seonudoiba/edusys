<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The exam link a teacher shares (cbt/<exam id>): the student signs in and lands on the exam
 * screen (user/onlineexam/take) instead of the dashboard. A student already signed in goes
 * straight there; a parent signed in on the same browser is signed out (only the student sits the
 * exam). Whether the exam is assigned to the student and open is checked by the exam screen.
 */
class Cbt extends Public_Controller
{
    public function index($id = 0)
    {
        $id = (int) $id;
        $exam = $id > 0 ? $this->db->select('id, exam')->where('id', $id)->get('onlineexam')->row_array() : null;
        if (!$exam) {
            $this->session->set_flashdata('disable_message', 'This exam link is not valid. Ask your teacher for the right link.');
            redirect('site/login');
        }
        $target = 'user/onlineexam/take/' . $id;
        // Used after sign-in (and after choosing a class or a new password): User::after_choose.
        $_SESSION['redirect_to_user'] = site_url($target);

        $user = $this->session->userdata('student');
        if ($user && isset($user['role']) && $user['role'] === 'student') {
            redirect($target);
        }
        if ($user) {
            $this->session->unset_userdata(array('student', 'current_class'));
        }
        $this->session->set_userdata('exam_link', array('id' => $id, 'title' => (string) $exam['exam']));
        redirect('site/login');
    }
}
