<?php
/**
 * @package Sefab Plugin
 */
namespace Inc;
 
use Inc\Managers\RegistrationManager;
use Inc\Providers\GeoLocationProvider;
use Inc\Services\FileService;
use Inc\Services\ExcelService;
use Inc\Services\CsvService;
use Inc\Services\SmsService;
use Inc\Services\LogService;
use Inc\Managers\DbManager;
use Inc\Services\Loader;
use Inc\Services\ValidationService; 

class Authentication
{
    private $geoLocationManager;
    private $registrationManager;
    private $excelService;
    private $fileService;
    private $environment;
    private $csvService;
    private $smsService;
    private $logService;
    private $dbManager;
    private $loader;
    private $validationService;

    public function __construct ($environment) {
        $this->environment = $environment;

        $this->load_dependencies();
        $this->register_hooks();
    }

    public function run () {
        $this->loader->run();
    }

    public function get_registration_manager () {
        return $this->registrationManager;
    }

    private function load_dependencies () {
        $this->dbManager = new DbManager();
        $this->loader = new Loader();
        $this->logService = new LogService($this->environment);

        $this->smsService = new SmsService($this->environment, $this->logService);
        $this->geoLocationProvider = new GeoLocationProvider($this->environment);
        $this->fileService = new FileService($this->environment, $this->dbManager, $this->logService);
        $this->excelService = new ExcelService($this->dbManager, $this->logService, $this->environment);  
        $this->csvService = new CsvService($this->dbManager, $this->logService); 
        $this->validationService = new ValidationService($this->logService);
        $this->registrationManager = new RegistrationManager($this->environment, $this->dbManager, $this->smsService, $this->geoLocationProvider, $this->fileService, $this->logService, $this->excelService, $this->csvService,  $this->validationService);
    }
    
    private function register_hooks () {
        $this->loader->add_action('init', $this->registrationManager, 'register_actions');

        $this->loader->add_action('admin_menu', $this->registrationManager, 'add_menu_items');
    }
}

?>