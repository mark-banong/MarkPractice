<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\StartUp;
use \Inc\Managers\DbManager;

class QuestionTableStartUpManager
{
    private $environment;

    public function __construct($environment) {
        $this->environment = $environment;
    }
 
    public function register(){
        
        $question_table = $this->environment->questionTable;
        $table_exist = DbManager::select( 1, $question_table['tableName'], '1 = 1', 'LIMIT 1' );
      
        if( $table_exist ){
            //check columns
        }
        else{
            //create table
            $createResult = DbManager::create( $question_table['tableName'], $question_table['columns'] );
        }
    
    }
}

?>