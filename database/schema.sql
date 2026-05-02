-- =============================================================
-- ClassSystem — Full Database Schema
-- Import into: classsystem_db
-- =============================================================


-- =============================================================
-- 1. USERS  (shared login table for all roles)
-- =============================================================
CREATE TABLE IF NOT EXISTS `users` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`       VARCHAR(150)        NOT NULL,
    `email`      VARCHAR(191)        UNIQUE DEFAULT NULL,
    `phone`      VARCHAR(20)         UNIQUE DEFAULT NULL,
    `profile_image` VARCHAR(255)     DEFAULT NULL,
    `password`   VARCHAR(255)        NOT NULL,
    `role`       ENUM('admin','teacher','student') NOT NULL,
    `created_at` TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- 2. STUDENTS
-- =============================================================
CREATE TABLE IF NOT EXISTS `students` (
    `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`          INT UNSIGNED        NOT NULL,
    `student_id`       VARCHAR(20)         NOT NULL UNIQUE,  -- e.g. STU4F2A1B
    `whatsapp_number`  VARCHAR(20)         DEFAULT NULL,
    `whatsapp_enabled` TINYINT(1)          NOT NULL DEFAULT 0,
    `created_at`       TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_students_user`
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- 3. TEACHERS
-- =============================================================
CREATE TABLE IF NOT EXISTS `teachers` (
    `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`        INT UNSIGNED        NOT NULL,
    `teacher_id`     VARCHAR(20)         NOT NULL UNIQUE,  -- e.g. TCH9D3C2E
    `is_first_login` TINYINT(1)          NOT NULL DEFAULT 1,
    `created_at`     TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_teachers_user`
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- 4. ADMINS
-- =============================================================
CREATE TABLE IF NOT EXISTS `admins` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`    INT UNSIGNED        NOT NULL,
    `admin_id`   VARCHAR(20)         NOT NULL UNIQUE,
    `created_at` TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_admins_user`
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- 5. OTPs
-- =============================================================
CREATE TABLE IF NOT EXISTS `otps` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `identifier` VARCHAR(191)        NOT NULL,   -- email or phone
    `code`       VARCHAR(10)         NOT NULL,
    `purpose`    ENUM(
                    'registration',
                    'password_reset',
                    'email_change',
                    'phone_change'
                 )                   NOT NULL,
    `used`       TINYINT(1)          NOT NULL DEFAULT 0,
    `expires_at` TIMESTAMP           NOT NULL,
    `created_at` TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_otp_lookup` (`identifier`, `purpose`, `used`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- 6. COURSES
-- =============================================================
CREATE TABLE IF NOT EXISTS `courses` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `teacher_id`  INT UNSIGNED        NOT NULL,
    `name`        VARCHAR(200)        NOT NULL,
    `subject`     VARCHAR(100)        NOT NULL,
    `grade`       VARCHAR(50)         NOT NULL,   -- e.g. "Grade 11" or "A/L Year 1"
    `price`              DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    `institute_cost`     DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    `teacher_commission` DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    `description`        TEXT                DEFAULT NULL,
    `cover_image`        VARCHAR(255)        DEFAULT NULL,
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

-- =============================================================
-- 7. SCHEDULES (class date & time entries per course)
-- =============================================================
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

-- =============================================================
-- 8. ENROLLMENTS (permanent — never deleted on missed slip)
-- =============================================================
CREATE TABLE IF NOT EXISTS `enrollments` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `student_id`  INT UNSIGNED        NOT NULL,
    `course_id`   INT UNSIGNED        NOT NULL,
    `enrolled_at` TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_enrollment` (`student_id`, `course_id`),
    CONSTRAINT `fk_enrollment_student`
        FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_enrollment_course`
        FOREIGN KEY (`course_id`)  REFERENCES `courses`(`id`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- 9. SLIPS (one row per student per course per month)
-- =============================================================
CREATE TABLE IF NOT EXISTS `slips` (
    `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `student_id`       INT UNSIGNED        NOT NULL,
    `course_id`        INT UNSIGNED        NOT NULL,
    `slip_month`       CHAR(7)             NOT NULL,  -- format: YYYY-MM e.g. 2025-04
    `file_path`        VARCHAR(255)        NOT NULL,
    `status`           ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    `rejection_reason` VARCHAR(255)        DEFAULT NULL,
    `submitted_at`     TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `reviewed_at`      TIMESTAMP           DEFAULT NULL,
    UNIQUE KEY `uq_slip_month` (`student_id`, `course_id`, `slip_month`),
    CONSTRAINT `fk_slips_student`
        FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_slips_course`
        FOREIGN KEY (`course_id`)  REFERENCES `courses`(`id`)  ON DELETE CASCADE,
    INDEX `idx_slips_month`  (`slip_month`),
    INDEX `idx_slips_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- 10. EXAMS (marking sheets created by teachers)
-- =============================================================
CREATE TABLE IF NOT EXISTS `exams` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `course_id`   INT UNSIGNED        NOT NULL,
    `title`       VARCHAR(200)        NOT NULL,   -- e.g. "Mid Term 2025"
    `created_by`  INT UNSIGNED        NOT NULL,   -- teacher user_id
    `created_at`  TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_exams_course`
        FOREIGN KEY (`course_id`)  REFERENCES `courses`(`id`)  ON DELETE CASCADE,
    CONSTRAINT `fk_exams_teacher`
        FOREIGN KEY (`created_by`) REFERENCES `teachers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- 11. GRADES (one row per student per exam)
-- =============================================================
CREATE TABLE IF NOT EXISTS `grades` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `exam_id`     INT UNSIGNED        NOT NULL,
    `student_id`  INT UNSIGNED        NOT NULL,
    `grade`       VARCHAR(10)         NOT NULL,   -- e.g. "A", "85", "Pass"
    `remarks`     VARCHAR(255)        DEFAULT NULL,
    `created_at`  TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_grade` (`exam_id`, `student_id`),
    CONSTRAINT `fk_grades_exam`
        FOREIGN KEY (`exam_id`)    REFERENCES `exams`(`id`)    ON DELETE CASCADE,
    CONSTRAINT `fk_grades_student`
        FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- 12. NOTIFICATIONS
-- =============================================================
CREATE TABLE IF NOT EXISTS `notifications` (
    `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `sender_role`    ENUM('admin','teacher')             NOT NULL,
    `sender_id`      INT UNSIGNED                        NOT NULL,
    `recipient_role` ENUM('admin','teacher','student')   NOT NULL,
    `recipient_id`   INT UNSIGNED                        NOT NULL,  -- user_id
    `message`        TEXT                                NOT NULL,
    `is_read`        TINYINT(1)                          NOT NULL DEFAULT 0,
    `created_at`     TIMESTAMP                           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_notif_recipient` (`recipient_id`, `is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- 13. SITE SETTINGS (key-value store)
-- =============================================================
CREATE TABLE IF NOT EXISTS `site_settings` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key`        VARCHAR(100)        NOT NULL UNIQUE,
    `value`      TEXT                DEFAULT NULL,
    `updated_at` TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default site settings
INSERT INTO `site_settings` (`key`, `value`) VALUES
    ('site_name',       'ClassSystem'),
    ('site_logo',       ''),
    ('site_favicon',    ''),
    ('smtp_host',       ''),
    ('smtp_port',       '587'),
    ('smtp_username',   ''),
    ('smtp_password',   ''),
    ('smtp_encryption', 'tls'),
    ('smtp_from_email', ''),
    ('smtp_from_name',  'ClassSystem');

-- =============================================================
-- Default Admin Account
-- Password: admin123  (bcrypt — change immediately after setup)
-- =============================================================
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
    ('Super Admin', 'admin@classystem.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

INSERT INTO `admins` (`user_id`, `admin_id`) VALUES
    (LAST_INSERT_ID(), 'ADM000001');
