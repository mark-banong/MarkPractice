<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Providers;

class ReportProvider
{
    private $emailContentBuilder;
    private $emailService;
    private $environment;
    private $dbManager;

    public function __construct($db_manager, $environment, $email_content_builder, $email_service)
    {
        $this->emailContentBuilder = $email_content_builder;
        $this->emailService = $email_service;
        $this->environment = $environment;
        $this->dbManager = $db_manager;
    }

    public function submit_report($data)
    {
        $user_id = 0;

        //Create Content

        if ($this->environment->useLocalSettings) { 
            $to = "jamie.vanstone@mllrdev.com";
        } else {
            $to = "anestis.nikolaidis@sefabbygg.se";
        }
        
        $header = "Sefapp Report";
        $body = $this->emailContentBuilder->build_report_email($data);
        $subject = "Report: " . $data['error'];

        //Insert to database
        $this->dbManager->insert('sefab_email', [
            'receiver_email_address' => $to,
            'user_id' => $user_id,
            'subject' => $subject,
            'content' => $body,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);

        //Send email
        if ($this->environment->isEmailEnabled && $this->environment->isReportEmailsEnabled) {
            $this->emailService->send($to, $header, $body, $subject);
        }

        return [
            'code' => 200, 
            'message' => 'success', 
            'data' => []
        ];
    }
}
