<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Services;

class FormDataBuilder {
    
    public function __construct() {
    }

    public function build($data) {
        //ToDo: Construct FormData
        $formData = [];

        $questions = $data['form']['value']['questions'];
        foreach($questions as $key => $question) {
            if ($question['questionType'] === 'select') {
                $formData[$key] =  
                    [
                        'id' => $question['id'],
                        'name' => $question['value'],
                        'value' => $question['selectedOption']
                    ];
            }

            if ($question['questionType'] === 'text' || $question['questionType'] === 'name' || $question['questionType'] === 'email') {
                $formData[$key] =  
                    [
                        'id' => $question['id'],
                        'name' => $question['value'],
                        'value' => ''
                    ];
                
                foreach($question['options'] as $optionKey => $option) {
                    if($optionKey > 0) {
                        $formData[$key]['value'] .= "\n" . $option['value'];
                    } else {
                        $formData[$key]['value'] .= $option['value'];
                    }
                }
            }

            if ($question['questionType'] === 'checkbox') {
                $formData[$key] = 
                    [  
                        'id' => $question['id'],
                        'name' => $question['value'],
                        'value' => ''
                    ];
                
                $selectedValues = 0;
                foreach($question['options'] as $optionKey => $option) {
                    if ($option['value'] === 'true' || $option['value']) {
                        $selectedValues =$selectedValues + 1;
                        
                        if($selectedValues > 1) {
                            $formData[$key]['value'] .= "\n" . $option['label'];
                        } else {
                            $formData[$key]['value'] .= $option['label'];
                        }
                    }
                }
            }

            if ($question['questionType'] === 'radio' || $question['questionType'] === 'rating') {
                $formData[$key] = 
                     [
                        'id' => $question['id'],
                        'name' => $question['value'],
                        'value' => $question['selectedOption']
                    ];
            }
        }

        return $formData;
    }
}
?>