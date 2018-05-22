<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\StartUp;
use \Inc\Managers\DbManager;

class ProjectsTableStartUpManager
{
    private $environment;

    public function __construct($environment) {
        $this->environment = $environment;
    }
 
    public function register(){
        
        $projects_table = $this->environment->projectsTable;
        $table_exist = DbManager::select( 1, $projects_table['tableName'], '1 = 1', 'LIMIT 1' );
      
        if( $table_exist ){
            //check columns
        }
        else{
            //create table
            $createResult = DbManager::create( $projects_table['tableName'], $projects_table['columns'] );
        }
    
    }
}

?>