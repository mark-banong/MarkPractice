<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\StartUp;
use \Inc\Managers\DbManager;

class ParagraphTableStartUpManager
{
    private $environment;

    public function __construct($environment) {
        $this->environment = $environment;
    }
 
    public function register(){
        $paragraph_table = $this->environment->paragraphTable;
        $table_exist = DbManager::select( 1, $paragraph_table['tableName'], '1 = 1', 'LIMIT 1' );
      
        if( $table_exist ){
            //check columns
        }
        else{
            //create table
            $createResult = DbManager::create( $paragraph_table['tableName'], $paragraph_table['columns'] );
        }
    
    }
}

?>