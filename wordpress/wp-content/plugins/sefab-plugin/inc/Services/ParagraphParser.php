<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Services;

class ParagraphParser
{
    private $dbManager;

    public function __construct($db_manager)
    {
        $this->dbManager = $db_manager;
    }
    
    public function parse($last_post_id, $elements)
    {
        $position  = 0;
        $data = [];

        for ($count = 0; $count < count($elements); $count++) {
            if ($elements[$count]['type'] == "form") {
                $position += 1;
            } elseif ($elements[$count]['type'] == "header") {
                $position += 1;
                //last h tag in the post || next element is a header || next element is a form
                if (($count == count($elements) - 1) || ($elements[$count+1]['type'] == "header") || ($elements[$count+1]['type'] == 'form')) {
                    $data[] = [
                        "post_id"  => $last_post_id,
                        "content"  => "NULL",
                        "header"   => json_encode(array($elements[$count]['with_tag'], $elements[$count]['no_tag'])),
                        "position" => $position
                    ];
                    // $this->dbManager->insert('sefab_paragraph', array(
                    //     "post_id"  => $last_post_id,
                    //     "content"  => "NULL",
                    //     "header"   => json_encode(array($elements[$count]['with_tag'], $elements[$count]['no_tag'])),
                    //     "position" => $position
                    // ));
                }
                //next element is p tag
                elseif ($elements[$count+1]['type'] == "ptag") {
                    $data[] = [
                        "post_id"  => $last_post_id,
                        "content"  => json_encode(array($elements[$count+1]['with_tag'], $elements[$count+1]['no_tag'])),
                        "header"   => json_encode(array($elements[$count]['with_tag'], $elements[$count]['no_tag'])),
                        "position" => $position
                    ];
                    // $this->dbManager->insert('sefab_paragraph', array(
                    //     "post_id"  => $last_post_id,
                    //     "content"  => json_encode(array($elements[$count+1]['with_tag'], $elements[$count+1]['no_tag'])),
                    //     "header"   => json_encode(array($elements[$count]['with_tag'], $elements[$count]['no_tag'])),
                    //     "position" => $position
                    // ));
               
                    $count += 1;
                }
            } elseif ($elements[$count]['type'] == "ptag") {
                $position += 1;
                $data[] = [
                    "post_id"  => $last_post_id,
                    "content"  => json_encode(array($elements[$count]['with_tag'], $elements[$count]['no_tag'])),
                    "header"   => "NULL",
                    "position" => $position
                ];
                // $this->dbManager->insert('sefab_paragraph', array(
                //     "post_id"  => $last_post_id,
                //     "content"  => json_encode(array($elements[$count]['with_tag'], $elements[$count]['no_tag'])),
                //     "header"   => "NULL",
                //     "position" => $position
                // ));
            }
        }

        return $data;
    }
}
