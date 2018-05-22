<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Services;

class FormParser
{
    public function __construct()
    {
    }

    public function parse($last_post_id, $elements)
    {
        $position = 0;
        $data = [];
        for ($count_form = 0; $count_form < count($elements); $count_form++) {
            $form = [];
            if ($elements[$count_form]['type'] == 'header' && $elements[$count_form+1]['type'] == 'ptag') {
                $position += 1;
                 
                $count_form += 1;
            } elseif ($elements[$count_form]['type'] == 'header' && $elements[$count_form+1]['type'] == 'form') {
                $position += 1;
            } elseif ($elements[$count_form]['type'] == 'ptag') {
                $position += 1;
            } elseif ($elements[$count_form]['type']  == 'form') {
                $position += 1;
                $last_form_id;
                $oldFormId;
                $title = ($elements[$count_form]['form_title'] != null) ?  $elements[$count_form]['form_title'] : 'NULL';
                $description = ($elements[$count_form]['form_description'] != null) ?  $elements[$count_form]['form_description'] : 'NULL';
                
                $form['data'] = [
                    "wp_form_id" => $elements[$count_form]['wp_form_id'],
                    "post_id" => $last_post_id,
                    "title" => $title,
                    "form_description" => $description,
                    "position" => $position
                ];

                //Get Questions
                $questions_html = str_get_html($elements[$count_form]['form']);
                $form_questions = $questions_html->find('.wpforms-field-text,.wpforms-field-radio,.wpforms-field-select,.wpforms-field-checkbox,.wpforms-field-textarea,.wpforms-field-number,.wpforms-field-name,.wpforms-field-email');

                $form["question_params"] = [
                    "form_questions_html" => $form_questions
                ];

                $data[] = $form;
            }
        }
        return $data;
    }
}
