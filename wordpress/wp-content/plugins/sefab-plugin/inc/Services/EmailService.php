<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Services;

class EmailService
{
    private $environment;
    private $logService;

    public function __construct($environment, $log_service)
    {
        $this->environment = $environment;
        $this->logService = $log_service;

        require_once $this->environment->pluginPath . "vendor/autoload.php";
    }

    public function send($to, $header, $body, $subject)
    {
        $this->logService->log('email_log', "Send Email:");
        $this->logService->log('email_log', json_encode(['to' => $to, 'header' => $header, 'subject' => $subject]));

        try {
            if ($this->environment->useLocalSettings) {
                $transport = (new \Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))->setUsername('jamiejoevanstonemllrdev@gmail.com')->setPassword('devpassword');
            } else {
                $transport = new \Swift_SmtpTransport('localhost', 25);
            }

            $this->logService->log('email_log', json_encode(['transport' => $transport]));

            $mailer = (new \Swift_Mailer($transport));
            $message = (new \Swift_Message($subject))
                ->setFrom(array('no_reply@sefabbygg.com' => $header))
                ->setTo(array($to => 'no_reply@sefabbygg.com'))
                ->setBody($body, 'text/html');
            $result = $mailer->send($message, $errors);
        } catch (Exception $e) {
            $this->logService->log('email_log', "Error occured");
            $this->logService->log('email_log', $e->getMessage());
        }
        $this->logService->log('email_log', json_encode($result));
    }
}
