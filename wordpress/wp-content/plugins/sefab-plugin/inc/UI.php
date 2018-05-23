<?php
/**
 * @package Sefab Plugin
 */
namespace Inc;

use Inc\Services\EmailContentBuilderService;
use Inc\Managers\ShortCodeManager;
use Inc\Providers\FormProvider;
use Inc\Services\EmailService;
use Inc\Managers\FormManager;
use Inc\Services\LogService;
use Inc\Managers\DbManager;
use Inc\Services\Loader;

class UI
{
    private $emailContentBuilderService;
    private $shortCodeManager;
    private $emailService;
    private $formProvider;
    private $formManager;
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

    public function get_form_manager()
    {
        return $this->formManager;
    }

    private function load_dependencies()
    {
        $this->dbManager = new DbManager();
        $this->loader = new Loader();
        $this->logService = new LogService($this->environment);
        $this->formProvider = new FormProvider($this->dbManager);
        $this->emailService = new EmailService($this->environment, $this->logService);
        $this->emailContentBuilderService = new EmailContentBuilderService($this->environment);

        $this->shortCodeManager = new ShortCodeManager($this->dbManager, $this->environment);
        $this->formManager = new FormManager($this->dbManager, $this->environment, $this->emailService, $this->emailContentBuilderService, $this->formProvider);
    }
    
    private function register_hooks()
    {
        $this->loader->add_action('init', $this->formManager, 'register_actions');
        $this->loader->add_action('init', $this->shortCodeManager, 'register_actions');
    }
}
