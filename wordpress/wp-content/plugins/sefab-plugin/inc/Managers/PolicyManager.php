<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Managers;

class PolicyManager
{
    private $paragraphParser;
    private $questionParser;
    private $policyProvider;
    private $optionParser;
    private $policyParser;
    private $logService;
    private $dbManager;
    private $formParser;
    private $postId;
    private $environment;

    public function __construct($db_manager, $policy_parser, $paragraph_parser, $form_parser, $question_parser, $option_parser, $policy_provider, $environment, $log_service)
    {
        $this->paragraphParser = $paragraph_parser;
        $this->questionParser = $question_parser;
        $this->policyProvider = $policy_provider;
        $this->optionParser = $option_parser;
        $this->policyParser = $policy_parser;
        $this->environment = $environment;
        $this->formParser = $form_parser;
        $this->logService = $log_service;
        $this->dbManager = $db_manager;
    }

    public function register_scripts($hook)
    {
        $this->logService->log('register_scripts', json_encode($hook));
        if ($hook === 'post-new.php' || $hook === 'post.php') {
            $plugin_url = plugin_dir_url(__FILE__);
            wp_enqueue_script('sefab-prompt-script', plugins_url() . '/sefab-plugin/inc/resources/scripts/notification-prompt.js');
        }
    }

    public function create($post_id)
    {
        $this->postId = $post_id;

        $existing_posts = $this->dbManager->select('sefab_post.wp_post_id', "sefab_post", "sefab_post.wp_post_id = $post_id ");

        if (count($existing_posts) == 0) {
            $post_title = get_the_title($post_id);
            $category = (isset(get_the_category($post_id)[0]->name) ? get_the_category($post_id)[0]->name : 'Uncategorized');

            $last_post_id = $this->dbManager->insert('sefab_post', array(
                "wp_post_id" => $post_id,
                "title" => $post_title,
                "user_id" => get_current_user_id(),
                "time_stamp" => date("Y-m-d H:i:s"),
                "time_stamp_updated" => date("Y-m-d H:i:s"),
                "category" => $category,
            ));
            $this->set_policy($post_id, $last_post_id);
        }
    }

    private function set_policy($post_id, $last_post_id, $update = false, $old_data = [])
    {
        $elements = $this->policyParser->parse($post_id);

        $paragraphData = $this->paragraphParser->parse($last_post_id, $elements);
        foreach ($paragraphData as $paragraph) {
            $result = $this->dbManager->insert("sefab_paragraph", $paragraph);
        }

        $formData = $this->formParser->parse($last_post_id, $elements);
        foreach ($formData as $form) {

            $last_form_id = $this->dbManager->insert("sefab_form", $form["data"]);
            $last_form = $this->dbManager->select("*", "sefab_form", "id = $last_form_id");
            if (!$last_form) {
                return;
            }

            $last_form = $last_form[0];

            //Check if questions already exist
            $existing_forms = $this->dbManager->select('*', 'sefab_form', 'wp_form_id = "' . $last_form->wp_form_id . '" AND is_deleted = 0');

            if ($update || count($existing_forms) === 1) {
                $questions = $this->questionParser->parse($last_form->wp_form_id, $last_form_id, $form["question_params"]["form_questions_html"]);

                foreach ($questions as $question) {
                    //Check if question exists by wp ids
                    $existing_questions = $this->dbManager->select('*', 'sefab_question', 'wp_question_id = "' . $question['data']['wp_question_id'] . '" AND wp_form_id = "' . $question['data']['wp_form_id'] . '" AND is_deleted = 0');

                    if (!$existing_questions || count($existing_questions) === 0) {
                        $last_question_id = $this->dbManager->insert("sefab_question", $question["data"]);

                        if ($update) {
                            $last_question = $this->dbManager->select("*", "sefab_question", "id = $last_question_id")[0];
                            if ($old_data['questions'] && count($old_data['questions']) !== 0) {
                                foreach ($old_data['questions'] as $old_question) {

                                    if ($old_question->wp_question_id === $last_question->wp_question_id) {
                                        $update_answer_result = $this->dbManager->update("sefab_answer", "question_id = $last_question_id, post_id = $last_post_id", "question_id = $old_question->id");
                                    }
                                }
                            }
                        }

                        $options = $this->optionParser->parse($last_question_id, $question["optionParams"]["question_html"], $question["optionParams"]["find_type"], $question["optionParams"]["form_type"]);

                        foreach ($options as $option) {
                            $this->dbManager->insert("sefab_option", $option);
                        }
                    }
                }
            }
        }
    }

