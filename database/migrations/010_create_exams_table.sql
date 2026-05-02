CREATE TABLE IF NOT EXISTS `exams` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `course_id`   INT UNSIGNED        NOT NULL,
    `title`       VARCHAR(200)        NOT NULL,
    `created_by`  INT UNSIGNED        NOT NULL,
    `created_at`  TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_exams_course`
        FOREIGN KEY (`course_id`)  REFERENCES `courses`(`id`)  ON DELETE CASCADE,
    CONSTRAINT `fk_exams_teacher`
        FOREIGN KEY (`created_by`) REFERENCES `teachers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
