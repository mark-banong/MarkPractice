<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Providers;

class CoordinatesProvider
{
    private $environment;
    private $logService;
    private $dbManager;
    public function __construct($db_manager, $environment, $log_service)
    {
        $this->environment = $environment;
        $this->logService = $log_service;
        $this->dbManager = $db_manager;
    }

    public function insert ($data) {
        $this->logService->Log(
            "coordinate_service_log", 
            json_encode([
                'method' => 'insert', 
                'latitude' => $data['lat'], 
                'longitude' => $data['lng'],
                'timestamp' => date('Y-m-d H:i:s')
            ])
        );

        return $this->dbManager->insert('sefab_coordinates', [
            'latitude' => $data['lat'],
            'longitude' => $data['lng'],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    public function get_by_id($id) {
        return $this->dbManager->select('*', 'sefab_coordinates', "id = $id");
    }

}
