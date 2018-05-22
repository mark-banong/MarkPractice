<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Services;

$plugin_dir = trailingslashit( plugin_dir_path( dirname( __FILE__ ) ) );

require $plugin_dir .'../vendor/autoload.php';
use \PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx; 
use \PhpOffice\PhpSpreadsheet\Writer\Csv;

class ExcelService
{
   private $dbManager;
   private $logService;
   private $environment; 
   
   public function __construct($db_manager, $log_service, $environment){
      $this->dbManager = $db_manager;
      $this->logService = $log_service;
      $this->environment = $environment;
   }
   
   public function file_path( $file_id ) {
      $path =  $this->dbManager->select('dir', 'sefab_files', "id=$file_id");   
      return $path[0]->dir;   
    } 

    public function convert_to_csv( $file_id )
    {
        $upload_dir = $this->environment->pluginPath.'inc/resources/uploads/';
        $reader     = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $path = $this->file_path( $file_id );
        $spreadsheet = $reader->load($path); 
        $sheet_data = $spreadsheet->getActiveSheet()->toArray();    
        $loaded_sheet_names = $spreadsheet->getSheetNames();
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
  
        $csv_file_extension = '.csv';

        foreach($loaded_sheet_names as $sheet_index => $loaded_sheet_names) {
            
            $writer->setSheetIndex($sheet_index);
            $writer->save($upload_dir.$loaded_sheet_names.$csv_file_extension);
            $saved_file =$upload_dir.$loaded_sheet_names.$csv_file_extension;
            
        }     
        $converted_file = $upload_dir.$loaded_sheet_names.$csv_file_extension;
                
        return [
          'name' => $loaded_sheet_names,
          'size' => filesize($converted_file),
          'dir'  => $converted_file
        ];
    }
}