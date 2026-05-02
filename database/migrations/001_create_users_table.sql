CREATE TABLE IF NOT EXISTS `users` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`       VARCHAR(150)        NOT NULL,
    `email`      VARCHAR(191)        UNIQUE DEFAULT NULL,
    `phone`      VARCHAR(20)         UNIQUE DEFAULT NULL,
    `password`   VARCHAR(255)        NOT NULL,
    `role`       ENUM('admin','teacher','student') NOT NULL,
    `created_at` TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
