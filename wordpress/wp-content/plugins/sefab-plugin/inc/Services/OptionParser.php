<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Services;

class OptionParser
{
    public function __construct()
    {
    }

    public function parse($last_question_id, $question_html, $find_type, $form_type)
    {
        $data = [];
        foreach ($question_html->find($find_type) as $input) {
            if ($form_type === 'text' || $form_type === 'textarea' || $form_type === 'number' || $form_type === 'email') {
                $data[] = [
                    "question_id"  => $last_question_id,
                    "option_value" => $input->attr['placeholder']
                ];
            } else {
                $data[] = [
                    "question_id"  => $last_question_id,
                    "option_value" => $input->plaintext,
                ];
            }
        }
        return $data;
    }
}
