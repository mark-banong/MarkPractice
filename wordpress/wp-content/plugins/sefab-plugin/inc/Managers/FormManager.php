<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Managers;

class FormManager
{
    private $emailContentBuilderService;
    private $emailService;
    private $formProvider;
    private $environment;
    private $dbManager;

    public function __construct($db_manager, $environment, $email_service, $email_content_builder_service, $form_provider)
    {
        $this->emailContentBuilderService = $email_content_builder_service;
        $this->emailService = $email_service;
        $this->formProvider = $form_provider;
        $this->environment = $environment;
        $this->dbManager = $db_manager;

        require_once $this->environment->pluginPath . "vendor/autoload.php";
    }

    public function form_submit_handler($data, $entry, $form_data, $entry_id)
    {
        $wp_post_id = get_post()->ID;
        $wp_form_id = $form_data['id'];
        $post_result = $this->dbManager->select("*", "sefab_post", "wp_post_id = $wp_post_id");
        if (!count($post_result)) {
            return;
        }

        foreach ($data as $key => $field) {
            $wp_question_id = $field['id'];

            $post_result = $this->dbManager->select("*", "sefab_post", "wp_post_id = $wp_post_id");
            $post_id = $post_result[0]->id;

            $form_result = $this->formProvider->get_forms_by_wp_id_and_post_id($wp_form_id, $post_id);
            // $form_result = $this->dbManager->select("*", "sefab_form", "wp_form_id = 'wpforms-form-$wp_form_id' AND post_id = $post_id");
            $form_id = $form_result[0]->id;

            $sefab_question_result = $this->dbManager->select("*", "sefab_question", "wp_question_id = $wp_question_id AND form_id = $form_id");

            $data[$key]['id'] = $sefab_question_result[0]->id;
        }

        $this->execute_submit($data, get_post()->post_title, $post_result[0]->id, get_current_user_id());
    }

