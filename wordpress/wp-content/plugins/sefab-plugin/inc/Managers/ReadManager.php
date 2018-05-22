<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Managers;

class ReadManager
{
    private $environment;
    private $logService;
    private $dbManager;

    public function __construct($db_manager, $environment, $log_service)
    {
        //Construct and inject dependencies
        $this->dbManager = $db_manager;
        $this->environment = $environment;
        $this->logService = $log_service;
    }

    public function register_actions()
    {
        add_action('the_post', [$this, 'track_post_view']);
        add_action('the_post', [$this, 'highlight_unread_post']);
    }

    public function highlight_unread_post()
    {
        $current_post = get_post();
        $post_id = $current_post->ID;

        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;

        $postId = $this->dbManager->select("id", "sefab_post", "wp_post_id = '$post_id'");
        $sefab_postId = $postId[0]->id;

        $check_post_if_read = $this->dbManager->select("post_id", "sefab_view_tracker", "user_id = '$user_id' AND post_id = '$sefab_postId'");

        $verified_read_post_id = $check_post_if_read[0]->post_id;

        if ($verified_read_post_id == null) {
            echo '<style type="text/css" href="style.css">
                article.post-' . $post_id . '> header > div >  a > time.entry-date.published::before
                {
                    /*background: rgb(140, 207, 246); */
                    color: rgb(228, 7, 7);
                    content: "UNREAD ";
                }
            </style>';
        }
    }

    public function track_post_view()
    {

        global $wp_query;

        if ($wp_query->is_single) {
            echo '<script>
                jQuery( document ).ready(function() {
                    if (!jQuery("#have_read").length){
                        console.log("wala");
                        jQuery.post("", {mode: "markread"});
                    } else {
                        console.log("naa");
                    }
                });
            </script>';

            echo '<script>
                jQuery( document ).ready(function() {
                    $button = jQuery("#have_read");
                    $button.click(function(){
                        jQuery.post("", {mode: "markread"});
                        $button.hide();
                    });

                    if( jQuery($button).length )
                    {

                    }
                });
            </script>';
        }

        if (isset($_POST) && $_POST['mode'] == 'markread') {
            $wp_post_id = get_the_ID();
            $post_id = $this->dbManager->select("id", "sefab_post", "wp_post_id = '$wp_post_id'");
            $sefab_post_id = $post_id[0]->id;
            $this->mark_as_read($sefab_post_id, wp_get_current_user()->ID);
        }
    }

    public function mark_as_read($post_id, $user_id)
    {
        $this->logService->log("read_log", json_encode( ['postID' => $post_id, 'userID' => $user_id] ));

        if (!$post_id || !$user_id) {
            return [
                'message' => 'missing data.',
                'code' => 200,
                'data' => []
            ];
        }

        $check_post_if_read = $this->dbManager->select("post_id", "sefab_view_tracker", "user_id = '$user_id' AND post_id = '$post_id'");
        $verified_read_post_id = $check_post_if_read[0]->post_id;

        if ($verified_read_post_id == null) {
            $markPost = $this->dbManager->insert(
                'sefab_view_tracker',
                [
                    'post_id' => $post_id,
                    'user_id' => $user_id,
                    'timestamp' => date('Y-m-d H:i:s'),
                ]
            );

            return [
                'message' => 'success',
                'code' => 200,
                'data' => [],
            ];
        }

        return [
            'message' => 'existing',
            'code' => 200,
            'data' => [],
        ];
    }

    public function get_read_unread_amount()
    {
        $data = ['read' => 0, 'unread' => 0];

        $readResult = $this->dbManager->select("COUNT(sefab_view_tracker.id) AS amount", "sefab_view_tracker LEFT JOIN wp_users ON sefab_view_tracker.user_id = wp_users.ID, sefab_post", "sefab_view_tracker.post_id = sefab_post.id AND sefab_post.is_deleted = 0");

        if (count($readResult)) {
            $data['read'] = $readResult[0]->amount;
        }

        $posts = $this->dbManager->select("COUNT(id) AS amount", "sefab_post", "is_deleted = 0");
        $postsCount = 0;
        if (count($posts) > 0) {
            $postsCount = $posts[0]->amount;
        }

        $users = $this->dbManager->select("COUNT(ID) AS amount", "wp_users", "1");
        $usersCount = 0;
        if (count($users)) {
            $usersCount = $users[0]->amount;
        }

        $expectedTotalRecords = $postsCount * $usersCount;
        $data['unread'] = $expectedTotalRecords - $data['read'];
        return $data;
    }

    public function get_read_unread_amount_per_policy() {
        $policies = $this->dbManager->select("*", "sefab_post", "is_deleted = 0");
        $users = $this->dbManager->select("*", "wp_users", "1");
        $return_data = [];
        foreach($policies as $policy) {
            $read = $this->dbManager->select("COUNT(sefab_view_tracker.id) AS amount", "sefab_view_tracker, sefab_post", "post_id = $policy->id AND sefab_post.id = $policy->id AND sefab_post.is_deleted = 0")[0]->amount;

            if (count($users) >= $read) { 
                $unread = count($users) - $read;
            } else {
                $unread = $read - count($users);
            }

            $return_data[] = [
                'id' => $policy->id,
                'title' => $policy->title,
                'read' => $read,
                'unread' => $unread,
                'timestamp' => $policy->time_stamp,
                'timestampUpdated' => $policy->time_stamp_updated
            ];
        }

        return $return_data;
    }

    public function get_user_read_data_by_policy_id($policy_id) {
        $policy = $this->dbManager->select("*", "sefab_post", "id = $policy_id")[0];

        $users = $this->dbManager->select("*", "wp_users", "1");

        $user_data = [];
        $read = 0;
        $unread = 0;
        foreach($users as $user) {
            $select_read_result = $this->dbManager->select("*", "sefab_view_tracker", "post_id = $policy_id AND user_id = $user->ID");

            if ($select_read_result && count($select_read_result) > 0) {
                $remark = 'Read';
                $read += 1;
            } else {
                $unread += 1;
                $remark = 'Unread';
            }

            $user_data[] = [
                'id' => $user->ID,
                'name' => $user->display_name,
                'remark' => $remark
            ];

        }

        return [
            'policy' => $policy,
            'userData' => $user_data,
            'total' => [
                'read' => $read,
                'unread' => $unread
            ]
        ];
    }
}
