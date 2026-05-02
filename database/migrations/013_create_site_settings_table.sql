CREATE TABLE IF NOT EXISTS `site_settings` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key`        VARCHAR(100)        NOT NULL UNIQUE,
    `value`      TEXT                DEFAULT NULL,
    `updated_at` TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `site_settings` (`key`, `value`) VALUES
    ('site_name',       'ClassSystem'),
    ('site_favicon',    ''),
    ('smtp_host',       ''),
    ('smtp_port',       '587'),
    ('smtp_username',   ''),
    ('smtp_password',   ''),
    ('smtp_encryption', 'tls'),
    ('smtp_from_email', ''),
    ('smtp_from_name',  'ClassSystem');
