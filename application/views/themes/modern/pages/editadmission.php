<?php
/* Changing an application before it's sent: the admission form (pages/admission.php), filled in. */
$edit_values = array(
    'class_id' => $class_id, 'firstname' => $firstname, 'middlename' => $middlename, 'lastname' => $lastname, 'gender' => $gender,
    'dob' => $dob, 'mobileno' => $mobileno, 'email' => $email, 'category_id' => $category_id, 'religion' => $religion, 'cast' => $cast,
    'house' => $house_id, 'blood_group' => $blood_group, 'height' => $height, 'weight' => $weight, 'measure_date' => $measurement_date,
    'father_name' => $father_name, 'father_phone' => $father_phone, 'father_occupation' => $father_occupation,
    'mother_name' => $mother_name, 'mother_phone' => $mother_phone, 'mother_occupation' => $mother_occupation,
    'guardian_is' => $guardian_is, 'guardian_name' => $guardian_name, 'guardian_relation' => $guardian_relation, 'guardian_email' => $guardian_email,
    'guardian_phone' => $guardian_phone, 'guardian_occupation' => $guardian_occupation, 'guardian_address' => $guardian_address,
    'current_address' => $current_address, 'permanent_address' => $permanent_address, 'bank_account_no' => $bank_account_no,
    'bank_name' => $bank_name, 'ifsc_code' => $ifsc_code, 'adhar_no' => $adhar_no, 'samagra_id' => $samagra_id,
    'previous_school' => $previous_school, 'note' => $note, 'rte' => $rte,
);
include __DIR__ . '/admission.php';
