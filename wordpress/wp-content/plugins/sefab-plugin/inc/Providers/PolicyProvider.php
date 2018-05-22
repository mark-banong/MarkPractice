<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Providers;

class PolicyProvider
{
    private $dbManager;
    public function __construct($db_manager)
    {
        $this->dbManager = $db_manager;
    }

    public function get($user_id, $amount = -1, $last_timestamp = null)
    {
        $policies = [];
        
        foreach ($this->getPolicies($user_id, $amount, $last_timestamp) as $policy) {
            $policy['paragraphs'] = $this->getParagraphs($policy);
            $policy['forms'] = $this->getForms($policy);
            $policies[] = $policy;
        }

        return $policies;
    }

    public function get_by_id($policy_id)
    {
        $policy;
        $result = $this->dbManager->select("*", "sefab_post", "is_deleted = 0 AND id = $policy_id");

        if (count($result) === 1) {
            $policy =  [
                'id' => $result[0]->id,
                'wpPostId' => $result[0]->wp_post_id,
                'userId' => $result[0]->user_id,
                'category'  => $result[0]->category,
                'title' => $result[0]->title,
                'timestamp' => $result[0]->time_stamp,
                'timestampUpdated' => $result[0]->time_stamp_updated,
                'paragraphs' => [],
                'forms' => []
            ];
            $policy['paragraphs'] = $this->getParagraphs($policy);
            $policy['forms'] = $this->getForms($policy);
        }

        return $policy;
    }

    public function get_timestamp($policy_id)
    {
        return $this->dbManager->select("time_stamp", "sefab_post", "id = $policy_id")[0]->time_stamp;
    }

    private function getPolicies($user_id, $amount, $last_timestamp = null)
    {
        $policies = [];
        
        if ($amount === -1 && !$last_timestamp) {
            $result = $this->dbManager->select("*", "sefab_post", "is_deleted = 0", "ORDER BY time_stamp_updated DESC");
        }
        
        if ($amount > -1 && $last_timestamp) {
            $result = $this->dbManager->select("*", "sefab_post", "is_deleted = 0 AND time_stamp_updated < '$last_timestamp'", "ORDER BY time_stamp_updated DESC LIMIT $amount");
        }
        
        if ($amount === -1 && $last_timestamp) {
            $result = $this->dbManager->select("*", "sefab_post", "is_deleted = 0 AND time_stamp_updated > '$last_timestamp'", "ORDER BY time_stamp_updated");
        }

        if ($amount > -1 && !$last_timestamp) {
            $result = $this->dbManager->select("*", "sefab_post", "is_deleted = 0", "ORDER BY time_stamp_updated DESC LIMIT $amount");
        }

        foreach ($result as $policy) {
            $policies[] = [
                'id' => $policy->id,
                'wpPostId' => $policy->wp_post_id,
                'userId' => $policy->user_id,
                'category'  => $policy->category,
                'title' => $policy->title,
                'isRead' => $this->get_is_read($user_id, $policy),
                'timestamp' => $policy->time_stamp,
                'timestampUpdated' => $policy->time_stamp_updated,
                'paragraphs' => [],
                'forms' => [],
            ];
        }

        return $policies;
    }

    private function get_is_read($user_id, $policy)
    {
        $result = $this->dbManager->select("*", "sefab_view_tracker", "user_id = '$user_id' AND post_id = '$policy->id'");

        return count($result) > 0;
    }

    private function getForms($policy)
    {
        $policy_id = $policy['id'];
        $forms = [];
        $result = $this->dbManager->select("*", "sefab_form", "post_id = $policy_id");
        
        foreach ($result as $form) {
            $forms[] = [
                'id ' => $form->id,
                'position' => $form->position,
                'wp_form_id' => $form->wp_form_id,
                'deleted' => $form->is_deleted,
                'title' => (!isset($form->title) || strtolower($form->title) === strtolower('"NULL"') || strtolower($form->title) === strtolower('NULL')) ? '' : $form->title,
                'description' => (!isset($form->form_description) || strtolower($form->form_description) === strtolower('"NULL"') || strtolower($form->form_description) === strtolower('NULL')) ? '' : $form->form_description,
                'questions' => $this->getQuestions($form)
            ];
        }

        return $forms;
    }

    private function getQuestions($form)
    {
        $form_id = $form->id;
        $questions = [];
        $result = $this->dbManager->select('*', 'sefab_question', 'wp_form_id = "' . $form->wp_form_id .'" AND is_deleted = 0');

        foreach ($result as $question) {
            $options = $this->getOptions($question);

            $selected = '';
            if ((strtolower($question->form_type) === 'select' || strtolower($question->form_type) === 'radio') && count($options) > 0) {
                $selected = $options[0]['label'];
            }

            $questions[] = [
                'id' => $question->id,
                'value' => $question->form_title,
                'questionType' => strtolower($question->form_type),
                'deleted' => $question->is_deleted,
                'required' => $question->is_require,
                'options' => $options,
                'selectedOption' => $selected
            ];
        }

        return $questions;
    }

    private function getOptions($question)
    {
        $question_id = $question->id;
        $options = [];
        $result = $this->dbManager->select("*", "sefab_option", "question_id = $question_id");

        foreach ($result as $option) {
            $options[] = [
                'id' => $option->id,
                'label' => $option->option_value,
                'deleted' => $option->is_deleted
            ];
        }

        if (count($options) === 0) {
            $options[] = [
                'id' => -1,
                'label' => '',
                'deleted' => false
            ];
        }

        return $options;
    }

    private function getParagraphs($policy)
    {
        $policy_id = $policy['id'];
        $paragraphs = [];
        $result = $this->dbManager->select("*", "sefab_paragraph", "post_id = $policy_id");

        foreach ($result as $paragraph) {
            $paragraphs[] = [
                'id' => $paragraph->id,
                'value' => (!isset($paragraph->content) || strtolower($paragraph->content) === strtolower('"NULL"') || strtolower($paragraph->content) === strtolower('NULL')) ? '' : json_decode($paragraph->content),
                'header' => (!isset($paragraph->header) || strtolower($paragraph->header) === strtolower('"NULL"') || strtolower($paragrahp->header) === strtolower('NULL')) ? '' : json_decode($paragraph->header),
                'position' => $paragraph->position
            ];
        }
        return $paragraphs;
    }

    public function search($user_id, $search_value)
    {
        $policies = [];
    
        $search_result = $this->dbManager->select("DISTINCT sefab_post.*, sefab_paragraph.post_id", "sefab_post INNER JOIN sefab_paragraph ON sefab_post.id=sefab_paragraph.post_id", "sefab_post.is_deleted = 0 && (sefab_post.title LIKE '%$search_value%' OR sefab_paragraph.content LIKE '%$search_value%' OR sefab_paragraph.header LIKE '%$search_value%')");

        foreach ($search_result as $policy) {
            $newPolicy = [
                'id' => $policy->id,
                'wpPostId' => $policy->wp_post_id,
                'userId' => $policy->user_id,
                'category' => $policy->category,
                'title' => $policy->title,
                'timestamp' => $policy->time_stamp,
                'timestampUpdated' => $policy->time_stamp_updated,
                'isRead' => $this->get_is_read($user_id, $policy),
                'paragraphs' => [],
                'forms' => []
            ];

            $newPolicy['paragraphs'] = $this->getParagraphs($newPolicy);
            $newPolicy['forms'] = $this->getForms($newPolicy);
            $policies[] = $newPolicy;
        }
        return $policies;
    }
}
