<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Managers;

class RegistrationManager
{
    private $geoLocationProvider;
    private $dbManager;
    private $smsService;
    private $loginPage;
    private $environment;
    private $fileService;
    private $logService;
    private $excelService;
    private $csvService;
    private $validationService;

    public function __construct($environment, $db_manager, $sms_service, $geo_location_provider, $file_service, $log_service, $excel_service, $csv_service, $validation_service)
    {
        session_start();
        $this->loginPage = home_url('/login/');
        $this->dbManager = $db_manager;
        $this->smsService = $sms_service;
        $this->environment = $environment;
        $this->geoLocationProvider = $geo_location_provider;
        $this->fileService = $file_service;
        $this->logService = $log_service;
        $this->excelService = $excel_service;
        $this->csvService = $csv_service;
        $this->validationService = $validation_service;
    }

    public function register_actions()
    {
        add_action('admin_post_nopriv_new_registration', [$this, 'prefix_admin_new_registration']);
        add_action('admin_post_nopriv_verify_code', [$this, 'prefix_admin_verify_code']);
        add_action('admin_post_nopriv_resend_code', [$this, 'prefix_admin_resend_code']);
        add_action('admin_post_nopriv_send_code', [$this, 'prefix_admin_send_code']);
        add_action('admin_post_nopriv_validate_user', [$this, 'prefix_admin_validate_user']);
        add_action('jwt_auth_token_before_dispatch', [$this, 'define_sign_in_data']);
        add_action('jwt_auth_token_before_sign', [$this, 'before_token_sign']);
    }

    public function before_token_sign($data)
    {

        $_SESSION['jwt_user_sign_id'] = $data['data']['user']['id'];

        return $data;
    }

    public function define_sign_in_data($data)
    {
        $userdata = get_user_by('user_login', $data['user_email']);
        $data['user_id'] = $_SESSION['jwt_user_sign_id'];

        return $data;
    }

    private function do_result($api, $action, $result, $returnData = [])
    {
        if (isset($api) && ($api === 'true' || $api === true)) {
            if ($result === 'false' || $result === 'incomplete' || $result === 'returnee') {
                return [
                    'code' => 500,
                    'success' => false,
                    'returnData' => $returnData,
                ];
            }

            return [
                'code' => 200,
                'success' => true,
                'returnData' => $returnData,
            ];
        } else {
            wp_redirect($this->loginPage . '?' . $action . '=' . $result);
        }
    }

    public function prefix_admin_validate_user()
    {
        if (isset($_POST['log'])) {
            $user_number = $_POST['log'];
            $api = $_POST['api'];
        } else {
            $inputJSON = file_get_contents('php://input');
            $input = json_decode($inputJSON, true);
            $api = $input['api'];
            $user_number = $input['log'];
        }

        if (isset($_POST['pwd'])) {
            $pass_word = $_POST['pwd'];
            $api = $_POST['api'];
        } else {
            $inputJSON = file_get_contents('php://input');
            $input = json_decode($inputJSON, true);
            $api = $input['api'];
            $pass_word = $input['pwd'];
        }

        if ($user_number != null) {

            if (is_numeric(preg_replace('/-+/', '', $user_number))) {
                $clean_number = substr(preg_replace('/-+/', '', $user_number), -10);
            } else {
                $clean_number = $user_number;
            }

            $user = wp_authenticate($clean_number, $pass_word);
            $user_id = $user->ID;

            if ($user) {
                wp_set_auth_cookie($user->ID);
                wp_redirect(home_url(''));
            } else {
                return $this->do_result($api, 'login', 'failed');
                exit;
            }
        } else {
            return $this->do_result($api, 'login', 'empty');
            exit;
        }
    }

