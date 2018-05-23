<?php
/**
 * @package Sefab Plugin
 */
namespace Inc;

use Inc\Providers\CoordinatesProvider;
use Inc\Providers\ProjectsProvider;
use Inc\Providers\AnswerProvider;
use Inc\Providers\PolicyProvider;
use Inc\Providers\ReportProvider;

use Inc\Managers\EndpointManager;
use Inc\Managers\ProjectsManager;
use Inc\Managers\ImageManager;
use Inc\Managers\DbManager;

use Inc\Services\EmailContentBuilderService;
use Inc\Services\NotificationService;
use Inc\Services\FormDataBuilder;
use Inc\Services\EmailService;
use Inc\Services\FileService;
use Inc\Services\LogService;
use Inc\Services\Loader;

class Api
{
    private $coordinatesProvider;
    private $notificationService;
    private $projectsProvider;
    private $projectsManager;
    private $endpointManager;
    private $formDataBuilder;
    private $answerProvider;
    private $authentication;
    private $policyProvider;
    private $reportProvider;
    private $imageManager;
    private $emailBuilder;
    private $fileService;
    private $environment;
    private $logService;
    private $statistics;
    private $dbManager;
    private $loader;
    private $ui;

    public function __construct($environment, $authentication, $ui, $statistics)
    {
        $this->authentication = $authentication;
        $this->environment = $environment;
        $this->statistics = $statistics;
        $this->ui = $ui;

        $this->load_dependencies();
        $this->register_hooks();
    }

    public function run()
    {
        $this->loader->run();
    }

    public function get_policy_provider()
    {
        return $this->policyProvider;
    }

    public function get_answer_provider()
    {
        return $this->answerProvider;
    }

    private function load_dependencies()
    {
        $this->loader = new Loader();
        $this->dbManager = new DbManager();

        $this->logService = new LogService($this->environment);
        $this->fileService = new FileService($this->environment, $this->dbManager, $this->logService);
        $this->emailService = new EmailService($this->environment, $this->logService);
        $this->emailBuilder = new EmailContentBuilderService($this->environment);
        $this->formDataBuilder = new FormDataBuilder();
        $this->notificationService = new NotificationService($this->environment);

        $this->coordinatesProvider = new CoordinatesProvider($this->dbManager, $this->environment, $this->logService);
        $this->projectsProvider = new ProjectsProvider($this->dbManager, $this->environment, $this->logService, $this->coordinatesProvider, $this->fileService);
        $this->policyProvider = new PolicyProvider($this->dbManager);
        $this->answerProvider = new AnswerProvider($this->environment, $this->dbManager);
        $this->reportProvider = new ReportProvider($this->dbManager, $this->environment, $this->emailBuilder, $this->emailService);
        
        $this->projectsManager = new ProjectsManager($this->dbManager, $this->environment, $this->logService, $this->projectsProvider);
        $this->imageManager = new ImageManager($this->environment, $this->dbManager, $this->logService, $this->fileService);
        $this->endpointManager = new EndpointManager($this->environment, $this->get_policy_provider(), $this->get_answer_provider(), $this->authentication->get_registration_manager(), $this->ui->get_form_manager(), $this->formDataBuilder, $this->statistics->get_read_manager(), $this->notificationService, $this->reportProvider, $this->projectsManager, $this->imageManager);
    }

    private function register_hooks()
    {
        $this->loader->add_action('rest_api_init', $this->endpointManager, 'register_routes');
    }
}
