<?php
/**
 * @package Sefab Plugin
 */
namespace Inc;

use Inc\Services\Loader;
use Inc\Managers\DbManager;
use Inc\Managers\PolicyManager;
use Inc\Services\FormParser;
use Inc\Services\Html;
use Inc\Services\NotificationService;
use Inc\Services\OptionParser;
use Inc\Services\ParagraphParser;
use Inc\Services\PolicyParser;
use Inc\Services\QuestionParser;
use Inc\Services\LogService;

class HtmlParser
{
    private $paragraphParser;
    private $questionParser;
    private $policyManager;
    private $optionParser;
    private $policyParser;
    private $environment;
    private $logService;
    private $formParser;
    private $dbManager;
    private $loader;
    private $html;
    private $api;

    public function __construct($environment, $api)
    {
        $this->environment = $environment;
        $this->api = $api;
        $this->load_dependencies();
        $this->register_hooks();
    }

    public function run()
    {
        $this->loader->run();
    }

    private function load_dependencies()
    {
        //Order Matters
        $this->dbManager = new DbManager();
        $this->html = new Html();
        $this->logService = new LogService($this->environment);

        $this->paragraphParser = new ParagraphParser($this->dbManager);
        $this->optionParser = new OptionParser();

        $this->questionParser = new QuestionParser($this->html);
        $this->formParser = new FormParser();

        $this->policyParser = new PolicyParser($this->logService);
        $this->policyManager = new PolicyManager($this->dbManager, $this->policyParser, $this->paragraphParser, $this->formParser, $this->questionParser, $this->optionParser, $this->api->get_policy_provider(), $this->environment, $this->logService);
        $this->loader = new Loader();
    }

    private function register_hooks()
    {
        $this->loader->add_action('init', $this->policyManager, 'register_actions');
    }
}
