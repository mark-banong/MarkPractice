<?php
/**
 * @package Sefab Plugin
 */

 /*
 Plugin Name: Sefab Plugin
 Plugin URI: 
 Description: Sefab Plugin manages all the data and UI content for the mobile and web platforms.
 Version: 1.0.0
 Author: MD
 Author URI: 
 License:GPLv2 or later
 Text Domain: Sefab Plugin
 */

 /*
 This program is free software; you can redistribute it and or modify it under the terms
 of the GNU General Public License as published by the Free Software Foundation; either 
 version 2 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 See the GNU General Public License for more details.

 You should have received a copy of the GNU General Public License along with this program;
 if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, 
 Ma 02110-1301, USA.

 Copyright 2005-2015 Automatic, Inc.
 */

//include_once('/parser/simple_html_dom.php');
include_once('parser/simple_html_dom.php');

//for security purposes, to secure our plugin
if( ! defined('ABSPATH')){
    die;
}  

if( file_exists( dirname ( __FILE__ ) . '/vendor/autoload.php' ) ){
    require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

use Inc\Statistics;
use Inc\UI;
use Inc\HtmlParser;
use Inc\Authentication;
use Inc\Dashboard;
use Inc\SefabEnvironment;
use Inc\Initializer;
use Inc\Api;

class SefabPlugin {

    private $authentication;
    private $environment;
    private $dashboard;
    private $htmlParser;
    private $statistics;
    private $api;
    private $ui;

    public function __construct() {
        date_default_timezone_set("Europe/Stockholm");

        //Register Services        
        //Order Matters
        $this->environment = new SefabEnvironment();
        $this->environment->pluginPath = plugin_dir_path( __FILE__ ); 
        $this->environment->pluginUrl = plugins_url(__FILE__);
        
        $init = new Initializer();
        $init->register_services($this->environment);
        
        //ui
        $this->ui = new UI($this->environment);
        $this->ui->run();
        
        //Statistics
        $this->statistics = new Statistics($this->environment, $this->ui);
        $this->statistics->run();
        
        //Authentication
        $this->authentication = new Authentication($this->environment);
        $this->authentication->run();

        //Dashboard
        $this->dashboard = new Dashboard($this->environment);
        $this->dashboard->run();
        
        //Api
        $this->api = new Api($this->environment, $this->authentication, $this->ui, $this->statistics);
        $this->api->run();
        
        //Parser
        $this->htmlParser = new HtmlParser($this->environment, $this->api);
        $this->htmlParser->run();

        //For Debug
        // $dir = plugin_dir_path( __FILE__ );
        // $file = $dir . 'test.txt';
        // file_put_contents($file, json_encode( $init->someVar ));
    }
}
$sefabPlugin = new SefabPlugin();

