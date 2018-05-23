<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Providers;

class FormProvider
{
    private $dbManager;
    public function __construct($db_manager)
    {
        $this->dbManager = $db_manager;
    }

    public function get_forms_by_wp_id_and_post_id($wp_form_id, $post_id)
    {
        return $this->dbManager->select("*", "sefab_form", "wp_form_id = 'wpforms-form-$wp_form_id' AND post_id = $post_id");
    }
}