    public function prefix_admin_send_code()
    {
        if (isset($_POST['phoneNumber'])) {
            $phone_number = $_POST['phoneNumber'];
            $api = $_POST['api'];
        } else {
            $inputJSON = file_get_contents('php://input');
            $input = json_decode($inputJSON, true);
            $api = $input['api'];
            $phone_number = $input['phoneNumber'];
        }

        $clean_number = substr(preg_replace('/-+/', '', $phone_number), -10);
        $final_number = $this->environment->countryCode . $clean_number;

        if ($clean_number != null) {
            $country_code = $this->geoLocationProvider->get_phone_country_code_by_ip();

            if (!$country_code) {
                return $this->do_result($api, 'register', 'false&country=invalid', ['message' => 'Invalid Country Code']);
            }

            $final_number = $country_code . substr(preg_replace('/\s+/', '', $clean_number), -10);
            $users = $this->dbManager->select_from_users_table('ID', "user_login = '$clean_number'");
            $user_id = $users[0]->ID;
            $user = get_user_by('ID', $user_id);

            $verify_account_set = $this->dbManager->select("user_id", "sefab_verification_code", "status = 'Completed' AND user_id = $user_id");

            $account_set = $verify_account_set[0]->user_id;
            $verified_account_set = get_user_by('ID', $account_set);
            $_SESSION['sefab_registration_user_id'] = $user_id;

            if ($verified_account_set) {
                return $this->do_result($api, 'register', 'returnee', ['hasAccount' => true]);
                exit;
            }

            if (!$user) {
                return $this->do_result($api, 'register', 'false');
            } else {
                $expire_code = $this->dbManager->update("sefab_verification_code", "status = 'Expired'", "status = 'Active' AND user_id = '$user_id'");

                $code = rand(100000, 999999);

                // Twilio
                $result = $this->smsService->send_verification_code($final_number, $code);

                $insertResult = $this->dbManager->insert(
                    'sefab_verification_code',
                    [
                        'verification_code' => $code,
                        'user_id' => $user_id,
                        'status' => 'Active',
                        'timestamp' => date('Y-m-d H:i:s'),
                    ]
                );

                return $this->do_result($api, 'verify', 'pending', [
                    'sefabRegistrationUserId' => $user_id,
                ]);
            }
        } else {
            return $this->do_result($api, 'register', 'false');
        }
    }

    public function prefix_admin_verify_code()
    {
        if (isset($_POST['verificationCode'])) {
            $verification_code = $_POST['verificationCode'];
            $api = $_POST['api'];
            $current_user_id = $_SESSION['sefab_registration_user_id'];
        } else {
            $inputJSON = file_get_contents('php://input');
            $input = json_decode($inputJSON, true);
            $api = $input['api'];
            $verification_code = $input['verificationCode'];
            $current_user_id = $input['userId'];
        }

        if ($verification_code != null) {
            $users = $this->dbManager->select("user_id", "sefab_verification_code", "verification_code = '$verification_code' AND status = 'Active' and user_id = '$current_user_id'");
            $verified_user_id = $users[0]->user_id;
            $verified_user = get_user_by('ID', $verified_user_id);

            if (!$verified_user) {
                return $this->do_result($api, 'verify', 'false');
                exit;
            } else {
                $verify_code = $this->dbManager->update("sefab_verification_code", "status = 'Verified'", "verification_code = '$verification_code' AND user_id = '$current_user_id'");
                return $this->do_result($api, 'registration', 'start');
                exit;
            }
        } else {
            return $this->do_result($api, 'verify', 'false');
            exit;
        }
    }

    public function prefix_admin_new_registration()
    {
        if (isset($_POST['pwd'])) {
            $password = $_POST['pwd'];
            $api = $_POST['api'];
            $new_user_id = $_SESSION['sefab_registration_user_id'];
        } else {
            $inputJSON = file_get_contents('php://input');
            $input = json_decode($inputJSON, true);
            $api = $input['api'];
            $password = $input['password'];
            $new_user_id = $input['userId'];
        }

        $hash = wp_hash_password($password);
        $current_user = get_user_by('ID', $new_user_id);

        wp_set_password($password, $new_user_id);
        wp_set_auth_cookie($current_user->ID);

        if ($password != null) {
            $complete = $this->dbManager->update("sefab_verification_code", "status='Completed'", "user_id= '$new_user_id' ORDER BY id DESC LIMIT 1");

            if ($api) {
                return [
                    'code' => 200,
                    'success' => true,
                ];
            } else {
                wp_redirect(home_url(''));
            }
            exit;
        } else {
            $this->do_result($api, 'registration', 'incomplete');
            exit;
        }
    }

