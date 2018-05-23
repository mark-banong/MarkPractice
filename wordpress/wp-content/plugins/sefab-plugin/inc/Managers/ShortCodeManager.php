<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Managers;

class ShortCodeManager
{
    private $environment;
    private $dbManager;

    public function __construct($db_manager, $environment)
    {
        $this->environment = $environment;
        $this->dbManager = $db_manager;

        require_once $this->environment->pluginPath . "vendor/autoload.php";
    }

    public function read_required_shortcode($atts)
    {
        global $wp_query;
        
        if ($wp_query->is_single) {
            $current_post = get_post();
            $post_id = $current_post->ID;
            $current_user = wp_get_current_user();
            $user_id = $current_user->ID;
            
            $postId = $this->dbManager->select("id", "sefab_post", "wp_post_id = '$post_id'");
            $sefab_postId = $postId[0]->id;
        
            $check_post_if_read = $this->dbManager->select("post_id", "sefab_view_tracker", "user_id = '$user_id' AND post_id = '$sefab_postId'");
            $verified_read_post_id	= $check_post_if_read[0]->post_id;
            
            //echo $user_id." urfli";
        
            if ($verified_read_post_id == null) {
                return "<button id='have_read'>Jag har läst</button>";
            } else {
                return "";
            }
        }
    }
    
    public function register_actions()
    {
        add_shortcode('readrequired', [$this, 'read_required_shortcode']);
    }
}
