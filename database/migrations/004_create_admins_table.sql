CREATE TABLE IF NOT EXISTS `admins` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`    INT UNSIGNED        NOT NULL,
    `admin_id`   VARCHAR(20)         NOT NULL UNIQUE,
    `created_at` TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_admins_user`
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
