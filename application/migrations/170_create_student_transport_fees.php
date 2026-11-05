<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `student_transport_fees`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: student_session, route_pickup_point, transport_feemaster, school.
 */
class Migration_Create_student_transport_fees extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `student_transport_fees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `transport_feemaster_id` int NOT NULL,
  `student_session_id` int NOT NULL,
  `route_pickup_point_id` int NOT NULL,
  `generated_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_session_id` (`student_session_id`),
  KEY `route_pickup_point_id` (`route_pickup_point_id`),
  KEY `transport_feemaster_id` (`transport_feemaster_id`),
  KEY `idx_student_transport_fees_school_id` (`school_id`),
  CONSTRAINT `student_transport_fees_ibfk_1` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_transport_fees_ibfk_2` FOREIGN KEY (`route_pickup_point_id`) REFERENCES `route_pickup_point` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_transport_fees_ibfk_3` FOREIGN KEY (`transport_feemaster_id`) REFERENCES `transport_feemaster` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_student_transport_fees_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `student_transport_fees`');
    }
}
