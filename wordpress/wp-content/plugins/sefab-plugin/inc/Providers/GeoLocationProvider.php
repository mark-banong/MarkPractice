<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Providers;

class GeoLocationProvider
{
    private $environment;
    public function __construct($environment)
    {
        $this->environment = $environment;
        require_once $this->environment->pluginPath . "vendor/autoload.php";
    }
    

    public function get_phone_country_code_by_ip()
    {
        $country_code;
        $location_info = $this->get_location_by_id();

        if ($location_info["country"] === "PH") {
            $country_code = "+63";
        } elseif ($location_info["country"] === "SE") {
            $country_code = "+46";
        }

        return $country_code;
    }

    public function get_location_by_id()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        
        if ($ip === "::1") {
            $externalContent = file_get_contents('http://checkip.dyndns.com/');
            preg_match('/Current IP Address: \[?([:.0-9a-fA-F]+)\]?/', $externalContent, $m);
            $ip = $m[1];
        }

        $ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));
        if ($ip_data && $ip_data->geoplugin_countryName != null) {
            $result['country'] = $ip_data->geoplugin_countryCode;
            $result['city'] = $ip_data->geoplugin_city;
        }
        return $result;
    }
}
