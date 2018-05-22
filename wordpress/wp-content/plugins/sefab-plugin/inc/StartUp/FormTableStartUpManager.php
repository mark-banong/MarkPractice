<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\StartUp;
use \Inc\Managers\DbManager;

class FormTableStartUpManager
{
    private $environment;

    public function __construct($environment) {
        $this->environment = $environment;
    }

    public function register(){
        $form_table = $this->environment->formTable;
        $table_exist = DbManager::select( 1, $form_table['tableName'], '1 = 1', 'LIMIT 1' );
      
        if( $table_exist ){
            //check columns
        }
        else{
            //create table
            $createResult = DbManager::create( $form_table['tableName'], $form_table['columns'] );
        }
    
    }
}

?>