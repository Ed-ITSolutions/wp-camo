<?php
/**
* Plugin Name: WP Camo
* Description: Proxy URLS through your WordPress site to prevent mixed content warnings and 
* Version: 0.0.2
* Author: Ed-IT Solutions
* Author URI: http://www.ed-itsolutions.com
**/


require_once('vendor/autoload.php');

function wp_camo_run(){
  require_once('lib/class.php');

  wup_client('plugin', 'wp-camo', 'https://www.ed-it.solutions/wup/wp-camo');

  $wp_camo = new WPCamo();
}

wp_camo_run();