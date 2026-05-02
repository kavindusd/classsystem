<?php

// SMTP settings — managed via admin site settings panel
// These are defaults; live values loaded from DB at runtime
define('SMTP_HOST',       '');
define('SMTP_PORT',       587);
define('SMTP_USERNAME',   '');
define('SMTP_PASSWORD',   '');
define('SMTP_ENCRYPTION', 'tls');
define('SMTP_FROM_EMAIL', '');
define('SMTP_FROM_NAME',  APP_NAME);
