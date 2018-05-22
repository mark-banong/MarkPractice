<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Services;

class EmailContentBuilderService
{
    private $environment;

    public function __construct($environment)
    {
        $this->environment = $environment;

        require_once $this->environment->pluginPath . "vendor/autoload.php";
    }

    public function build_policy_email($policy, $data)
    {
        $return_text = "";

        $top = file_get_contents($this->environment->pluginPath . 'inc/resources/templates/policyEmailTop.html');
        $top = str_replace("{{PolicyTitle}}", $policy["title"], $top);

        $return_text .= $top;
        foreach ($data as $field) {
            $middle = file_get_contents($this->environment->pluginPath . 'inc/resources/templates/policyEmailMiddle.html');

            $middle = str_replace("{{Question}}", $field['name'], $middle);

            $answer = str_replace("\n", " & ", $field['value']);
            $middle = str_replace("{{Answer}}", $answer, $middle);

            $return_text .= $middle;
        }
        $return_text .= file_get_contents($this->environment->pluginPath . 'inc/resources/templates/policyEmailBottom.html');

        return $return_text;
    }

    public function build_report_email($data)
    {
        $return_text = "";
        $template = file_get_contents($this->environment->pluginPath . 'inc/resources/templates/reportEmail.html');
        $template = str_replace("{{error}}", $data['error'], $template);
        $template = str_replace("{{name}}", $data['name'], $template);
        $template = str_replace("{{phoneNumber}}", $data['phoneNumber'], $template);
        $template = str_replace("{{company}}", $data['company'], $template);
        $template = str_replace("{{details}}", $data['details'], $template);

        $return_text .= $template;

        return $return_text;
    }
}
