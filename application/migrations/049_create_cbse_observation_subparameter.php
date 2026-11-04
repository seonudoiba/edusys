<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `cbse_observation_subparameter`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: cbse_exam_observations, cbse_observation_parameters, school.
 */
class Migration_Create_cbse_observation_subparameter extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `cbse_observation_subparameter` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `cbse_exam_observation_id` int NOT NULL,
  `cbse_observation_parameter_id` int NOT NULL,
  `maximum_marks` float NOT NULL,
  `description` mediumtext,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cbse_observation_parameter_id_ibfk_1` (`cbse_observation_parameter_id`),
  KEY `cbse_exam_observation_id_ibfk_1` (`cbse_exam_observation_id`),
  KEY `idx_cbse_observation_subparameter_school_id` (`school_id`),
  CONSTRAINT `cbse_exam_observation_id_ibfk_1` FOREIGN KEY (`cbse_exam_observation_id`) REFERENCES `cbse_exam_observations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cbse_observation_parameter_id_ibfk_1` FOREIGN KEY (`cbse_observation_parameter_id`) REFERENCES `cbse_observation_parameters` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cbse_observation_subparameter_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `cbse_observation_subparameter`');
    }
}
