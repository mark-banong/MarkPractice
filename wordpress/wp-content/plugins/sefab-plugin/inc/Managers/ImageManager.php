<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Managers;

class ImageManager
{
    private $fileService;
    private $environment;
    private $logService;
    private $dbManager;

    public function __construct($environment, $db_manager, $log_service, $file_service)
    {
        $this->fileService = $file_service;
        $this->environment = $environment;
        $this->logService = $log_service;
        $this->dbManager = $db_manager;

        require_once $this->environment->pluginPath . "vendor/autoload.php";
    }
   

    public function upload_image($data) {
        echo json_decode('<script type="text/javascript">
            alert("HI");
            </script>')
        ;
      

        return $this->fileService->upload($_FILES['image'], ['png', 'PNG', 'jpg', 'jpeg', 'JPG', 'JPEG']);
    }

    public function register_actions()
    {
        //Add actions here
        // add_action('admin_enqueue_scripts', [$this, 'register_scripts']);
    }
}
