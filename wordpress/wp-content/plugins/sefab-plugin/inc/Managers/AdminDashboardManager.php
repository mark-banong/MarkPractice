<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Managers;

class AdminDashboardManager
{

    private $extaFields;

    public function __construct()
    {
        $this->extraFields = [
            ['Phone', __('Phone Number', 'rc_cucm'), true],
        ];
    }

    public function save_extra_fields($user_id, $password = '', $meta = [])
    {
        $user_data = [];
        $user_data['ID'] = $user_id;

        foreach ($this->extraFields as $field) {
            if ($field[2] === true) {
                $user_data[$field[0]] = $_POST[$field[0]];
            }
        }

        $new_user_id = wp_update_user($user_data);
    }

    public function register_scripts($hook)
    {
        wp_enqueue_script('sefab-admin-script', plugins_url('../resources/scripts/admin-dashboard.js', __FILE__));
    }

    public function change_username($translated_text, $text, $domain)
    {
        if ($text === 'Username') {
            $translated_text = 'Phone Number';
        }

        if ($text === 'Usernames cannot be changed.') {
            $translated_text = "Phone Numbers cannot be changed";
        }
        return $translated_text;
    }

    public function RemoveAddMediaButtonsForNonAdmins()
    {
        remove_action('media_buttons', 'media_buttons');
    }

    public function register_actions()
    {
        add_filter('gettext', [$this, 'change_username'], 20, 3);
        add_action('admin_head', [$this, 'RemoveAddMediaButtonsForNonAdmins']);
        add_action('admin_enqueue_scripts', [$this, 'register_scripts']);
    }
}
