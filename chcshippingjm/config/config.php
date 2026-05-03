<?php

define('APP_NAME', 'CHC Shipping JM');
define('APP_URL', 'http://localhost/chcshippingjm/public');

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'chcshippingjm');
define('DB_USER', 'root');
define('DB_PASS', '');

define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'mailer@example.com');
define('SMTP_PASS', 'change-me');
define('SMTP_FROM_EMAIL', 'no-reply@chcshipping.com');
define('SMTP_FROM_NAME', APP_NAME);

define('MAIL_FALLBACK_LOG', __DIR__ . '/../storage/logs/mail.log');
