<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Managers;

class EndpointManager
{
    private $notificationService;
    private $registrationManager;
    private $projectsManager;
    private $formDataBuilder;
    private $answerProvider;
    private $policyProvider;
    private $reportProvider;
    private $imageManager;
    private $formManager;
    private $readManager;
    private $environment;
    
    public function __construct($environment, $policy_provider, $answer_provider, $registration_manager, $form_manager, $form_data_builder, $read_manager, $notification_service, $report_provider, $projects_manager, $image_manager)
    {
        $this->environment = $environment;

        require_once $this->environment->pluginPath . "vendor/autoload.php";
        
        $this->notificationService = $notification_service;
        $this->registrationManager = $registration_manager;
        $this->formDataBuilder = $form_data_builder;
        $this->projectsManager = $projects_manager;
        $this->answerProvider = $answer_provider;
        $this->policyProvider = $policy_provider;
        $this->reportProvider = $report_provider;
        $this->imageManager= $image_manager;
        $this->formManager = $form_manager;
        $this->readManager = $read_manager;
    }
    
    public function register_routes()
    {   
        register_rest_route($this->environment->sefabRoute, '/projects', [
            'methods' => 'GET',
            'callback' => [$this->projectsManager, 'get_all']
        ]);

        register_rest_route($this->environment->sefabRoute, '/project-by-id/(?P<projectId>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_project_by_id']
        ]);

        register_rest_route($this->environment->sefabRoute, '/insert-project-test', [
            'methods' => 'GET',
            'callback' => [$this->projectsManager, 'add_test']
        ]);

        register_rest_route($this->environment->sefabRoute, '/policy-by-id/(?P<policyId>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_policy_by_id']
        ]);

        register_rest_route($this->environment->sefabRoute, '/import-users-file', [
            'methods' => 'POST',
            'callback' => [$this->registrationManager, 'import_file']
        ]);

        register_rest_route($this->environment->sefabRoute, '/insert-users-from-file', [
            'methods' => 'POST',
            'callback' => [$this->registrationManager, 'insert_users_from_file']
        ]);
        

        register_rest_route($this->environment->sefabRoute, '/upload-image', [
            'methods' => 'POST',
            'callback' => [$this->imageManager, 'upload_image']
        ]);

        register_rest_route($this->environment->sefabRoute, '/project-delete', [
            'methods' => 'POST',
            'callback' => [$this->projectsManager, 'delete']
        ]);

        register_rest_route($this->environment->sefabRoute, '/project-add', [
            'methods' => 'POST',
            'callback' => [$this->projectsManager, 'add']
        ]);

        register_rest_route($this->environment->sefabRoute, '/submit-report', [
            'methods' => 'POST',
            'callback' => [$this, 'submit_report']
        ]);

        register_rest_route($this->environment->sefabRoute, '/notify', [
            'methods' => 'POST',
            'callback' => [$this, 'notify_post']
        ]);

        register_rest_route($this->environment->sefabRoute, '/policies', [
            'methods' => 'POST',
            'callback' => [$this, 'get_all_policies']
        ]);

        register_rest_route($this->environment->sefabRoute, '/policies-amount/', [
            'methods' => 'POST',
            'callback' => [$this, 'get_policies'],
        ]);

        register_rest_route($this->environment->sefabRoute, '/policies-amount-after', [
            'methods' => 'POST',
            'callback' => [$this, 'get_policies_after']
        ]);

        register_rest_route($this->environment->sefabRoute, '/all-policies-after', [
            'methods' => 'POST',
            'callback' => [$this, 'get_all_policies_after']
        ]);
            
        register_rest_route($this->environment->sefabRoute, '/form-submit', [
            'methods' => 'POST',
            'callback' => [$this, 'policy_form_submit']
        ]);

        register_rest_route($this->environment->sefabRoute, '/read-policy', [
            'methods' => 'POST',
            'callback' => [$this, 'read_policy']
        ]);

        register_rest_route($this->environment->sefabRoute, '/send-code', [
            'methods' => 'POST',
            'callback' => [$this->registrationManager, 'prefix_admin_send_code']
        ]);

        register_rest_route($this->environment->sefabRoute, '/verify-code', [
            'methods' => 'POST',
            'callback' => [$this->registrationManager, 'prefix_admin_verify_code']
        ]);

        register_rest_route($this->environment->sefabRoute, '/complete-registration', [
            'methods' => 'POST',
            'callback' => [$this->registrationManager, 'prefix_admin_new_registration']
        ]);

        register_rest_route($this->environment->sefabRoute, '/resend-code', [
            'methods' => 'POST',
            'callback' => [$this->registrationManager, 'prefix_admin_resend_code']
        ]);

        register_rest_route($this->environment->sefabRoute, '/search', array(
            'methods' => 'POST',
            'callback' => array($this, 'search_policy')
        ));
    }

    public function get_project_by_id($data) {
        return $this->projectsManager->get_by_id($data['projectId']);
    }

    public function notify_post ($data) {
        return $this->notificationService->notify("SEFAPP", $data['postTitle'], []);
    }

    public function read_policy ($data) {
        return $this->readManager->mark_as_read($data['policyId'], $data['userId']);
    }

    public function get_policy_by_id($data)
    {
        return $this->policyProvider->get_by_id($data['policyId']);
    }

    public function get_all_policies($data)
    {
        return $this->policyProvider->get($data['userId']);
    }

    public function policy_form_submit($data)
    {
        $form = $data['form'];
        $policy = $data['policy'];
        $policy_title = $policy['title'];

        $form_data = $this->formDataBuilder->build($data);

        $this->formManager->execute_submit((object) $form_data, $policy_title, $policy['id'], get_current_user_id());

        return [
            'result' => 'success',
            'code' => 200,
            'data' => []
        ];
    }
    
    public function get_policies($data)
    {
        return $this->policyProvider->get($data['userId'], $data['amount'], '');
    }

    public function get_policies_after($data)
    {
        $amount = $data['amount'];
        $policy_id = $data['policyId'];
        $timestamp = $this->policyProvider->get_timestamp($policy_id);
        return $this->policyProvider->get($data['userId'], $amount, $timestamp);
    }

    public function get_all_policies_after($data)
    {
        $policy_id = $data['policyId'];
        $timestamp = $this->policyProvider->get_timestamp($policy_id);
        return $this->policyProvider->get($data['userId'], -1, $timestamp);
    }

    public function search_policy($data) {        
        return $this->policyProvider->search($data['userId'], $data['searchValue']);
    }

    public function submit_report($data) {
        return $this->reportProvider->submit_report($data);
    }
}
