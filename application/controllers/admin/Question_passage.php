<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Reading passages for the question bank (comprehension): one passage, several questions.
 * A question picks its passage on the question form (questions.passage_id); students see the
 * passage above each of those questions, and the questions stay together when shuffled.
 */
class Question_passage extends Admin_Controller
{
    public function index($id = null)
    {
        if (!$this->rbac->hasPrivilege('question_bank', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Online_Examinations');
        $this->session->set_userdata('sub_menu', 'Online_Examinations/question');
        $data['passages'] = $this->db->select('p.id, p.title, p.updated_at, p.created_at, s.name AS subject, (SELECT COUNT(*) FROM questions q WHERE q.passage_id = p.id) AS questions', false)
            ->from('question_passages p')->join('subjects s', 's.id = p.subject_id', 'left')
            ->order_by('p.title')->get()->result_array();
        $data['subjects'] = $this->db->select('id, name, code')->order_by('name')->get('subjects')->result_array();
        $data['edit'] = $id ? $this->db->where('id', (int) $id)->get('question_passages')->row_array() : null;
        $data['can_add'] = $this->rbac->hasPrivilege('question_bank', 'can_add');
        $data['can_edit'] = $this->rbac->hasPrivilege('question_bank', 'can_edit');
        $data['can_delete'] = $this->rbac->hasPrivilege('question_bank', 'can_delete');
        $this->load->view('layout/header', $data);
        $this->load->view('admin/question/passages', $data);
        $this->load->view('layout/footer', $data);
    }

    public function save()
    {
        $id = (int) $this->input->post('id');
        if ($this->input->method() !== 'post' || !$this->rbac->hasPrivilege('question_bank', $id ? 'can_edit' : 'can_add')) {
            access_denied();
        }
        $title = trim(strip_tags((string) $this->input->post('title')));
        $body = trim((string) $this->input->post('body'));
        if ($title === '' || trim(strip_tags($body, '<img><math>')) === '') {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Give the passage a title and its text.</div>');
            redirect('admin/question_passage/index' . ($id ? '/' . $id : ''));
        }
        $row = array('title' => mb_substr($title, 0, 255), 'body' => $body, 'subject_id' => (int) $this->input->post('subject_id') ?: null);
        if ($id) {
            $row['updated_at'] = date('Y-m-d H:i:s');
            $this->db->where('id', $id)->update('question_passages', $row);
        } else {
            $this->db->insert('question_passages', $row);
        }
        $this->session->set_flashdata('msg', '<div class="alert alert-success">Passage saved. Choose it on each question that belongs to it.</div>');
        redirect('admin/question_passage');
    }

    /** POST: the questions keep their text and lose only the link to the passage. */
    public function delete($id = null)
    {
        if ($this->input->method() !== 'post' || !$this->rbac->hasPrivilege('question_bank', 'can_delete')) {
            access_denied();
        }
        $this->db->where('passage_id', (int) $id)->update('questions', array('passage_id' => null));
        $this->db->where('id', (int) $id)->delete('question_passages');
        $this->session->set_flashdata('msg', '<div class="alert alert-success">Passage deleted.</div>');
        redirect('admin/question_passage');
    }
}