    public function update($post)
    {
        //To Do: Add Update functionality
        $old_policy = $this->dbManager->select('id, time_stamp', "sefab_post", "wp_post_id = $post->ID ");

        $old_policy_id = $old_policy[0]->id;
        $old_forms = $this->dbManager->select("id, wp_form_id", "sefab_form", "post_id = $old_policy_id");

        $old_questions = [];
        foreach ($old_forms as $old_form) {
            $old_questions = $this->dbManager->select("id, wp_question_id", "sefab_question", "form_id = $old_form->id");
        }

        $this->deletePost($post->ID);

        $post_title = get_the_title($post->ID);
        $category = (isset(get_the_category($post->ID)[0]->name) ? get_the_category($post->ID)[0]->name : 'Uncategorized');

        $last_post_id = $this->dbManager->insert('sefab_post', array(
            "wp_post_id" => $post->ID,
            "title" => $post_title,
            "user_id" => get_current_user_id(),
            "time_stamp" => $old_policy[0]->time_stamp,
            "time_stamp_updated" => date("Y-m-d H:i:s"),
            "category" => $category,
        ));

        $old_data = [
            'forms' => $old_forms,
            'questions' => $old_questions,
        ];

        $this->set_policy($post->ID, $last_post_id, true, $old_data);
    }

    public function deletePost($post_id)
    {
        $formId = $this->dbManager->select('sefab_form.id', "sefab_form INNER JOIN sefab_post on sefab_form.post_id = sefab_post.id", "sefab_post.wp_post_id = $post_id AND sefab_post.is_deleted = 0");

        $questionId = $this->dbManager->select('sefab_question.id', "sefab_question INNER JOIN sefab_form INNER JOIN sefab_post on sefab_question.wp_form_id = sefab_form.wp_form_id AND sefab_form.post_id = sefab_post.id", "sefab_post.wp_post_id = $post_id AND sefab_post.is_deleted = 0");

        $optionId = $this->dbManager->select('sefab_option.id', "sefab_option INNER JOIN sefab_question INNER JOIN sefab_form INNER JOIN sefab_post on sefab_option.question_id = sefab_question.id AND sefab_question.wp_form_id = sefab_form.wp_form_id AND sefab_form.post_id = sefab_post.id", "sefab_post.wp_post_id = $post_id AND sefab_post.is_deleted = 0");

        $paragraphId = $this->dbManager->select('sefab_paragraph.id', "sefab_paragraph INNER JOIN sefab_post on sefab_paragraph.post_id = sefab_post.id", "sefab_post.wp_post_id = $post_id AND sefab_post.is_deleted = 0");

        // $this->dbManager->delete('sefab_post', array("wp_post_id" => $post_id));
        $this->dbManager->update('sefab_post', 'is_deleted = 1', 'wp_post_id = ' . $post_id);

        for ($count = 0; $count < count($paragraphId); $count++) {
            // $this->dbManager->delete('sefab_paragraph', array('id' => $paragraphId[$count]->id));

            $id = $paragraphId[$count]->id;
            $this->dbManager->update("sefab_paragraph", "is_deleted = 1", "id = $id");
        }

        for ($count = 0; $count < count($formId); $count++) {
            // $this->dbManager->delete('sefab_form', array('id' => $formId[$count]->id));

            $id = $formId[$count]->id;
            $this->dbManager->update("sefab_form", "is_deleted = 1", "id = $id");
        }

        for ($count = 0; $count < count($questionId); $count++) {
            // $this->dbManager->delete('sefab_question', array('id' => $questionId[$count]->id));

            $id = $questionId[$count]->id;
            $this->dbManager->update("sefab_question", "is_deleted = 1", "id = $id");
        }

        for ($count = 0; $count < count($optionId); $count++) {
            // $this->dbManager->delete('sefab_option', array('id' => $optionId[$count]->id));

            $id = $optionId[$count]->id;
            $this->dbManager->update("sefab_option", "is_deleted = 1", "id = $id");
        }
    }