    public function prefix_admin_resend_code()
    {
        if (isset($_POST['action']) && $_POST['action'] === 'resend_code') {
            $api = $_POST['api'];
            $valid_user = $_SESSION['sefab_registration_user_id'];
        } else {
            $inputJSON = file_get_contents('php://input');
            $input = json_decode($inputJSON, true);
            $api = $input['api'];
            $valid_user = $input['userId'];
        }

        $Another_code = $this->dbManager->update("sefab_verification_code", "status = 'Expired'", "status = 'Active' AND user_id = '$valid_user'");
        $user_phone_number = $this->dbManager->select_from_users_table('user_login', "ID = '$valid_user'");
        $phone_number = $user_phone_number[0]->user_login;

        $country_code = $this->geoLocationProvider->get_phone_country_code_by_ip();

        if (!$country_code) {
            return $this->do_result($api, 'register', 'false&country=invalid', ['message' => 'Invalid Country Code']);
        }

        $another_final_number = $country_code . substr(preg_replace('/\s+/', '', $phone_number), -10);
        $new_code = rand(100000, 999999);

        //Twilio SMS code here....
        $this->smsService->send_verification_code($another_final_number, $new_code);

        $insertResult = $this->dbManager->insert(
            'sefab_verification_code',
            [
                'verification_code' => $new_code,
                'user_id' => $valid_user,
                'status' => 'Active',
                'timestamp' => date('Y-m-d H:i:s'),
            ]
        );

        return $this->do_result($api, 'verify', 'pending');
        exit;
    }

    public function display_import_page()
    {
        require_once($this->environment->pluginPath . 'inc/resources/views/import-users.php');
        
        if(isset($_GET['display-table']) && isset($_GET['file-id'])){
            $data = $this->csvService->read( $_GET['file-id'] );
            
            $validated_data = $this->validationService->validate_user_csv($data);
            $this->logService->Log('registration_manager', json_encode($validated_data));
            $_SESSION['data']=$validated_data;
            require_once($this->environment->pluginPath . 'inc/resources/views/display-users.php');
        }
        elseif(isset($_GET['upload-error'])){
            $error = preg_replace("~-~"," ", $_GET['upload-error']);
            echo "<h1>".$error."</h1>";
        }
        elseif(isset($_GET['users-added'])){
            echo '<h1>Added '.$_GET['users-added'].' Users'."</h1>";
        }
    }

    public function add_menu_items()
    {
        if (current_user_can('administrator') || current_user_can('lower_administrator') || current_user_can('super_administrator')) {
            $page_title = 'Sefab Import Users';
            $menu_title = 'Sefab Import Users';
            $capability = 'read';
            $menu_slug = 'sefab-import-users';
            $function = [$this, 'display_import_page'];
            $icon_url = 'dashicons-format-aside';
            $position = 4;
            add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position);
        }
    }

    public function import_file($data)
    {
        $upload_info = $this->fileService->upload($_FILES['file'], ['xlsx']);
        $file_id = $upload_info[0]['file_id'];

        if (empty($upload_info['error']) == true) {
            $converted_file = $this->excelService->convert_to_csv($file_id);

            $converted_file_id = $this->fileService->insert($converted_file, $converted_file['dir']);

            wp_redirect( '/wordpress/wp-admin/admin.php?page=sefab-import-users&display-table=true&file-id='.$converted_file_id['file_id']);

            exit;
        } else {

            wp_redirect('/wordpress/wp-admin/admin.php?page=sefab-import-users&upload-error=' . $upload_info['error']);
            exit;
        }
    }

    public function insert_users_from_file() {
        $data = $_SESSION['data'];
        $this->logService->Log('registration_manager_logs', json_encode($data).'urfli' );
        
        foreach($data as $user_info){
           
            $new_password = wp_generate_password();
            $user_id =  wp_create_user($user_info['mobile'], $new_password, '');
            update_user_meta( $user_id, 'first_name', $user_info['given-name'] );
            update_user_meta( $user_id, 'last_name', $user_info['surname'] );
        }

        wp_redirect( '/wordpress/wp-admin/admin.php?page=sefab-import-users&users-added='.count($data));
        exit;
    } 
}
