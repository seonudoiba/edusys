<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Creates `student_fees_deposite`.
 *
 * School-owned table: every row belongs to exactly one school (school_id).
 * References: student_transport_fees, student_fees_master, fee_groups_feetype, school.
 */
class Migration_Create_student_fees_deposite extends CI_Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE TABLE `student_fees_deposite` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `student_fees_master_id` int DEFAULT NULL,
  `fee_groups_feetype_id` int DEFAULT NULL,
  `student_transport_fee_id` int DEFAULT NULL,
  `amount_detail` text,
  `is_active` varchar(10) NOT NULL DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_fees_master_id` (`student_fees_master_id`),
  KEY `fee_groups_feetype_id` (`fee_groups_feetype_id`),
  KEY `student_transport_fee_id` (`student_transport_fee_id`),
  KEY `idx_student_fees_deposite_school_id` (`school_id`),
  CONSTRAINT `student_fees_deposite_ibfk_1` FOREIGN KEY (`student_transport_fee_id`) REFERENCES `student_transport_fees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_fees_deposite_ibfk_2` FOREIGN KEY (`student_fees_master_id`) REFERENCES `student_fees_master` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_fees_deposite_ibfk_3` FOREIGN KEY (`fee_groups_feetype_id`) REFERENCES `fee_groups_feetype` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_student_fees_deposite_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `student_fees_deposite`');
    }
}
