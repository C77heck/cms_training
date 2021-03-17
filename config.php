<?php

/**
 * Configuration settings
 * 
 */

/* enviromental variables. */

define('DB_HOST', 'localhost');
define('DB_NAME', 'cms');
define('DB_USER', 'cms_www');
define('DB_PASS', 'mLys9kctXXSmK5d8');

define('SMTP_HOST', 'mail.example.com');
define('SMTP_USER', 'user@example.com');
define('SMTP_PASS', 'secret');

define('SHOW_ERROR_DETAIL', true);
/* we set this true only in developement mode to see the exceptions thrown if we 
deploy this app we turn this to false to prevent detailed error messages being thrown */