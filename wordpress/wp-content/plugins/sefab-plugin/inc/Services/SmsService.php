<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Services;

use Twilio\Rest\Client as SmsClient;


class SmsService {
    
    private $environment;
    private $logService;
    public function __construct($environment, $log_service) 
    {   
        $this->environment = $environment;
        $this->logService = $log_service;
        require_once $this->environment->pluginPath . "vendor/autoload.php"; 
    }
    
    public function send_verification_code($client_phone_number, $code) {
        $this->logService->log('sms_log', json_encode(['phoneNumber' => $client_phone_number, 'code' => $code]));
        
        if (!$this->environment->isSmsEnabled) return false;
        
        $client = new SmsClient($this->environment->smsAccountSid, $this->environment->smsAuthToken);
       
        $client->messages->create(
            $client_phone_number,
            [
                "from" => $this->environment->smsPhoneNumber,
                "body" => "Your verification code is $code."
            ]
        );
    }
}
?>