    public function execute_submit($formData, $post_title, $post_id, $user_id)
    {   
        //Create Content
        $to = "jamie.vanstone@mllrdev.com";
        $header = "Sefab Intranet";
        $body = $this->emailContentBuilderService->build_policy_email(['title' => $post_title], $formData);
        $subject = "Answers: " . $post_title;

        //Insert into db
        foreach ($formData as $field) {
            $this->dbManager->insert('sefab_answer', [
                'question_id' => $field['id'],
                'post_id' => $post_id,
                'user_id' => $user_id,
                'answers' => $field['value'],
                'timestamp' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->dbManager->insert('sefab_email', [
            'receiver_email_address' => $to,
            'user_id' => $user_id,
            'subject' => $subject,
            'content' => $body,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);

        //Send Email
        if ($this->environment->isEmailEnabled && $this->environment->isFormEmailsEnabled) {
            $this->emailService->send($to, $header, $body, $subject);
        }
    }

    public function get_forms_gouped_by_wp_form_id()
    {
        $forms = [];

        $result = $this->dbManager->select("*", "sefab_form", "1");
        foreach ($result as $form) {
            $wp_form_id = $form->wp_form_id;
            $policiesResult = $this->dbManager->select("COUNT(id) AS amount", "sefab_form", "wp_form_id = '$wp_form_id' AND is_deleted = 0", "GROUP BY wp_form_id");
            $inArray = false;

            foreach ($forms as $key => $form_in_array) {
                if ($form_in_array['wp_form_id'] === $form->wp_form_id) {
                    $inArray = true;

                    $forms[$key]['title'] = ($form->title && $form->title !== 'NULL') ? $form->title : $form_in_array['title'];
                    $forms[$key]['form_description'] = ($form->form_description && $form->form_description !== 'NULL') ? $form->form_description : $form_in_array['form_description'];
                }
            }

            if (!$inArray) {
                $forms[] = [
                    'id' => $form->id,
                    'wp_form_id' => $form->wp_form_id,
                    'title' => $form->title,
                    'description' => $form->form_description,
                    'policies' => $policiesResult[0]->amount,
                ];
            }
        }

        return $forms;
    }

    public function get_form_questions($wp_form_id)
    {
        $questions = [];

        $form_result = $this->dbManager->select("*", "sefab_form", "wp_form_id = '$wp_form_id'", "LIMIT 1");
        $form_id = $form_result[0]->wp_form_id;

        $questions_result = $this->dbManager->select("sefab_question.*", "sefab_question", "wp_form_id = '$wp_form_id' AND is_deleted = 0", "ORDER BY form_title");

        foreach ($questions_result as $question) {
            $questions[] = [
                'id' => $question->id,
                'text' => $question->form_title,
                'type' => $question->form_type,
                'options' => $this->get_question_options($question->id),
            ];
        }

        return $questions;
    }

    public function get_question_options($question_id)
    {
        $options = [];
        $options_result = $this->dbManager->select("sefab_option.*, sefab_question.form_type, sefab_question.form_title", "sefab_option, sefab_question", "sefab_option.question_id = $question_id AND sefab_question.id = $question_id AND sefab_question.is_deleted = 0", "ORDER BY sefab_option.option_value");

        foreach ($options_result as $option) {
            $options[] = [
                'id' => $option->id,
                'text' => $option->option_value,
                'type' => $option->form_type,
                'question' => $option->form_title,
            ];
        }

        if (count($options) === 0) {
            $options[] = [
                'id' => -1,
                'text' => '',
                'type' => '',
                'question' => $option->form_title,
            ];
        }

        return $options;
    }

    public function get_form_policies($wp_form_id)
    {
        $grouped_questions = [];
        $policies = [];

        $policies_result = $this->dbManager->select("sefab_post.*, sefab_form.wp_form_id as form_id", "sefab_post, sefab_form", "sefab_post.id = sefab_form.post_id AND sefab_post.is_deleted = 0 AND sefab_form.wp_form_id = '$wp_form_id'");

        foreach ($policies_result as $policy) {
            $policy_data = [];
            $policy_id = $policy->id;
            $questions = [];
            $form_id = $policy->form_id;
            $questions_result = $this->dbManager->select("*", "sefab_question", "wp_form_id = '$wp_form_id' AND is_deleted = 0", "ORDER BY form_title");

            foreach ($questions_result as $question) {
                $question_id = $question->id;
                $options_to_add = [];
                $options_result = $this->dbManager->select("*", "sefab_option", "question_id  = $question_id AND is_deleted = 0");

                foreach ($options_result as $option) {
                    $options_to_add[] = [
                        'id' => $option->id,
                        'value' => $option->option_value,
                        'question_id' => $questoin_id,
                    ];
                }

                $answers_data_result = $this->dbManager->select("sefab_answer.*", "sefab_answer, sefab_question", "sefab_answer.question_id = $question_id AND sefab_answer.post_id = $policy_id AND sefab_answer.question_id = sefab_question.id AND sefab_question.is_deleted = 0");
                $answers = [];
                $count = [];

                //Get answers and count
                foreach ($answers_data_result as $key => $answer) {
                    

            
                    if (($question->form_type === "TEXT" || $question->form_type === "EMAIL") && $answer->answers) {
                        $answers[] = ['value' => $answer->answers, 'timestamp' => $answer->timestamp];
                        $count[0] += 1;
                    }

                    if ($question->form_type === "NAME" && $answer->answers) {
                       
                        $answers_array = explode("\n", $answer->answers);

                        //Save first name
                        $answers[0] = $answers_array[0];
                        $count[0] += 1;

                        //Save last name
                        $answers[1] = $answers_array[1];
                        $count[1] += 1;
                    }

                    if (($question->form_type === "SELECT" || $question->form_type === "RADIO" || $question->form_type === "RATING") && $answer->answers) {
                        foreach ($options_to_add as $option_key => $option) {
                            if ($option['value'] === $answer->answers) {
                                $answers[$option_key] = $answer->answers;
                                $count[$option_key] += 1;
                            }
                        }
                    }

                    if (($question->form_type === "CHECKBOX") && $answer->answers) {
                        $answers_array = explode("\n", $answer->answers);

                        foreach ($options_to_add as $option_key => $option) {
                            foreach ($answers_array as $actual_answer) {
                                if ($option['value'] === $actual_answer) {
                                    $answers[$option_key] = $actual_answer;
                                    $count[$option_key] += 1;
                                }
                            }
                        }
                    }
                }

                //Check if no answers
                if (($question->form_type === "TEXT" || $question->form_type === "EMAIL") && !$count[0]) {
                    $count[0] = 0;
                }

                if ($question->form_type === "SELECT" || $question->form_type === "CHECKBOX" || $question->form_type === "RADIO" || $question->form_type === "RATING" || $question->form_type === "NAME") {
                    foreach ($options_to_add as $option_key => $option) {
                        if (!$count[$option_key]) {
                            $count[$option_key] = 0;
                        }
                    }

                    ksort($count);
                }

                $questions[] = [
                    'id' => $question->id,
                    'value' => $question->form_title,
                    'type' => $question->form_type,
                    'options' => $options_to_add,
                    'data' => ['answers' => $answers, 'count' => $count],
                ];

                //Add data to policy row
                $policy_data[] = ['answers' => $answers, 'count' => $count];
            }

            $policies[] = [
                'id' => $policy->id,
                'title' => $policy->title,
                'data' => $policy_data,
            ];
        }

        $total_data = [];
        foreach ($policies as $policy) {
            foreach ($policy['data'] as $answer_key => $answer_data) {
                foreach ($answer_data['count'] as $count_key => $count_data) {
                    if (count($answer_data['answers']) > 0) {
                        $total_data[$answer_key][$count_key] = $count_data + $total_data[$answer_key][$count_key];
                    }
                }
            }
        }

        ksort($total_data);

        $return_data['policies'] = $policies;
        $return_data['total'] = $total_data;
        return $return_data;
    }

    public function register_actions()
    {
        //Hooks for submit wpforms_process_complete || wpforms_process_complete_{$form_id}
        add_action('wpforms_process_complete', [$this, 'form_submit_handler'], 10, 4);
    }
}
