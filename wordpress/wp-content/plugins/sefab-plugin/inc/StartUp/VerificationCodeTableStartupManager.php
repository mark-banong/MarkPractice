<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\StartUp;
use \Inc\Managers\DbManager;

class VerificationCodeTableStartUpManager
{
    private $environment;

    public function __construct($environment) {
        $this->environment = $environment;
    }
 
    public function register(){
        $verification_code_table = $this->environment->verificationCodeTable;
        $table_exist = DbManager::select( 1, $verification_code_table['tableName'], '1 = 1', 'LIMIT 1' );
      
        if( $table_exist ){
            //check columns
        }
        else{
            //create table
            $createResult = DbManager::create( $verification_code_table['tableName'], $verification_code_table['columns'] );
        }
    
    }
}

?>