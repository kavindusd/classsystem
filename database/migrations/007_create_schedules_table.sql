CREATE TABLE IF NOT EXISTS `schedules` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `course_id`   INT UNSIGNED        NOT NULL,
    `class_date`  DATE                NOT NULL,
    `start_time`  TIME                NOT NULL,
    `end_time`    TIME                NOT NULL,
    `notes`       VARCHAR(255)        DEFAULT NULL,
    `created_at`  TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_schedules_course`
        FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE,
    INDEX `idx_schedules_date` (`class_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
