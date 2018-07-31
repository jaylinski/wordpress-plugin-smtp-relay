<?php

class SMTPRelayPHPMailer
{
    public function __construct()
    {
        add_action('phpmailer_init', [$this, 'init']);
    }

    public static function getDefaultPort()
    {
        return 587;
    }

    public static function getDefaultFrom()
    {
        return get_bloginfo('name');
    }

    public static function getDefaultFromAddress()
    {
        return 'wordpress@' . parse_url(get_bloginfo('url'), PHP_URL_HOST);
    }

    public function init($phpmailer)
    {
        $options = get_option(SMTPRelayAdmin::OPTION_NAME);

        if (empty($options)) return;

        $phpmailer->isSMTP();
        $phpmailer->SMTPAuth = false;
        $phpmailer->Host = $options[SMTPRelayAdmin::OPTIONS['host']];
        $phpmailer->Port = (int) $options[SMTPRelayAdmin::OPTIONS['port']] ?: $this->getDefaultPort();
        $phpmailer->From = $options[SMTPRelayAdmin::OPTIONS['from_address']] ?: $this->getDefaultFromAddress();
        $phpmailer->FromName = $options[SMTPRelayAdmin::OPTIONS['from']] ?: $this->getDefaultFrom();
    }
}
