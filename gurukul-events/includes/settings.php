<?php
if (!defined('ABSPATH')) {
    exit;
}

class Gurukul_Events_Settings {
    private $options;

    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
    }

    public function add_plugin_page() {
        add_submenu_page(
            'edit.php?post_type=gurukul_event',
            'Event Settings',
            'Settings',
            'manage_options',
            'gurukul-event-settings',
            array($this, 'create_admin_page')
        );
    }

    public function create_admin_page() {
        $this->options = get_option('gurukul_event_settings'); ?>

        <div class="wrap">
            <h1>Event Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('gurukul_event_settings_group');
                do_settings_sections('gurukul-event-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function page_init() {
        register_setting(
            'gurukul_event_settings_group',
            'gurukul_event_settings',
            array($this, 'sanitize')
        );

        add_settings_section(
            'gurukul_event_registration_section',
            'Registration Settings',
            array($this, 'section_info'),
            'gurukul-event-settings'
        );

        add_settings_field(
            'registration_page',
            'Registration Page URL',
            array($this, 'registration_page_callback'),
            'gurukul-event-settings',
            'gurukul_event_registration_section'
        );

        add_settings_field(
            'registration_button_text',
            'Registration Button Text',
            array($this, 'registration_button_text_callback'),
            'gurukul-event-settings',
            'gurukul_event_registration_section'
        );
    }

    public function sanitize($input) {
        $new_input = array();
        
        if(isset($input['registration_page']))
            $new_input['registration_page'] = esc_url_raw($input['registration_page']);
            
        if(isset($input['registration_button_text']))
            $new_input['registration_button_text'] = sanitize_text_field($input['registration_button_text']);

        return $new_input;
    }

    public function section_info() {
        echo 'Configure your event registration settings below:';
    }

    public function registration_page_callback() {
        $value = isset($this->options['registration_page']) ? esc_attr($this->options['registration_page']) : '';
        printf(
            '<input type="url" id="registration_page" name="gurukul_event_settings[registration_page]" value="%s" class="regular-text" />
            <p class="description">Enter the URL where users will be redirected to register for events. You can use {event_id} and {event_title} in the URL as placeholders.</p>',
            $value
        );
    }

    public function registration_button_text_callback() {
        $value = isset($this->options['registration_button_text']) ? esc_attr($this->options['registration_button_text']) : 'Register Now';
        printf(
            '<input type="text" id="registration_button_text" name="gurukul_event_settings[registration_button_text]" value="%s" class="regular-text" />',
            $value
        );
    }
}

if (is_admin())
    $gurukul_events_settings = new Gurukul_Events_Settings(); 