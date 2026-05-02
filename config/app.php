<?php

define('APP_NAME',    Env::get('APP_NAME', 'ClassSystem'));
define('APP_URL',     Env::get('APP_URL', 'http://localhost:8000'));
define('APP_VERSION', '1.0.0');
define('APP_ENV',     Env::get('APP_ENV', 'development')); // 'production'

define('OTP_EXPIRY_MINUTES', Env::get('OTP_EXPIRY_MINUTES', 10));
define('STUDENT_ID_PREFIX',  'STU');
define('TEACHER_ID_PREFIX',  'TCH');

define('SLIP_SUBMISSION_DAY', Env::get('SLIP_SUBMISSION_DAY', 25)); // Students can submit slips from this day each month
define('SLIP_GRACE_DAYS',     Env::get('SLIP_GRACE_DAYS', 10));  // Grace days after month starts before access is blocked
