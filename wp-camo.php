<?php
/**
* Plugin Name: WP Camo
* Description: Proxy URLS through your WordPress site to prevent mixed content warnings and bypass local filtering
* Version: 1.0.2
* Author: Ed-IT Solutions
* Author URI: https://www.ed-itsolutions.com
* Details URI: https://github.com/Ed-ITSolutions/wp-camo/releases
* Image: https://raw.githubusercontent.com/Ed-ITSolutions/wp-camo/master/logo.png
**/

if(!defined('ABSPATH')) exit;

function wp_camo_run(){
  require_once('lib/class.php');

  $wp_camo = new WPCamo();
}

wp_camo_run();