CREATE TABLE IF NOT EXISTS `notifications` (
    `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `sender_role`    ENUM('admin','teacher')           NOT NULL,
    `sender_id`      INT UNSIGNED                      NOT NULL,
    `recipient_role` ENUM('admin','teacher','student') NOT NULL,
    `recipient_id`   INT UNSIGNED                      NOT NULL,
    `message`        TEXT                              NOT NULL,
    `is_read`        TINYINT(1)                        NOT NULL DEFAULT 0,
    `created_at`     TIMESTAMP                         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_notif_recipient` (`recipient_id`, `is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
