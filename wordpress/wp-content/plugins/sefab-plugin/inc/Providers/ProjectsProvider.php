<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Providers;

class ProjectsProvider
{
    private $coordinatesProvider;
    private $fileService;
    private $environment;
    private $logService;
    private $dbManager;
    public function __construct($db_manager, $environment, $log_service, $coordinates_provider, $file_service)
    {
        $this->coordinatesProvider = $coordinates_provider;
        $this->fileService = $file_service;
        $this->environment = $environment;
        $this->logSerivce = $log_service;
        $this->dbManager = $db_manager;
    }

    public function delete($project_id)
    {
        return $this->dbManager->update('sefab_projects', 'is_deleted = 1', "id = $project_id");
    }

    public function get_all()
    {

        $return_data = [];
        $projects = $this->dbManager->select('*', 'sefab_projects', 'is_deleted = 0', 'ORDER BY timestamp DESC');

        foreach ($projects as $project) {
            $project->coordinates = $this->coordinatesProvider->get_by_id($project->coordinates_id)[0];

            $image_result = $this->fileService->get_by_id($project->image_id);
            $project->image = (count($image_result) > 0) ? $image_result[0] : null ;

            $return_data[] = $project;
        }

        return $return_data;
    }

    public function get_by_id($id)
    {

        $project = $this->dbManager->select('*', 'sefab_projects', "id = $id")[0];

        $project->coordinates = $this->coordinatesProvider->get_by_id($project->coordinates_id)[0];
        $image_result = $this->fileService->get_by_id($project->image_id);
        $project->image = (count($image_result) > 0) ? $image_result[0] : null ;

        return $project;
    }

    public function insert($data)
    {
        //Insert Coordinates to get coordinates id
        $coordinates_id = $this->coordinatesProvider->insert($data);

        return $this->dbManager->insert('sefab_projects', [
            'coordinates_id' => $coordinates_id,
            'image_id' => $data['imageId'],
            'name' => $data['name'],
            'description' => $data['description'],
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }
}
