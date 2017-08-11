<?php
/**
 * @package EBay Plugins
 */
/*
Plugin Name: Ebay Seller
Plugin URI: http://www.edeetion.com/ebayseller
Description: 
Version: 4
Author: edeetion.com
Author URI: http://www.edeetion.com
License: http://www.edeetion.com
*/

/**
 * Making it possible to use the application for version 2.6,2.7, 3.x and more
 */
if(!function_exists('plugins_url_internal_function')){
  function plugins_url_internal_function($path = '', $plugin = ''){
    $mu_plugin_dir = WPMU_PLUGIN_DIR;
    foreach ( array('path', 'plugin', 'mu_plugin_dir') as $var ) {
      $$var = str_replace('\\' ,'/', $$var); // sanitize for Win32 installs
      $$var = preg_replace('|/+|', '/', $$var);
    }

    if ( !empty($plugin) && 0 === strpos($plugin, $mu_plugin_dir) )
      $url = WPMU_PLUGIN_URL;
    else
      $url = WP_PLUGIN_URL;

    if ( 0 === strpos($url, 'http') && is_ssl() )
      $url = str_replace( 'http://', 'https://', $url );

    if ( !empty($plugin) && is_string($plugin) ) {
      $folder = dirname(plugin_basename($plugin));
      if ( '.' != $folder )
        $url .= '/' . ltrim($folder, '/');
    }

    if ( !empty($path) && is_string($path) && strpos($path, '..') === false )
      $url .= '/' . ltrim($path, '/');

    return apply_filters('plugins_url', $url, $path, $plugin);
  }
}

/**
 * Making it possible to use the application for version 2.6,2.7, 3.x, 4.x and more
 */
if(!function_exists('plugin_dir_url')){
  /**
   * Gets the URL directory path (with trailing slash) for the plugin __FILE__ passed in
   *
   * @param string $file The filename of the plugin (__FILE__)
   * @return string the URL path of the directory that contains the plugin
   */
  function plugin_dir_url( $file ) {

    return trailingslashit( plugins_url_internal_function( '', $file ) );
  }
}

define('EBAY_SELLER_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('EBAY_SELLER_PLUGIN_PATH', dirname( __FILE__ ));
define('EBAY_SELLER_PLUGIN_VAR_NAME', "ebay_seller_configuration");

function ebay_seller_get_proxy_url(){
  return "http://edeetion.com/ebayproxy/index.js.php?";
  //return "http://localhost/ebayproxy/index.js.php?";
}

if ( is_admin() )
	require_once dirname( __FILE__ ) . '/admin.php';

include_once dirname( __FILE__ ) . '/widget.php';