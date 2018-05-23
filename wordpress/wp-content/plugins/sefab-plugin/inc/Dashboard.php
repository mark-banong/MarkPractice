<?php
/**
 * @package Sefab Plugin
 */
namespace Inc;

use Inc\Providers\CoordinatesProvider;
use Inc\Providers\ProjectsProvider;

use Inc\Services\FileService;
use Inc\Services\LogService;
use Inc\Services\Loader;

use Inc\Managers\AdminDashboardManager;
use Inc\Managers\RoleLimiterManager;
use Inc\Managers\ProjectsManager;
use Inc\Managers\DbManager;

class Dashboard
{
    private $adminDashboardManager;
    private $coordinatesProvider;
    private $roleLimiterManager;
    private $projectsProvider;
    private $projectsManager;
    private $environment;
    private $logService;
    private $dbManager;
    private $loader;

    public function __construct($environment)
    {
        $this->environment = $environment;
        $this->load_dependencies();
        $this->register_hooks();
    }

    public function run()
    {
        $this->loader->run();
    }

    private function load_dependencies()
    {
        $this->dbManager = new DbManager();
        $this->loader = new Loader();   

        $this->logService = new LogService($this->environment);
        $this->fileService = new FileService($this->environment, $this->dbManager, $this->logService);
        $this->coordinatesProvider = new CoordinatesProvider($this->dbManager, $this->environment, $this->logService);
        $this->projectsProvider = new ProjectsProvider($this->dbManager, $this->environment, $this->logService, $this->coordinatesProvider, $this->fileService);
        $this->projectsManager = new ProjectsManager($this->dbManager, $this->environment, $this->logService, $this->projectsProvider);
        $this->adminDashboardManager = new AdminDashboardManager();
        $this->roleLimiterManager = new RoleLimiterManager($this->dbManager);
    }
    
    private function register_hooks()
    {
        $this->loader->add_action('init', $this->adminDashboardManager, 'register_actions');
        $this->loader->add_action('init', $this->roleLimiterManager, 'register_actions');
        $this->loader->add_action('init', $this->projectsManager, 'register_actions');
        
        $this->loader->add_action('admin_menu', $this->projectsManager, 'add_menu_items');
    }
}
