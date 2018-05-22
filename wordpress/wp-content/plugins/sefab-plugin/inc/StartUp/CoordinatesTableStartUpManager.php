<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\StartUp;
use \Inc\Managers\DbManager;

class CoordinatesTableStartUpManager
{
    private $environment;

    public function __construct($environment) {
        $this->environment = $environment;
    }
 
    public function register(){
        
        $coordinates_table = $this->environment->coordinatesTable;
        $table_exist = DbManager::select( 1, $coordinates_table['tableName'], '1 = 1', 'LIMIT 1' );
      
        if( $table_exist ){
            //check columns
        }
        else{
            //create table
            $createResult = DbManager::create( $coordinates_table['tableName'], $coordinates_table['columns'] );
        }
    
    }
}

?>