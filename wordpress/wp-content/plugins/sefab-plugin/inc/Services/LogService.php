<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Services;

class LogService
{
    private $environment;

    public function __construct($environment)
    {
        $this->environment = $environment;
    }

    public function Log($file_name, $message)
    {
        if (!$this->environment->isLoggingEnabled) return false;

        $dir = $this->environment->pluginPath . 'inc/logs/';
        $file = $dir . $file_name . '.txt';

        if (file_exists( $file )) {
            $orig_string = file_get_contents($file);
            $f = fopen($file, "w");
            fwrite($f, $orig_string);
            fwrite($f, "\n" . date("Y-m-d H:i:s") .  ": ");
            fwrite($f, $message);
            fclose($f);
        } else {
            if(!file_exists( $dir )){
                mkdir($dir, 0777);
            }
            file_put_contents($file, date("Y-m-d H:i:s") .  ": " . $message);
        }
       
    }
}
