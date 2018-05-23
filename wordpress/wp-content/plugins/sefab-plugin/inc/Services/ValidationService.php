<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Services;

class ValidationService {
    
    private $logService;
    
    public function __construct( $log_service ) 
    {   
        $this->logService = $log_service;  
    }
    
    public function validate_user_csv( $data )
    {
        $validate_data = [];
        $warning       = NULL;
        $row           = 2;
        
      
        $this->logService->Log('validation_service_log', json_encode($data).'mark'); 
        for($numberOfUser = 1; $numberOfUser < count($data); $numberOfUser++){
            if($numberOfUser !== count($data)-1){
                $surname    = $data[$numberOfUser][0];
                $given_name = $data[$numberOfUser][1];
                $mobile     = $data[$numberOfUser][2];
            
             
                if($surname === ""){
                    $warning = '<br>Empty surname in row '. $row.' '.'</br>';
                }
            
                if($given_name === ""){
                    $warning = $warning.'<br>Empty given name in row '. $row.' '.'</br>';
                }

                if(!(preg_match('/^0\d{9}$/', $mobile)) && !(preg_match('/^\+46\d{9}$/',$mobile))){
                     
                    $warning = $warning.'<br>Invalid  mobile number in row '. $row.'</br>';
                }

                if (!(preg_match('/^\+46\d{9}$/',$mobile))){
                    $mobile = preg_replace('/^\+46(\d{9})$/','0$1',$mobile);
                }

                array_push($validate_data,
                    array(
                        "surname"    => $surname,
                        "given-name" => $given_name,
                        "mobile"     => $mobile,
                        "warning"    => isset($warning)?$warning:'' 
                ));

                $warning = NULL;
                $row++;
            }
        }
         
        return $validate_data;
      
    }
     
}
?>