    public function trash($post_id)
    {
        //To Do: Add Delete functionality
        $this->dbManager->wp_update('sefab_post', array("is_deleted" => 1), array("wp_post_id" => $post_id));
        $this->dbManager->update('sefab_paragraph INNER JOIN sefab_post on sefab_paragraph.post_id = sefab_post.id', "sefab_paragraph.is_deleted = 1", "sefab_post.wp_post_id = $post_id");
        $this->dbManager->update('sefab_form INNER JOIN sefab_post on sefab_form.post_id = sefab_post.id', "sefab_form.is_deleted =1", "sefab_post.wp_post_id = $post_id");
        $this->dbManager->update('sefab_question INNER JOIN sefab_form INNER JOIN sefab_post on sefab_question.form_id = sefab_form.id AND sefab_form.post_id = sefab_post.id', "sefab_question.is_deleted =1", "sefab_post.wp_post_id = $post_id");
        $this->dbManager->update(
            'sefab_option INNER JOIN sefab_question INNER JOIN sefab_form INNER JOIN sefab_post on sefab_option.question_id = sefab_question.id AND sefab_question.form_id = sefab_form.id AND sefab_form.post_id = sefab_post.id',
            "sefab_option.is_deleted =1",
            "sefab_post.wp_post_id = $post_id"
        );
    }

    public function restore($post_id)
    {
        //To Do: Add Delete functionality
        $this->dbManager->wp_update('sefab_post', array("is_deleted" => 0), array("wp_post_id" => $post_id));
        $this->dbManager->update('sefab_paragraph INNER JOIN sefab_post on sefab_paragraph.post_id = sefab_post.id', "sefab_paragraph.is_deleted = 0", "sefab_post.wp_post_id = $post_id");
        $this->dbManager->update('sefab_form INNER JOIN sefab_post on sefab_form.post_id = sefab_post.id', "sefab_form.is_deleted =0", "sefab_post.wp_post_id = $post_id");
        $this->dbManager->update('sefab_question INNER JOIN sefab_form INNER JOIN sefab_post on sefab_question.form_id = sefab_form.id AND sefab_form.post_id = sefab_post.id', "sefab_question.is_deleted =0", "sefab_post.wp_post_id = $post_id");
        $this->dbManager->update(

            'sefab_option INNER JOIN sefab_question INNER JOIN sefab_form INNER JOIN sefab_post on sefab_option.question_id = sefab_question.id AND sefab_question.form_id = sefab_form.id AND sefab_form.post_id = sefab_post.id',

            "sefab_option.is_deleted =0",
            "sefab_post.wp_post_id = $post_id"

        );
    }

    public function register_actions()
    {
        //when post is trash
        add_action('trash_post', array($this, 'trash'), 1, 1);
        //when post is restore/undo
        add_action('untrash_post', array($this, 'restore'), 1, 1);
        //published post
        add_action('publish_post', array($this, 'create'), 1, 1);
        //confirm notification when publishing policy
        // add_action('admin_footer', array($this, 'confirm_notification'), 1, 1);
        //post is updated
        //add_action('post_updated', array($this, 'update'), 1, 1);
        add_action('transition_post_status', function ($new_status, $old_status, $post) {
            if ($old_status == 'publish' && $new_status == 'publish') {
                $this->update($post);
            }
        }, 10, 3);
        //when post is deleted
        add_action('before_delete_post', array($this, 'deletePost'), 1, 1);

        //Enqueue Scripts
        add_action('admin_enqueue_scripts', [$this, 'register_scripts']);
    }
}
