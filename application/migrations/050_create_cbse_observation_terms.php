<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `cbse_observation_terms`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: cbse_exam_observations, cbse_terms, sessions, school.
 */
class Migration_Create_cbse_observation_terms extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `cbse_observation_terms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `cbse_exam_observation_id` int NOT NULL,
  `cbse_term_id` int NOT NULL,
  `session_id` int NOT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  KEY `cbse_term_id` (`cbse_term_id`),
  KEY `cbse_ovservation_terms_ibfk_3` (`session_id`),
  KEY `cbse_exam_observations_ibfk_1` (`cbse_exam_observation_id`),
  KEY `idx_cbse_observation_terms_school_id` (`school_id`),
  CONSTRAINT `cbse_exam_observations_ibfk_1` FOREIGN KEY (`cbse_exam_observation_id`) REFERENCES `cbse_exam_observations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cbse_observation_terms_ibfk_2` FOREIGN KEY (`cbse_term_id`) REFERENCES `cbse_terms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cbse_observation_terms_ibfk_3` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cbse_observation_terms_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `cbse_observation_terms`');
    }
}
