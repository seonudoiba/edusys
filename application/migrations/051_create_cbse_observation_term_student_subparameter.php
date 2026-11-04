<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `cbse_observation_term_student_subparameter`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: cbse_observation_terms, cbse_observation_subparameter, student_session, school.
 */
class Migration_Create_cbse_observation_term_student_subparameter extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `cbse_observation_term_student_subparameter` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `cbse_ovservation_term_id` int DEFAULT NULL,
  `cbse_observation_subparameter_id` int DEFAULT NULL,
  `student_session_id` int DEFAULT NULL,
  `obtain_marks` float(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cbse_observation_term_student_subparameter_ibfk_1` (`cbse_ovservation_term_id`),
  KEY `cbse_observation_subparameter_id` (`cbse_observation_subparameter_id`),
  KEY `student_session_id` (`student_session_id`),
  KEY `idx_cbse_observation_term_student_subparameter_school_id` (`school_id`),
  CONSTRAINT `cbse_observation_term_student_subparameter_ibfk_1` FOREIGN KEY (`cbse_ovservation_term_id`) REFERENCES `cbse_observation_terms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cbse_observation_term_student_subparameter_ibfk_2` FOREIGN KEY (`cbse_observation_subparameter_id`) REFERENCES `cbse_observation_subparameter` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cbse_observation_term_student_subparameter_ibfk_3` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cbse_observation_term_student_subparameter_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `cbse_observation_term_student_subparameter`');
    }
}
