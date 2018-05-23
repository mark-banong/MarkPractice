<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Services;

class QuestionParser
{
    private $html;

    public function __construct($html)
    {
        $this->html = $html;
    }

    public function parse($last_wp_form_id, $last_form_id, $form_questions)
    {
        $isRating = false;
        $data = [];
        foreach ($form_questions as $form_question) {
            $question = [];

            $question_html = str_get_html($form_question);
            $first_div = $question_html->find('div', 0);
            $wp_field_id = $first_div->attr['data-field-id'];

            //RATING
            if (count($question_html->find('.rating')) > 0) {
                $isRating = true;
                $is_required = count($question_html->find('.wpforms-required-label')) > 0;
                $form_value = ($is_required) ? $this->html->remove_span_tag($question_html->find('label', 0)->plaintext) : $question_html->find('label', 0)->plaintext;

                $description = $this->get_description($question_html);

                $question['data'] = [
                    "wp_question_id" => $wp_field_id,
                    "form_id" => $last_form_id,
                    "wp_form_id" => $last_wp_form_id,
                    "form_title" => $form_value,
                    "form_type" => "RATING",
                    "is_require" => $is_required,
                    "form_description" => $description,
                ];

                $question["optionParams"] = [
                    "question_html" => $question_html,
                    "find_type" => "li",
                    "form_type" => "radio",
                ];
                $data[] = $question;
            }
            //TEXT
            if (count($question_html->find(".wpforms-field-text")) > 0) {
                $is_required = count($question_html->find(".wpforms-required-label")) > 0;
                $form_value = ($is_required) ? $this->html->remove_span_tag($question_html->find("label", 0)->plaintext) : $question_html->find("label", 0)->plaintext;

                $description = $this->get_description($question_html);

                $question["data"] = [
                    "wp_question_id" => $wp_field_id,
                    "form_id" => $last_form_id,
                    "wp_form_id" => $last_wp_form_id,
                    "form_title" => $form_value,
                    "form_type" => "TEXT",
                    "is_require" => $is_required,
                    "form_description" => $description,
                ];

                $question["optionParams"] = [
                    "question_html" => $question_html,
                    "find_type" => "input",
                    "form_type" => "text",
                ];
                $data[] = $question;
            }
            //RADIO
            if (count($question_html->find('.wpforms-field-radio')) > 0 && $isRating == false) {
                $is_required = count($question_html->find('.wpforms-required-label')) > 0;
                $form_value = ($is_required) ? $this->html->remove_span_tag($question_html->find('label', 0)->plaintext) : $question_html->find('label', 0)->plaintext;

                $description = $this->get_description($question_html);

                $question["data"] = [
                    "wp_question_id" => $wp_field_id,
                    "form_id" => $last_form_id,
                    "wp_form_id" => $last_wp_form_id,
                    "form_title" => $form_value,
                    "form_type" => 'RADIO',
                    "is_require" => $is_required,
                    "form_description" => $description,
                ];
                $question["optionParams"] = [
                    "question_html" => $question_html,
                    "find_type" => "li",
                    "form_type" => "radio",
                ];
                $data[] = $question;
            }
            //SELECT
            if (count($question_html->find('.wpforms-field-select')) > 0) {
                $is_required = count($question_html->find('.wpforms-required-label')) > 0;
                $form_value = ($is_required) ? $this->html->remove_span_tag($question_html->find('label', 0)->plaintext) : $question_html->find('label', 0)->plaintext;

                $description = $this->get_description($question_html);

                $question["data"] = [
                    "wp_question_id" => $wp_field_id,
                    "form_id" => $last_form_id,
                    "wp_form_id" => $last_wp_form_id,
                    "form_title" => $form_value,
                    "form_type" => 'SELECT',
                    "is_require" => $is_required,
                    "form_description" => $description,
                ];
                $question["optionParams"] = [
                    "question_html" => $question_html,
                    "find_type" => "option",
                    "form_type" => "select",
                ];
                $data[] = $question;
            }
            //CHECKBOX
            if (count($question_html->find('.wpforms-field-checkbox')) > 0) {
                $find_required_label_result = $question_html->find('.wpforms-required-label');
                $is_required = ($find_required_label_result && count($find_required_label_result) > 0);

                $form_value = ($is_required) ? $this->html->remove_span_tag($question_html->find('label', 0)->plaintext) : $question_html->find('label', 0)->plaintext;

                $description = $this->get_description($question_html);

                $question["data"] = [
                    "wp_question_id" => $wp_field_id,
                    "form_id" => $last_form_id,
                    "wp_form_id" => $last_wp_form_id,
                    "form_title" => $form_value,
                    "form_type" => 'CHECKBOX',
                    "is_require" => $is_required,
                    "form_description" => $description,
                ];
                $question["optionParams"] = [
                    "question_html" => $question_html,
                    "find_type" => "li",
                    "form_type" => "checkbox",
                ];
                $data[] = $question;
            }
            //TEXTAREA
            if (count($question_html->find('.wpforms-field-textarea')) > 0) {
                $is_required = count($question_html->find('.wpforms-required-label')) > 0;
                $form_value = ($is_required) ? $this->html->remove_span_tag($question_html->find('label', 0)->plaintext) : $question_html->find('label', 0)->plaintext;

                $description = $this->get_description($question_html);

                $question["data"] = [
                    "wp_question_id" => $wp_field_id,
                    "form_id" => $last_form_id,
                    "wp_form_id" => $last_wp_form_id,
                    "form_title" => $form_value,
                    "form_type" => 'TEXTAREA',
                    "is_require" => $is_required,
                    "form_description" => $description,
                ];
                $question["optionParams"] = [
                    "question_html" => $question_html,
                    "find_type" => "textarea",
                    "form_type" => "textarea",
                ];
                $data[] = $question;
            }
            //NUMBER
            if (count($question_html->find('.wpforms-field-number')) > 0) {
                $is_required = count($question_html->find('.wpforms-required-label')) > 0;
                $form_value = ($is_required) ? $this->html->remove_span_tag($question_html->find('label', 0)->plaintext) : $question_html->find('label', 0)->plaintext;

                $description = $this->get_description($question_html);

                $question["data"] = [
                    "wp_question_id" => $wp_field_id,
                    "form_id" => $last_form_id,
                    "wp_form_id" => $last_wp_form_id,
                    "form_title" => $form_value,
                    "form_type" => 'NUMBER',
                    "is_require" => $is_required,
                    "form_description" => $description,
                ];
                $question["optionParams"] = [
                    "question_html" => $question_html,
                    "find_type" => "input",
                    "form_type" => "number",
                ];
                $data[] = $question;
            }
            //NAME
            if (count($question_html->find('.wpforms-field-name')) > 0) {
                $is_required = count($question_html->find('.wpforms-required-label')) > 0;
                $form_value = ($is_required) ? $this->html->remove_span_tag($question_html->find('label', 0)->plaintext) : $question_html->find('label', 0)->plaintext;

                $description = $this->get_description($question_html);

                $question["data"] = [
                    "wp_question_id" => $wp_field_id,
                    "form_id" => $last_form_id,
                    "wp_form_id" => $last_wp_form_id,
                    "form_title" => $form_value,
                    "form_type" => 'NAME',
                    "is_require" => $is_required,
                    "form_description" => $description,
                ];
                $question["optionParams"] = [
                    "question_html" => $question_html,
                    "find_type" => "label[class=wpforms-field-sublabel]",
                    "form_type" => "name",
                ];
                $data[] = $question;
            }
            //EMAIL
            if (count($question_html->find('.wpforms-field-email')) > 0) {
                $is_required = count($question_html->find('.wpforms-required-label')) > 0;
                $form_value = ($is_required) ? $this->html->remove_span_tag($question_html->find('label', 0)->plaintext) : $question_html->find('label', 0)->plaintext;

                $description = $this->get_description($question_html);

                $question["data"] = [
                    "wp_question_id" => $wp_field_id,
                    "form_id" => $last_form_id,
                    "wp_form_id" => $last_wp_form_id,
                    "form_title" => $form_value,
                    "form_type" => 'EMAIL',
                    "is_require" => $is_required,
                    "form_description" => $description,
                ];
                $question["optionParams"] = [
                    "question_html" => $question_html,
                    "find_type" => "input",
                    "form_type" => "email",
                ];
                $data[] = $question;
            }
        }
        return $data;
    }

    private function get_description($question_html)
    {
        $find_description_result = $question_html->find('.wpforms-field-description', 0);
        $is_present_description = ($find_description_result && count($find_description_result) > 0);
        $description = ($is_present_description) ? $find_description_result->innertext : 'NULL';

        return $description;
    }
}
