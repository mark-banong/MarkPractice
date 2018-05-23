<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\StartUp;
use \Inc\Managers\DbManager;

class FileTableStartUpManager
{
    private $environment;

    public function __construct($environment) {
        $this->environment = $environment;
    }

    public function register(){
        $table = $this->environment->fileTable;
        $table_exist = DbManager::select( 1, $table['tableName'], '1 = 1', 'LIMIT 1' );
    
        if( $table_exist ){
            //check columns
        }
        else{
            //create table
            $createResult = DbManager::create( $table['tableName'], $table['columns'] );
        }
    }
}
?>