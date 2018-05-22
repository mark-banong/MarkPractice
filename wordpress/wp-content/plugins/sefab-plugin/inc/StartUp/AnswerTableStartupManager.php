<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\StartUp;
use \Inc\Managers\DbManager;

class AnswerTableStartUpManager
{
    private $environment;

    public function __construct($environment) {
        $this->environment = $environment;
    }

    public function register(){
        $answer_table = $this->environment->answerTable;
        $table_exist = DbManager::select( 1, $answer_table['tableName'], '1 = 1', 'LIMIT 1' );
      
        if( $table_exist ){
            //check columns
        }
        else{
            //create table
            $createResult = DbManager::create( $answer_table['tableName'], $answer_table['columns'] );
        }
    
    }
}

?>