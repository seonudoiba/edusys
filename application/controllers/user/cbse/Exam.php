<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Exam extends Student_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('cbseexam/cbseexam_exam_model', 'cbseexam/cbseexam_assessment_model', 'cbseexam/cbseexam_grade_model'));

    }

    public function timetable()
    {
		$this->session->set_userdata('top_menu', 'cbse_exam');
		$this->session->set_userdata('sub_menu', 'user/cbse/cbse_exam_timetable');
        $data = [];
        $student_current_class = $this->customlib->getStudentCurrentClsSection();
        $data['exams'] = $this->cbseexam_exam_model->getStudentExamTimetable($student_current_class->student_session_id);
        $this->load->view('layout/student/header', $data);
        $this->load->view('user/cbse/timetable', $data);
        $this->load->view('layout/student/footer', $data);
    }

    /** The student's report cards (published), every term and session: view or download each. */
    public function reportcards()
    {
        $this->session->set_userdata('top_menu', 'cbse_exam');
        $this->session->set_userdata('sub_menu', 'user/cbse/cbse_exam_result');
        $data = array('cards' => array());
        $current = $this->customlib->getStudentCurrentClsSection();
        $student_id = !empty($current->student_session_id) ? (int) $this->db->select('student_id')->where('id', (int) $current->student_session_id)->get('student_session')->row('student_id') : 0;
        if ($student_id) {
            $data['cards'] = $this->db->select('es.id AS es, e.id AS exam_id, e.name, t.name AS term_name, se.session, c.class, c.id AS class_id, s.section')
                ->from('cbse_exam_students es')->join('cbse_exams e', 'e.id = es.cbse_exam_id')->join('student_session ss', 'ss.id = es.student_session_id')
                ->join('classes c', 'c.id = ss.class_id')->join('sections s', 's.id = ss.section_id')->join('cbse_terms t', 't.id = e.cbse_term_id', 'left')
                ->join('sessions se', 'se.id = e.session_id', 'left')->where(array('ss.student_id' => $student_id, 'e.is_publish' => 1))
                ->order_by('e.session_id', 'desc')->order_by('e.cbse_term_id', 'desc')->get()->result_array();
        }
        $this->load->view('layout/student/header', $data);
        $this->load->view('user/cbse/reportcards', $data);
        $this->load->view('layout/student/footer', $data);
    }

    /** One report card of the student (published only): HTML, or PDF with ?pdf=1. */
    public function reportcard($es = 0)
    {
        $current = $this->customlib->getStudentCurrentClsSection();
        $student_id = !empty($current->student_session_id) ? (int) $this->db->select('student_id')->where('id', (int) $current->student_session_id)->get('student_session')->row('student_id') : 0;
        $row = $this->db->select('es.id, es.cbse_exam_id, ss.class_id')->from('cbse_exam_students es')->join('student_session ss', 'ss.id = es.student_session_id')
            ->join('cbse_exams e', 'e.id = es.cbse_exam_id')->where(array('es.id' => (int) $es, 'ss.student_id' => $student_id, 'e.is_publish' => 1))->get()->row_array();
        if (!$row) {
            show_404();
        }
        $this->load->library('class_results');
        $exam = $this->db->where('id', (int) $row['cbse_exam_id'])->get('cbse_exams')->row_array();
        $class = $this->db->where('id', (int) $row['class_id'])->get('classes')->row_array();
        $data = array('exam' => $exam, 'class' => $class, 'r' => $this->class_results->results($exam, (int) $class['id'], (int) $es), 'brand' => $this->customlib->brand(),
            'term_name' => $this->db->select('name')->where('id', (int) $exam['cbse_term_id'])->get('cbse_terms')->row('name'),
            'session_name' => $this->db->select('session')->where('id', (int) $exam['session_id'])->get('sessions')->row('session'), 'print' => true);
        if ($this->input->get('pdf')) {
            $data['print'] = false;
            $data['pdf'] = true;
            $this->load->library('m_pdf');
            $mpdf = $this->m_pdf->load('P');
            $mpdf->SetMargins(8, 8, 8);
            $mpdf->WriteHTML($this->load->view('cbseexam/results/cards', $data, true));
            $mpdf->Output(preg_replace('/[^A-Za-z0-9]+/', '-', $class['class'] . ' ' . $data['term_name'] . ' report card') . '.pdf', 'D');
            return;
        }
        $this->load->view('cbseexam/results/cards', $data);
    }

    public function result()
    {
        // Report cards by class (Report Cards > Classes & Results) are shown on their own page.
        if ($this->db->field_exists('class_id', 'cbse_exams')) {
            redirect('user/cbse/exam/reportcards');
        }
        $this->session->set_userdata('top_menu', 'cbse_exam');
		$this->session->set_userdata('sub_menu', 'user/cbse/cbse_exam_result');		
		
        $data = [];
        $student_current_class = $this->customlib->getStudentCurrentClsSection();

        $exam_list = $this->cbseexam_exam_model->getStudentExamByStudentSession($student_current_class->student_session_id);

        $student_exams = [];
        if (!empty($exam_list)) {
            foreach ($exam_list as $exam_key => $exam_value) {
               
              
                $exam_subjects = $this->cbseexam_exam_model->getexamsubjects($exam_value->cbse_exam_id);
                $exam_value->{"subjects"} = $exam_subjects;
                $exam_value->{"grades"} = $this->cbseexam_grade_model->getGraderangebyGradeID($exam_value->cbse_exam_grade_id);
                $exam_value->{"exam_assessments"} = $this->cbseexam_assessment_model->getWithAssessmentTypeByAssessmentID($exam_value->cbse_exam_assessment_id);
               
                $exam_value->{"exam_subject_assessments"} = $this->cbseexam_assessment_model->getSubjectAssessmentsByExam($exam_subjects);
   
                $cbse_exam_result = $this->cbseexam_exam_model->getStudentResultByExamId($exam_value->cbse_exam_id, [$exam_value->student_session_id]);
               
                $students = [];
                $student_rank="";

                if (!empty($cbse_exam_result)) {

                    foreach ($cbse_exam_result as $student_key => $student_value) {
                        $student_rank=$student_value->rank;
                   
                        if (!empty($students)) {

                            if (!array_key_exists($student_value->subject_id, $students['subjects'])) {

                                $new_subject = [
                                    'subject_id' => $student_value->subject_id,
                                    'subject_name' => $student_value->subject_name,
                                    'subject_code' => $student_value->subject_code,
                                    'exam_assessments' => [
                                        $student_value->cbse_exam_assessment_type_id => [
                                            'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                            'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                            'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                            'maximum_marks' => $student_value->maximum_marks,
                                            'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                            'marks' => $student_value->marks,
                                            'note' => $student_value->note,
                                            'is_absent' => $student_value->is_absent,
                                        ],
                                    ],
                                ];

                                $students['subjects'][$student_value->subject_id] = $new_subject;

                            } elseif (!array_key_exists($student_value->cbse_exam_assessment_type_id, $students['subjects'][$student_value->subject_id]['exam_assessments'])) {

                                $new_assesment = [
                                    'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                    'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                    'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                    'maximum_marks' => $student_value->maximum_marks,
                                    'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                    'marks' => $student_value->marks,
                                    'note' => $student_value->note,
                                    'is_absent' => $student_value->is_absent,
                                ];

                                $students['subjects'][$student_value->subject_id]['exam_assessments'][$student_value->cbse_exam_assessment_type_id] = $new_assesment;
                            }

                        } else {
                          
                            $students['subjects'] = [
                                $student_value->subject_id => [
                                    'subject_id' => $student_value->subject_id,
                                    'subject_name' => $student_value->subject_name,
                                    'subject_code' => $student_value->subject_code,
                                    'exam_assessments' => [
                                        $student_value->cbse_exam_assessment_type_id => [
                                            'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                            'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                            'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                            'maximum_marks' => $student_value->maximum_marks,
                                            'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                            'marks' => $student_value->marks,
                                            'note' => $student_value->note,
                                            'is_absent' => $student_value->is_absent,

                                        ],

                                    ],
                                ],

                            ];

                        }
                    }
                }
                $exam_value->{"rank"} = $student_rank;
                $exam_value->{"exam_data"} = $students;

            }
        }

        $data['exams'] = $exam_list;
      

        $this->load->view('layout/student/header', $data);
        $this->load->view('user/cbse/result', $data);
        $this->load->view('layout/student/footer', $data);
    }

}