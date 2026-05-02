CREATE TABLE IF NOT EXISTS `teachers` (
    `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`        INT UNSIGNED        NOT NULL,
    `teacher_id`     VARCHAR(20)         NOT NULL UNIQUE,
    `is_first_login` TINYINT(1)          NOT NULL DEFAULT 1,
    `created_at`     TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_teachers_user`
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
