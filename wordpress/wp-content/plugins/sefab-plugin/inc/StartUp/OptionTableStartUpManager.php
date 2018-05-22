<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\StartUp;
use \Inc\Managers\DbManager;

class OptionTableStartUpManager
{
    private $environment;

    public function __construct($environment) {
        $this->environment = $environment;
    }

    public function register(){
        $option_table = $this->environment->optionTable;
        $table_exist = DbManager::select( 1, $option_table['tableName'], '1 = 1', 'LIMIT 1' );
      
        if( $table_exist ){
            //check columns
        }
        else{
            //create table
            $createResult = DbManager::create( $option_table['tableName'], $option_table['columns'] );
        }
    
    }
}

?>