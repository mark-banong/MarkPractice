<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Providers;

class AnswerProvider {
    private $dbManager;
    private $environment;
    public function __construct($environment, $db_manager) 
    {
        $this->environment = $environment;
        $this->dbManager = $db_manager;
    }

    public function insert ($form_data, $user_token) {
        $userId = get_current_user_id();


        foreach($form_data['value']['questions'] as $question) {
            $answers = [];
            if ($question['questionType'] === 'text') {
                foreach($question['options'] as $option) {
                    $answers[] = $option['value'];
                }
            } else if ($question['questionType'] === 'select' || $question['questionType'] === 'radio') {
                $answers[] = $question['selectedOption'];
            } else if ($question['questionType'] === 'checkbox') {
                foreach($question['options'] as $option) {
                    if($option['value']) {
                        $answers[] = $option['label'];
                    }
                }
            }

            $this->dbManager->insert($this->environment->answerTable['tableName'], [
                'question_id' => $question['id'],
                'user_id' => $userId,
                'answers' => json_encode($answers),
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        }

       return $form_data;
    }
}
?>