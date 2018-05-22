<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Managers;

class RoleLimiterManager {

    private $dbManager;

    public function __construct($db_manager) {
        //Construct and inject dependencies
        $this->dbManager = $db_manager;
    }

    private function get_allowed_roles ($user) {
        $allowed = array();

        if ( in_array( 'administrator', $user->roles ) ) { // Admin can edit all roles
            $allowed = array_keys( $GLOBALS['wp_roles']->roles );
        } elseif ( in_array( 'lower_administrator', $user->roles ) ) {
            $allowed[] = 'lower_administrator';
            $allowed[] = 'employee';
        } 
        /*elseif ( in_array( 'Receptionist', $user->roles ) ) {
            $allowed[] = 'Guest';
        }*/

        return $allowed;
    }

    public function get_editable_roles ($roles) {
        if ( $user = wp_get_current_user() ) {
            $allowed = $this->get_allowed_roles( $user );
    
            foreach ( $roles as $role => $caps ) {
                if ( ! in_array( $role, $allowed ) )
                    unset( $roles[ $role ] );
            }
        }
    
        return $roles;
    }

    public function map_meta_cap($caps, $cap, $user_id, $args) {
        if ( ( $cap === 'edit_user' || $cap === 'delete_user' ) && $args ) {
            $the_user = get_userdata( $user_id ); // The user performing the task
            $user     = get_userdata( $args[0] ); // The user being edited/deleted
    
            if ( $the_user && $user && $the_user->ID != $user->ID /* User can always edit self */ ) {
                $allowed = $this->get_allowed_roles( $the_user );
    
                if ( array_diff( $user->roles, $allowed ) ) {
                    // Target user has roles outside of our limits
                    $caps[] = 'not_allowed';
                }
            }
        }
    
        return $caps;
    }

    public function hide_administrator($user_search) {
        if ( !current_user_can( 'administrator' ) ) { // Is Not Administrator - Remove Administrator
           $this->dbManager->replace_user_search_query($user_search);
        }
    }

    public function register_actions () {
        add_filter('editable_roles', [$this, 'get_editable_roles'], 2, 1);
        add_filter('map_meta_cap', [$this, 'map_meta_cap'], 10, 4);
        add_filter('views_users', '__return_empty_array');
        add_action('pre_user_query', [$this, 'hide_administrator']);
    }
}
?>