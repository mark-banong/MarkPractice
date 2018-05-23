<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Managers;

class StatisticsDashboardManager
{
    private $formManager;
    private $readManager;
    private $environment;
    private $dbManager;

    public function __construct($db_manager, $environment, $read_manager, $form_manager)
    {
        //Construct and inject dependencies
        $this->environment = $environment;
        require_once $this->environment->pluginPath . "vendor/autoload.php";

        $this->dbManager = $db_manager;
        $this->readManager = $read_manager;
        $this->formManager = $form_manager;
    }

    public function display_statistics_page()
    {
        if (isset($_GET['form'])) {
            $forms = $this->formManager->get_forms_gouped_by_wp_form_id($_GET['form']);
            $questions = $this->formManager->get_form_questions($_GET['form']);
            $col_width = 100 / (count($questions) + 1);
            $answer_data = $this->formManager->get_form_policies($_GET['form']);
        } else if (isset($_GET['policy'])) {
            $data = $this->readManager->get_user_read_data_by_policy_id($_GET['policy']);
        } else {
            $read_unread_amount = $this->readManager->get_read_unread_amount();
            $policy_read_unread_amount = $this->readManager->get_read_unread_amount_per_policy();
            $forms = $this->formManager->get_forms_gouped_by_wp_form_id();
        }

        require_once $this->environment->pluginPath . 'inc/resources/views/dashboard-statistics.php';
    }

    public function add_menu_items()
    {
        if (current_user_can('administrator') || current_user_can('lower_administrator') || current_user_can('super_administrator')) {
            $page_title = 'Statistics';
            $menu_title = 'Statistics';
            $capability = 'read';
            $menu_slug = 'sefab-statistics';
            $function = [$this, 'display_statistics_page'];
            $icon_url = 'dashicons-welcome-view-site';
            $position = 4;
            add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position);
        }
    }

    public function register_scripts($hook)
    {
        if ($hook === 'toplevel_page_sefab-statistics') {
            $plugin_url = plugin_dir_url(plugin_dir_url(__FILE__));
            wp_enqueue_style('datatable-styles', 'https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css');
            wp_enqueue_script('datatable-scripts', 'https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js');
            wp_enqueue_style('sefab-dashboard-styles', plugins_url('../resources/styles/dashboard-styles.css', __FILE__));
            wp_enqueue_script('sefab-statistics-script', plugins_url('../resources/scripts/statistics.js', __FILE__));
            wp_enqueue_script('sefab-statistics-canvas-js-script', plugins_url('../resources/scripts/chart.min.js', __FILE__));
        }
    }

    public function register_actions()
    {
        add_action('admin_enqueue_scripts', [$this, 'register_scripts']);
    }

}
