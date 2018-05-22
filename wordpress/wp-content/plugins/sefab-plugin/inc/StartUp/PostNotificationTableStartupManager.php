<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\StartUp;
use \Inc\Managers\DbManager;

class PostNotificationTableStartUpManager 
{
    private $environment;

    public function __construct($environment) {
        $this->environment = $environment;
    }
 
    public function register(){
        $post_notification_detail_table = $this->environment->postNotificationDetailTable;
        $table_exist = DbManager::select( 1, $post_notification_detail_table['tableName'], '1 = 1', 'LIMIT 1' );
      
        if( $table_exist ){
            //check columns
        }
        else{
            //create table
            $createResult = DbManager::create( $post_notification_detail_table['tableName'], $post_notification_detail_table['columns'] );
        }
    
    }
}

?>