<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Services;

class NotificationService
{
    private $environment;

    public function __construct($environment)
    {
        $this->environment = $environment;
    }

    public function Notify($title, $message, $data = [])
    {
        if (!$this->environment->isPushNotificationsEnabled) return false;

        $content = [
            "en" => $message
        ];
        
        $fields = [
            'app_id' => $this->environment->pushAppId,
            'included_segments' => ['All'],
            'data' => ["post" => $data],
            'contents' => $content,
            'headings' => [
                'en' => $title
            ],
            'small_icon' => 'ic_stat_onesignal_default',
            'android_group' => 'sefapp',
            'android_group_message' => array("en" => 'You have $[notif_count] new updates.'),
            'adm_group' => 'sefapp',
            'adm_group_message' => ["en" => 'You have $[notif_count] new updates.']
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->environment->pushApiUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                                                   'Authorization: Basic ' . $this->environment->pushKey));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
