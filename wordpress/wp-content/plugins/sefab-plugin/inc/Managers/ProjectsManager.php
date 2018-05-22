<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Managers;

class ProjectsManager
{
    private $projectsProvider;
    private $environment;
    private $logService;
    private $dbManager;

    public function __construct($db_manager, $environment, $log_service, $projects_provider)
    {
        $this->projectsProvider = $projects_provider;
        $this->environment = $environment;
        $this->logService = $log_service;
        $this->dbManager = $db_manager;

        require_once $this->environment->pluginPath . "vendor/autoload.php";
    }

    public function get_all()
    {
        $result = $this->projectsProvider->get_all();

        return $result;
    }

    public function get_by_id($id)
    {
        $result = $this->projectsProvider->get_by_id($id);

        return $result;
    }

    public function add_test()
    {
        $data = [
            'name' => "Test Project",
            'description' => "This is my amazing description",
            'latitude' => 53.2734,
            'longitude' => -7.778320310000026,
        ];

        $this->projectsProvider->insert($data);

    }

    public function display_projects_page()
    {
        if (isset($_GET['add']) && $_GET['add'] === 'true') {
            require_once $this->environment->pluginPath . 'inc/resources/views/dashboard-projects-add.php';

        } else if (!isset($_GET['add']) && !isset($_GET['delete']) && isset($_GET['id']))  {
            $project = $this->projectsProvider->get_by_id($_GET['id']);

            require_once $this->environment->pluginPath . 'inc/resources/views/dashboard-projects-details.php';
        } else {
            $projects = $this->projectsProvider->get_all();

            if (isset($_GET['add']) && $_GET['add'] === 'success' && isset($_GET['id'])) {
                $last_added_project = $this->projectsProvider->get_by_id($_GET['id']);
                require_once $this->environment->pluginPath . 'inc/resources/views/dashboard-projects-add-success.php';
            }

            if (isset($_GET['delete']) && $_GET['delete'] === 'success' && isset($_GET['id']) ) {
                $deleted_project = $this->projectsProvider->get_by_id($_GET['id']);
                require_once $this->environment->pluginPath . 'inc/resources/views/dashboard-projects-delete-success.php';

            }

            require_once $this->environment->pluginPath . 'inc/resources/views/dashboard-projects-table.php';
        }
    }

    public function delete($data) {
        $this->logService->Log("projects_manager_logs", json_encode([
            'method' => 'delete',
            'id' => $data['id']
        ]));

        $result = $this->projectsProvider->delete($data['id']);

        $this->logService->Log("projects_manager_logs", json_encode([
            'method' => 'delete',
            'result' => $result
        ]));

        wp_redirect('/wp-admin/admin.php?page=sefab-projects&delete=success&id=' . $data['id']);
        die();
    }

    public function add($data)
    {
        $this->logService->Log('projects_manager_logs', json_encode([
            'method' => 'add',
            'name' => $data['name'],
            'description' => $data['description'],
            'latitude' => $data['lat'],
            'longitude' => $data['lng'],
            'image_id' => $data['imageId']
        ]));

        $project_id = $this->projectsProvider->insert($data);

        wp_redirect('/wp-admin/admin.php?page=sefab-projects&add=success&id=' . $project_id);
        die();
    }

    public function add_menu_items()
    {
        if (current_user_can('administrator') || current_user_can('lower_administrator') || current_user_can('super_administrator')) {
            $page_title = 'Projects';
            $menu_title = 'Projects';
            $capability = 'read';
            $menu_slug = 'sefab-projects';
            $function = [$this, 'display_projects_page'];
            $icon_url = 'dashicons-admin-multisite';
            $position = 5;
            add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position);
        }
    }

    public function register_scripts($hook)
    {
        if ($hook === 'toplevel_page_sefab-projects') {
            $plugin_url = plugin_dir_url(plugin_dir_url(__FILE__));
            wp_enqueue_style('datatable-styles', 'https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css');
            wp_enqueue_script('datatable-scripts', 'https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js');

            wp_enqueue_style('bootstrap-grid-styles', plugins_url('../resources/bootstrap/css/bootstrap-grid.min.css', __FILE__));
            wp_enqueue_style('bootstrap-reboot-styles', plugins_url('../resources/bootstrap/css/bootstrap-reboot.min.css', __FILE__));
            wp_enqueue_style('bootstrap-styles', plugins_url('../resources/bootstrap/css/bootstrap.min.css', __FILE__));

            wp_enqueue_style('sefab-dashboard-styles', plugins_url('../resources/styles/dashboard-styles.css', __FILE__));
            wp_enqueue_script('sefab-statistics-canvas-js-script', plugins_url('../resources/scripts/chart.min.js', __FILE__));
            wp_enqueue_script('sefab-projects', plugins_url('../resources/scripts/projects.js', __FILE__));
            wp_enqueue_script('sefab-projects-geocomplete', plugins_url('../resources/scripts/jquery.geocomplete.min.js', __FILE__));

        }
    }

    public function register_actions()
    {
        //Add actions here
        add_action('admin_enqueue_scripts', [$this, 'register_scripts']);
    }
}
