<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Services;

 class CsvService
{
    private $dbManager;
    private $logService;

    public function __construct($db_manager, $log_service){
        $this->dbManager = $db_manager;
        $this->logService = $log_service;
    }

    public function file_path( $file_id ) {
        $path =  $this->dbManager->select('dir', 'sefab_files', "id=$file_id");   
        
        return $path[0]->dir;   
    
      } 
      
      
    public function read( $file_id ){
        
        $path    = $this->file_path( $file_id );
        $warning = NULL;
        $data    = array();
         

        $row = 1;
        $this->logService->Log('csv_service_log', $file_id);

        if (($csv_file = fopen($path, "r")) !== FALSE) {
           
            while( !feof($csv_file)){
                array_push($data, fgetcsv($csv_file, 1000));
            }
            fclose($csv_file);
        }
        
        $this->logService->Log('csv_service_log', json_encode($data));
        return $data;
    }
    
}