<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Netmon Application Configuration
|--------------------------------------------------------------------------
| All tactical/tunable variables for the Netmon application.
| Change values here instead of hardcoding in controllers/models.
|
*/

// Ping failure threshold before marking device as DOWN
$config['ping_fail_threshold'] = 3;

// Housekeeping: retention period for monitoring_logs (in days)
$config['log_retention_days'] = 7;

// Telegram Bot Notification
$config['telegram_enabled']   = FALSE;
$config['telegram_bot_token'] = '';
$config['telegram_chat_id']   = '';

// Dashboard AJAX refresh interval (milliseconds)
$config['dashboard_refresh_ms'] = 10000;

// Ping timeout (seconds) for CLI polling
$config['ping_timeout'] = 2;

// Ping count per device
$config['ping_count'] = 1;
