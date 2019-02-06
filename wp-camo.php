<?php
/**
* Plugin Name: WP Camo
* Description: Proxy URLS through your WordPress site to prevent mixed content warnings and 
* Version: 1.0.0
* Author: Ed-IT Solutions
* Author URI: http://www.ed-itsolutions.com
* Details URI: https://github.com/Ed-ITSolutions/wp-camo/releases
* Image: https://raw.githubusercontent.com/Ed-ITSolutions/wp-camo/master/logo.png
**/


require_once('vendor/autoload.php');

function wp_camo_run(){
  require_once('lib/class.php');

  wup_client('plugin', 'wp-camo', 'https://wp.ed-it.solutions/wup/wp-camo');

  $wp_camo = new WPCamo();
}

wp_camo_run();