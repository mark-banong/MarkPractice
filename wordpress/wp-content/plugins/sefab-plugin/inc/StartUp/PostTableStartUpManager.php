<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\StartUp;
use \Inc\Managers\DbManager;

class PostTableStartUpManager
{
    private $environment;

    public function __construct($environment) {
        $this->environment = $environment;
    }

    public function register(){
        $postTable = $this->environment->postTable;
        $tableExist= DbManager::select( 1, $postTable['tableName'], '1 = 1', 'LIMIT 1' );

        if( $tableExist ){
            //check columns
        }
        else{
            //create table
            $createResult = DbManager::create( $postTable['tableName'], $postTable['columns'] );
        }
    }
}

?>