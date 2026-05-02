CREATE TABLE IF NOT EXISTS `slips` (
    `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `student_id`       INT UNSIGNED        NOT NULL,
    `course_id`        INT UNSIGNED        NOT NULL,
    `slip_month`       CHAR(7)             NOT NULL,
    `file_path`        VARCHAR(255)        NOT NULL,
    `status`           ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    `rejection_reason` VARCHAR(255)        DEFAULT NULL,
    `submitted_at`     TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `reviewed_at`      TIMESTAMP           NULL DEFAULT NULL,
    UNIQUE KEY `uq_slip_month` (`student_id`, `course_id`, `slip_month`),
    CONSTRAINT `fk_slips_student`
        FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_slips_course`
        FOREIGN KEY (`course_id`)  REFERENCES `courses`(`id`)  ON DELETE CASCADE,
    INDEX `idx_slips_month`  (`slip_month`),
    INDEX `idx_slips_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
