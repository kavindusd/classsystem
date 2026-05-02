CREATE TABLE IF NOT EXISTS `students` (
    `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`          INT UNSIGNED        NOT NULL,
    `student_id`       VARCHAR(20)         NOT NULL UNIQUE,
    `whatsapp_enabled` TINYINT(1)          NOT NULL DEFAULT 0,
    `created_at`       TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_students_user`
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
