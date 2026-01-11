<?php

/*
Plugin Name: SMTP Relay
Description: Configure a SMTP relay for outgoing emails.
Version: 1.0.1
Author: jakobword
License: GPLv2 or later
Text Domain: smtp-relay
*/

define('SMTP_RELAY_SLUG', 'smtp-relay');
define('SMTP_RELAY_FILE_NAME', plugin_basename(__FILE__));

require_once(__DIR__ . '/includes/class.smtp-relay.admin.php');
require_once(__DIR__ . '/includes/class.smtp-relay.phpmailer.php');

function smtp_relay_init(): void
{
    new SMTPRelayPHPMailer();
}

if (is_admin() && current_user_can('manage_options')) {
    new SMTPRelayAdmin();
}

add_action('init', 'smtp_relay_init');
