CREATE TABLE IF NOT EXISTS `otps` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `identifier` VARCHAR(191)        NOT NULL,
    `code`       VARCHAR(10)         NOT NULL,
    `purpose`    ENUM('registration','password_reset','email_change','phone_change') NOT NULL,
    `used`       TINYINT(1)          NOT NULL DEFAULT 0,
    `expires_at` TIMESTAMP           NOT NULL,
    `created_at` TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_otp_lookup` (`identifier`, `purpose`, `used`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
