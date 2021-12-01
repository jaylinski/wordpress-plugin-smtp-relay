<?php

class SMTPRelayAdmin
{
    private const OPTION_GROUP_NAME = 'smtp_relay_option_group';
    public const OPTION_NAME = 'smtp_relay_options';
    public const OPTIONS = [
        'host' => 'smtp_relay_host',
        'port' => 'smtp_relay_port',
        'from' => 'smtp_relay_from',
        'from_address' => 'smtp_relay_from_address',
    ];

    /**
     * @var mixed Holds the values to be used in the fields callbacks.
     */
    private $options;

    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_plugin_page']);
        add_action('admin_init', [$this, 'add_plugin_settings']);
        add_action('wp_ajax_' . SMTP_RELAY_SLUG . '_test', [$this, 'action_test']);
        add_filter('plugin_action_links_' . SMTP_RELAY_FILE_NAME, [$this, 'add_action_links']);
    }

    /**
     * Add options page.
     */
    public function add_plugin_page(): void
    {
        $page = add_options_page(
            __('SMTP Relay', 'smtp-relay'),
            __('SMTP Relay', 'smtp-relay'),
            'manage_options',
            SMTP_RELAY_SLUG,
            [$this, 'render_options_page']
        );

        add_action('admin_footer-' . $page, [$this, 'add_script']);
    }

    /**
     * Add settings link to plugins page.
     */
    public function add_action_links(array $links): array
    {
        $plugin_links = [
            '<a href="' . admin_url( 'options-general.php?page=' . SMTP_RELAY_SLUG ) . '">' . __('Settings') . '</a>',
        ];

        return array_merge( $links, $plugin_links );
    }

    /**
     * Options page callback.
     */
    public function render_options_page(): void
    {
        $this->options = get_option(self::OPTION_NAME);
        ?>
        <div class="wrap">
            <h1><?php _e('SMTP Relay', 'smtp-relay') ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields(self::OPTION_GROUP_NAME);
                do_settings_sections(SMTP_RELAY_SLUG);
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings.
     */
    public function add_plugin_settings(): void
    {
        register_setting(self::OPTION_GROUP_NAME, self::OPTION_NAME, [$this, 'sanitize']);

        add_settings_section(
            'smtp_relay_section_settings',
            __('Settings'),
            [$this, 'settings_section'],
            SMTP_RELAY_SLUG
        );

        add_settings_field(
            self::OPTIONS['host'],
            __('Mail Server'),
            [$this, 'option_host'],
            SMTP_RELAY_SLUG,
            'smtp_relay_section_settings',
            ['label_for' => self::OPTIONS['host']]
        );

        add_settings_field(
            self::OPTIONS['from'],
            __('From', 'smtp-relay'),
            [$this, 'option_from'],
            SMTP_RELAY_SLUG,
            'smtp_relay_section_settings',
            ['label_for' => self::OPTIONS['from']]
        );

        add_settings_field(
            self::OPTIONS['from_address'],
            __('From address', 'smtp-relay'),
            [$this, 'option_from_address'],
            SMTP_RELAY_SLUG,
            'smtp_relay_section_settings',
            ['label_for' => self::OPTIONS['from_address']]
        );

        add_settings_section(
            'smtp_relay_section_test',
            __('Test'),
            [$this, 'settings_section'],
            SMTP_RELAY_SLUG
        );

        add_settings_field(
            'smtp_relay_test',
            __('Email Address'),
            [$this, 'option_test'],
            SMTP_RELAY_SLUG,
            'smtp_relay_section_test',
            ['label_for' => 'smtp_relay_test']
        );
    }

    /**
     * Sanitize each setting field as needed.
     *
     * @param mixed $input Contains all settings fields as array keys.
     * @return mixed
     */
    public function sanitize($input)
    {
        return is_array($input) ? array_map(function ($field) {
            return trim($field);
        }, $input) : $input;
    }

    public function settings_section(array $arg): void
    {
        // Noop
    }

    public function option_host(): void
    {
        printf(
            '<input type="text" class="regular-text code" id="%s" name="%s[%s]" value="%s" placeholder="smtp.example.local">',
            self::OPTIONS['host'],
            self::OPTION_NAME,
            self::OPTIONS['host'],
            isset($this->options[self::OPTIONS['host']]) ? esc_attr($this->options[self::OPTIONS['host']]) : ''
        );

        print('<br>');

        printf(
            '<input type="number" class="regular-text code" id="%s" name="%s[%s]" value="%s" placeholder="%s">',
            self::OPTIONS['port'],
            self::OPTION_NAME,
            self::OPTIONS['port'],
            isset($this->options[self::OPTIONS['port']]) ? esc_attr($this->options[self::OPTIONS['port']]) : '',
            SMTPRelayPHPMailer::getDefaultPort()
        );

        printf(
            '<p class="description" id="tagline-description">%s</p>',
            __('The host name and port of your SMTP relay.', 'smtp-relay')
        );
    }

    public function option_from(): void
    {
        printf(
            '<input type="text" class="regular-text" id="%s" name="%s[%s]" value="%s" placeholder="%s">',
            self::OPTIONS['from'],
            self::OPTION_NAME,
            self::OPTIONS['from'],
            isset($this->options[self::OPTIONS['from']]) ? esc_attr($this->options[self::OPTIONS['from']]) : '',
            SMTPRelayPHPMailer::getDefaultFrom()
        );

        printf(
            '<p class="description" id="tagline-description">%s</p>',
            __('The name your mails will be sent with.', 'smtp-relay')
        );
    }

    public function option_from_address(): void
    {
        printf(
            '<input type="text" class="regular-text" id="%s" name="%s[%s]" value="%s" placeholder="%s">',
            self::OPTIONS['from_address'],
            self::OPTION_NAME,
            self::OPTIONS['from_address'],
            isset($this->options[self::OPTIONS['from_address']]) ? esc_attr($this->options[self::OPTIONS['from_address']]) : '',
            SMTPRelayPHPMailer::getDefaultFromAddress()
        );

        printf(
            '<p class="description" id="tagline-description">%s</p>',
            __('The email address your mails will be sent with.', 'smtp-relay')
        );
    }

    public function option_test(): void
    {
        printf(
            '<input type="text" class="regular-text" id="smtp_relay_test" name="smtp_relay_test" placeholder="test@example.com" %s>' .
            '<button id="smtp_relay_test_submit" class="button button-small button-secondary" %s>%s</button>',
            isset($this->options[self::OPTIONS['host']]) && !empty($this->options[self::OPTIONS['host']]) ? '' : 'disabled',
            isset($this->options[self::OPTIONS['host']]) && !empty($this->options[self::OPTIONS['host']]) ? '' : 'disabled',
            __('Send test mail', 'smtp-relay')
        );
    }

    public function action_test(): void
    {
        check_ajax_referer(SMTP_RELAY_SLUG, 'security');

        $email = filter_var($_POST['email'] ?? null, FILTER_VALIDATE_EMAIL);

        if (!$email) {
            wp_die(__('Invalid email'), 'Bad Request', ['response' => 400]);
        } elseif (wp_mail($email, 'SMTP Relay Test', 'SMTP Relay Test')) {
            wp_die();
        } else {
            wp_die(__('Could not send email'), 'Internal Server Error', ['response' => 500]);
        }
    }

    public function add_script(): void
    {
        $ajax_nonce = wp_create_nonce(SMTP_RELAY_SLUG);
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                var test_button = $('#smtp_relay_test_submit');
                $(test_button).on('click', function (event) {
                    event.preventDefault();

                    var data = {
                        'action': '<?php echo SMTP_RELAY_SLUG; ?>_test',
                        'email': $('#smtp_relay_test').val(),
                        'security': '<?php echo $ajax_nonce; ?>',
                    };

                    $.post(ajaxurl, data, function () {
                        $(test_button).text('<?php esc_html_e('Email sent') ?>');
                    }).fail(function (jqXHR, textStatus, error) {
                        $(test_button).text(error + ': ' + jqXHR.responseText);
                    }).always(function () {
                        setTimeout(function () {
                            $(test_button).text('<?php esc_html_e('Send test mail', 'smtp-relay') ?>')
                        }, 4000);
                    });
                });
            });
        </script>
        <?php
    }
}
