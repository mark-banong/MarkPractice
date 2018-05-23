<?php
/**
 * @package Sefab Plugin
 */
namespace Inc;
 
use Inc\Managers\StatisticsDashboardManager;
use Inc\Managers\ReadManager;
use Inc\Services\LogService;
use Inc\Managers\DbManager; 
use Inc\Services\Loader;

class Statistics
{
    private $statisticsDashboardManager;
    private $readManager;    
    private $environment;
    private $dbManager;
    private $loader;
    private $ui;

    public function __construct ($environment, $ui) {
        $this->environment = $environment;
        $this->ui = $ui;

        $this->load_dependencies();
        $this->register_hooks();
    }

    public function run () {
        $this->loader->run();
    }

    private function load_dependencies () {
        $this->dbManager = new DbManager();
        $this->loader = new Loader();       

        $this->logService = new LogService($this->environment);
        $this->readManager = new ReadManager($this->dbManager, $this->environment, $this->logService);
        $this->statisticsDashboardManager = new StatisticsDashboardManager($this->dbManager, $this->environment, $this->readManager, $this->ui->get_form_manager());
    }
    
    private function register_hooks () {
        $this->loader->add_action('init', $this->readManager, 'register_actions');

        $this->loader->add_action('init', $this->statisticsDashboardManager, 'register_actions');
        $this->loader->add_action('admin_menu', $this->statisticsDashboardManager, 'add_menu_items');
    }

    public function get_read_manager () {
        return $this->readManager;
    }
}

?>