CREATE TABLE IF NOT EXISTS `enrollments` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `student_id`  INT UNSIGNED        NOT NULL,
    `course_id`   INT UNSIGNED        NOT NULL,
    `enrolled_at` TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_enrollment` (`student_id`, `course_id`),
    CONSTRAINT `fk_enrollment_student`
        FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_enrollment_course`
        FOREIGN KEY (`course_id`)  REFERENCES `courses`(`id`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
