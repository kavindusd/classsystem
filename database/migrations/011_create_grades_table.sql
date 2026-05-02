CREATE TABLE IF NOT EXISTS `grades` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `exam_id`     INT UNSIGNED        NOT NULL,
    `student_id`  INT UNSIGNED        NOT NULL,
    `grade`       VARCHAR(10)         NOT NULL,
    `remarks`     VARCHAR(255)        DEFAULT NULL,
    `created_at`  TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_grade` (`exam_id`, `student_id`),
    CONSTRAINT `fk_grades_exam`
        FOREIGN KEY (`exam_id`)    REFERENCES `exams`(`id`)    ON DELETE CASCADE,
    CONSTRAINT `fk_grades_student`
        FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
