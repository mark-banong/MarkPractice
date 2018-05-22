<?php
/**
 * @package Sefab Plugin
 */

namespace Inc\Services;

class FileService
{
    private $environment;
    private $logService;
    private $dbManager;

    public function __construct($environment, $db_manager, $log_service)
    {
        $this->environment = $environment;
        $this->logService = $log_service;
        $this->dbManager = $db_manager;
    }

    public function insert($file, $file_path)
    {
        $this->logService->Log('file_upload_logs', json_encode($files));

        $last_id = $this->dbManager->insert('sefab_files', array(
            "name" => $file['name'],
            "type" => pathinfo($file_path, PATHINFO_EXTENSION),
            "size" => $file['size'],
            "timestamp" => date("Y-m-d H:i:s"),
            "unique_name" => $file['name'].uniqid(),
            "dir" => $file_path,
            
        ));

        return [
            'file_id' => $last_id,
        ];
    }

    public function get_by_id($id) {
        return $this->dbManager->select("*", "sefab_files", "id = $id");
    }

    public function upload($files, $accepted_types)
    {
        $return_data = array();
       
        $arranged_files = $this->rearrange($files);
       
        $upload_dir = trailingslashit( plugin_dir_path( dirname( __FILE__ ) ) ) . 'resources/uploads/';
        wp_mkdir_p($upload_dir);

        foreach($arranged_files as $file) {
      
            $file_name = $file['name'];
            $file_tmp = $file['tmp_name'];
            $file_path = $upload_dir . basename($file_name);            
            $error = null;
            $file_type = strtolower(pathinfo($file_path,PATHINFO_EXTENSION));
 
            if($file_name == NULL){
                $return_data= array(
                    'error' => 'Select-a-file'
                );
            }
            else if(!(in_array($file_type, $accepted_types)) && $file_name !== NULL){
                $return_data = array(
                    'error' => 'Invalid-format'
                );
              
            }                        
            else {
                move_uploaded_file($file_tmp, $file_path);
                $return_data[] = $this->insert($file,  $file_path);                                    
            }            
        }
        return $return_data;
    }

    private function rearrange($arr)
    {
        foreach ($arr as $key => $all) {
            foreach ($all as $i => $val) {
                $new[$i][$key] = $val;
            }
        }
        return $new;
    }
}
