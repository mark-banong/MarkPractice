<?php
/**
 * @package Sefab Plugin
 */
namespace Inc;

class Initializer  
{   
    public $someVar = "SOME VAL";

    /** 
     * Store all the classes inside an array
     * @return array Full list of classes
     */
    public function get_services(){
        return [
            StartUp\PostTableStartUpManager ::class,
            StartUp\ParagraphTableStartUpManager ::class,
            StartUp\FormTableStartUpManager ::class,
            StartUp\QuestionTableStartUpManager ::class,
            StartUp\OptionTableStartUpManager ::class,
            StartUp\VerificationCodeTableStartUpManager ::class,
            StartUp\AnswerTableStartUpManager ::class,
            StartUp\PostNotificationTableStartUpManager ::class,
            StartUp\PostViewTrackerStartUpManager ::class,
            StartUp\EmailTableStartUpManager::class,
            StartUp\CoordinatesTableStartUpManager::class,
            StartUp\ProjectsTableStartUpManager::class,
            StartUp\FileTableStartUpManager::class,
        ]; 
    }
    /**
    * Loop through the classes, initialize them, and
    * call the register() method if it exists
    */
    public function register_services ($environment){
        foreach ( $this->get_services() as $class ) {
            $service = $this->instantiate( $class, $environment );
            if ( method_exists( $service, 'register' ) ) {
                $service->register();
            }
        }  
    }
    /*
    * Initialize the class
    * @param class $class class from the services array
    * @return class instance new instance of the class 
    */
    private function instantiate( $class, $environment ){
        $service = new $class($environment);
        return $service;
    }
}