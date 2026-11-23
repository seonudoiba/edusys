<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Online Classes in the student and parent portal: the class's Google Meet and Zoom classes in one
 * place, "Live now" first with a Join button, then what is coming up and what has ended. Students
 * join (the visit is recorded as before); parents see the timetable.
 */
class Onlineclasses extends Student_Controller
{
    const EARLY = 600;   // the Join button opens 10 minutes before the start
    const GRACE = 900;   // and stays 15 minutes after the planned end

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('gmeet_model', 'gmeethistory_model', 'conference_model', 'conferencehistory_model'));
    }

    public function index()
    {
        $this->session->set_userdata('top_menu', 'onlineclasses');
        $all = $this->classes();
        $live = $upcoming = $past = array();
        foreach ($all as $c) {
            if ($c['state'] === 'live') {
                $live[] = $c;
            } elseif ($c['state'] === 'upcoming') {
                $upcoming[] = $c;
            } else {
                $past[] = $c;
            }
        }
        usort($live, function ($a, $b) { return $a['start'] <=> $b['start']; });
        usort($upcoming, function ($a, $b) { return $a['start'] <=> $b['start']; });
        usort($past, function ($a, $b) { return $b['start'] <=> $a['start']; });
        $data = array(
            'live' => $live,
            'upcoming' => $upcoming,
            'past' => array_slice($past, 0, 30),
            'is_parent' => $this->customlib->getUserRole() === 'parent',
            'msg' => $this->session->flashdata('oc_msg'),
        );
        $this->load->view('layout/student/header', $data);
        $this->load->view('user/onlineclasses/index', $data);
        $this->load->view('layout/student/footer', $data);
    }

    /** Opens a live class (students only): records the visit, then goes to the meeting. */
    public function join($kind = '', $id = 0)
    {
        if ($this->customlib->getUserRole() === 'parent') {
            redirect('user/onlineclasses');
        }
        foreach ($this->classes() as $c) {
            if ($c['kind'] === $kind && $c['id'] === (int) $id) {
                if ($c['state'] !== 'live' || $c['url'] === '') {
                    $this->session->set_flashdata('oc_msg', $c['state'] === 'upcoming' ? 'This class has not started yet. Join opens 10 minutes before the start.' : 'This class is not open.');
                    redirect('user/onlineclasses');
                }
                $student_id = $this->customlib->getStudentSessionUserID();
                if ($kind === 'gmeet') {
                    $this->gmeethistory_model->updatehistory(array('gmeet_id' => (int) $id, 'student_id' => $student_id), 'student');
                } else {
                    $this->conferencehistory_model->updatehistory(array('conference_id' => (int) $id, 'student_id' => $student_id), 'student');
                }
                redirect($c['url']);
            }
        }
        show_404();
    }

    /** The class's online classes of this session, from the live class modules that are switched on. */
    private function classes()
    {
        $cs = $this->customlib->getStudentCurrentClsSection();
        if (empty($cs->class_id)) {
            return array();
        }
        $rows = array();
        if ($this->module_lib->hasActive('gmeet_live_classes')) {
            foreach ($this->gmeet_model->getByClassSection($cs->class_id, $cs->section_id) as $g) {
                $rows[] = $this->row('gmeet', $g, (string) $g->url);
            }
        }
        if ($this->module_lib->hasActive('zoom_live_classes')) {
            foreach ($this->conference_model->getByClassSection($cs->class_id, $cs->section_id) as $z) {
                $resp = json_decode((string) $z->return_response);
                $rows[] = $this->row('zoom', $z, $resp && !empty($resp->join_url) ? (string) $resp->join_url : '');
            }
        }
        return $rows;
    }

    private function row($kind, $r, $url)
    {
        $start = strtotime($r->date);
        $minutes = max(1, (int) $r->duration);
        $end = $start + $minutes * 60;
        $now = time();
        if ((int) $r->status === 1) {
            $state = 'cancelled';
        } elseif ((int) $r->status === 2 || $now > $end + self::GRACE) {
            $state = 'ended';
        } elseif ($now >= $start - self::EARLY) {
            $state = 'live';
        } else {
            $state = 'upcoming';
        }
        return array(
            'kind' => $kind,
            'id' => (int) $r->id,
            'title' => (string) $r->title,
            'subject' => isset($r->subject) ? (string) $r->subject : '',
            'description' => (string) $r->description,
            'teacher' => trim($r->create_for_name . ' ' . $r->create_for_surname),
            'start' => $start,
            'minutes' => $minutes,
            'state' => $state,
            'url' => $url,
        );
    }
}
