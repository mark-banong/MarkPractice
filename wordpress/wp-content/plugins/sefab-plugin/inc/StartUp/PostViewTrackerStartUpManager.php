<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\StartUp;
use \Inc\Managers\DbManager;

class PostViewTrackerStartUpManager
{
 
    private $environment;

    public function __construct($environment) {
        $this->environment = $environment;
    }

    public function register(){
        
        $view_tracker_table = $this->environment->viewTrackerTable;
        
        $table_exist = DbManager::select( 1, $view_tracker_table['tableName'], '1 = 1', 'LIMIT 1' );
      
        if( $table_exist ){
            //check columns
        }
        else{
            //create table
            $createResult = DbManager::create( $view_tracker_table['tableName'], $view_tracker_table['columns'] );
        }
    
    }
}

?>