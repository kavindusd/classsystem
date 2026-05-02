CREATE TABLE IF NOT EXISTS `courses` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `teacher_id`  INT UNSIGNED        NOT NULL,
    `name`        VARCHAR(200)        NOT NULL,
    `subject`     VARCHAR(100)        NOT NULL,
    `grade`       VARCHAR(50)         NOT NULL,
    `price`       DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    `description`        TEXT                DEFAULT NULL,
    `class_days`         VARCHAR(100)        DEFAULT NULL,  -- comma-separated: Mon,Wed,Fri
    `class_start_time`   TIME                DEFAULT NULL,
    `class_end_time`     TIME                DEFAULT NULL,
    `status`      ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_at`  TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_courses_teacher`
        FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE CASCADE,
    INDEX `idx_courses_subject` (`subject`),
    INDEX `idx_courses_grade`   (`grade`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
