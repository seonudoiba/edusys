<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


function unique_array($my_array, $key)
{
    // Ranks for a list sorted best first, standard competition ranking: equal scores share a rank
    // and the next rank is skipped (1, 1, 3). Returns rank => score, looked up with array_search().
    $result = array();
    $seen = array();
    $pos = 0;
    foreach ($my_array as $val) {
        $pos++;
        if (!in_array($val[$key], $seen)) {
            $seen[] = $val[$key];
            $result[$pos] = $val['rank_percentage'];
        }
    }
    return $result;
}

function searcharray($value, $key, $array)
{
    foreach ($array as $k => $val) {

        if ($val[$key] == $value) {
            return $val['rank'];
        }
    }
    return null;
}

function getSubjectData($student_array, $find_exam, $find_subject_id, $find_cbse_exam_assessment_type_id)
{
    $return_array = [
        'maximum_marks' => "",
        'marks' => "",
        'note' => "",
        'is_absent' => "",
        'cbse_exam_timetable_assessment_type_id' => false,
    ];

    if (array_key_exists($find_exam, $student_array['exams'])) {


        if (array_key_exists($find_subject_id, $student_array['exams'][$find_exam]['subjects'])) {
   
            $result_array = ($student_array['exams'][$find_exam]['subjects'][$find_subject_id]['exam_assessments'][$find_cbse_exam_assessment_type_id]);
            if(!is_null($result_array['cbse_exam_timetable_assessment_type_id'])){
                $return_array = [
                    'maximum_marks' => $result_array['maximum_marks'],
                    'marks' => is_null($result_array['marks']) ? "N/A" : $result_array['marks'],
                    'note' => $result_array['note'],
                    'is_absent' => $result_array['is_absent'],
                    'cbse_exam_timetable_assessment_type_id' => true,
                ];
            }          
        }
    }

    return $return_array;
}

function getSubjectDataTerm($term_array,$find_term,$find_exam,$find_subject_id,$find_cbse_exam_assessment_type_id){

    $return_array=[
                      'maximum_marks'=>"",                   
                      'marks'=>"",
                      'note'=>"",
                      'is_absent'=>"",
                      ];
  
  if(!empty($term_array)){
  
  if (array_key_exists($find_term, $term_array)){
  
  if (array_key_exists($find_exam, $term_array[$find_term]['exams'])){
  
   if (array_key_exists($find_subject_id, $term_array[$find_term]['exams'][$find_exam]['subjects'])){
       $result_array=($term_array[$find_term]['exams'][$find_exam]['subjects'][$find_subject_id]['exam_assessments'][$find_cbse_exam_assessment_type_id]);
       $return_array=[
                      'maximum_marks'=>$result_array['maximum_marks'],              
                      'marks'=>is_null($result_array['marks']) ? "N/A" : $result_array['marks'],
                      'note'=>$result_array['note'],
                      'is_absent'=>$result_array['is_absent'],
                      ];
   }
    }
  }
  
  }
  
  return $return_array;        
  
  }

?>