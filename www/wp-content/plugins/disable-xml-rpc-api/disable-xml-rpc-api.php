<?php
/*
Plugin Name: Disable XML-RPC-API
Plugin URI: https://neatma.com/dsxmlrpc-plugin/
Description: Lightweight plugin to disable XML-RPC API and Pingbacks,Trackbacks for faster and more secure website.
Version: 2.1.2
Tested up to: 5.8
Requires at least: 3.5
Author: Neatma
Author URI: https://neatma.com/
License: GPLv2
*/

//
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define('DSXMLRPC_FILE', plugin_dir_path(__FILE__));
define('DSXMLRPC_URL', plugin_dir_url( __FILE__ ));
define('DSXMLRPC_HOME_PATH', function_exists('get_home_path') ? get_home_path() : ABSPATH);
register_activation_hook( __FILE__, 'dsxmlrpc_add_htaccess' );
register_uninstall_hook( __FILE__, 'dsxmlrpc_uninstall_action' );



if ( ! class_exists( 'PAnD' ) ) {
require_once(DSXMLRPC_FILE . '/lib/admin-notices/persist-admin-notices-dismissal.php');
}

require_once(DSXMLRPC_FILE . '/admin/admin.php');
require_once (DSXMLRPC_FILE . '/lib/skelet/framework.config.php');

add_action( 'admin_init', array( 'PAnD', 'init' ) );


//
// Get options
function dsxmlrpc_get_option($option){
		$options = get_option( 'dsxmlrpc-settings' );
		if (isset($options[$option])){
			return $options[$option];
		}
}

//
// Fix IP list
function dsxmlrpc_fix_ip($type){
	if (!dsxmlrpc_get_option($type))  return;
		$ip_list = dsxmlrpc_get_option($type);
		$ips = explode(",",$ip_list);
		foreach ($ips as $ip)
		{
			$ip = trim($ip);
			if(!filter_var( $ip, FILTER_VALIDATE_IP ) === false){
				if ($type == 'White-list-IPs') {
				return "Allow from ".$ip. "\n";
				} elseif ($type == 'Black-list-IPs') {
				return "Deny from ".$ip. "\n";

				}
			}
		}
}



//
// Fix htaccess permissions
function dsxmlrpc_file_chmod() {
	$htaccess_file = DSXMLRPC_HOME_PATH . '.htaccess';
	if (!is_writable($htaccess_file)){
	  chmod($htaccess_file, 0644);
	}

}

//
// Fix htaccess permissions
function dsxmlrpc_file_protect() {
	$htaccess_file = DSXMLRPC_HOME_PATH . '.htaccess';
	if (is_writable($htaccess_file)){
	  chmod($htaccess_file, 0444);
	}
}


//
// Disable access to xmlrpc.php completely with .htaccess file
function dsxmlrpc_add_htaccess() {
		 global $current_screen;
    	if ( $current_screen->id == 'toplevel_page_Security Settings' || $current_screen->id == 'dashboard' ) {
		dsxmlrpc_hotlinkfix();
		
	if (dsxmlrpc_get_option('jetpack-switcher')) {
$jp_allowed_ips = '
Allow from 122.248.245.244/32
Allow from 54.217.201.243/32
Allow from 54.232.116.4/32
Allow from 192.0.80.0/20
Allow from 192.0.96.0/20
Allow from 192.0.112.0/20
Allow from 195.234.108.0/22
Allow from 192.0.96.202/32
Allow from 192.0.98.138/32
Allow from 192.0.102.71/32
Allow from 192.0.102.95/32';
	} else {
		$jp_allowed_ips = '';
	}


	if (!dsxmlrpc_get_option('dsxmlrpc-switcher') ) {

		 $dsxmlrpc_allowed_ips = dsxmlrpc_fix_ip('White-list-IPs') . $jp_allowed_ips;
$htaccess_code =
'<Files xmlrpc.php>
order deny,allow
deny from all
'.$dsxmlrpc_allowed_ips.'
</Files>
';
	} else {

	$dsxmlrpc_disallowed_ips =  dsxmlrpc_fix_ip('Black-list-IPs');
$htaccess_code =
'<Files xmlrpc.php>
order allow,deny
allow from all
'.$dsxmlrpc_disallowed_ips.'
</Files>
';
	}
		dsxmlrpc_file_chmod();
		insert_with_markers(DSXMLRPC_HOME_PATH . '.htaccess' , 'DS-XML-RPC-API', $htaccess_code);
		dsxmlrpc_get_option('htaccess protection') ? dsxmlrpc_file_protect() : '' ;
			
		}
} add_action('admin_head', 'dsxmlrpc_add_htaccess' );



//
//Remove .htaccess codes when disabled
function dsxmlrpc_remove_htaccess($plugin) {
	if ($plugin !== 'disable-xml-rpc-api/disable-xml-rpc-api.php')	{
		return;
	}
    $filename = DSXMLRPC_FILE . '/admin/dsxmlrpc-htaccess';
    $htaccess_file = DSXMLRPC_HOME_PATH . '.htaccess';
	if(!is_writable ($htaccess_file) ) {
		dsxmlrpc_file_chmod();
	    insert_with_markers($htaccess_file, 'DS-XML-RPC-API', '');
		dsxmlrpc_get_option('htaccess protection') ? dsxmlrpc_file_protect() : '' ;
	} else {
	    insert_with_markers($htaccess_file, 'DS-XML-RPC-API', '');
		dsxmlrpc_get_option('htaccess protection') ? dsxmlrpc_file_protect() : '' ;
	}
		delete_option( 'pand-' . md5('wpsg-notice') );
		delete_option( 'pand-' . md5('dsxmlrpc-notice') );
}
add_action( 'deactivated_plugin' , 'dsxmlrpc_remove_htaccess', 10, 2);

//
//  Unistallation actions
function dsxmlrpc_uninstall_action(){
	delete_option( 'dsxmlrpc-settings' );
	delete_option('pand-' . md5('wpsg-notice') );
	delete_option('pand-' . md5('dsxmlrpc-notice') );

}

// Update actions
function dsxmlrpc_after_update( $upgrader_object, $options ) {
    $current_plugin_path_name = plugin_basename( __FILE__ );
 
    if ($options['action'] == 'update' && $options['type'] == 'plugin' ) {
       foreach($options['plugins'] as $each_plugin) {
          if ($each_plugin==$current_plugin_path_name) {
             delete_option('pand-' . md5('wpsg-notice') );
 
          }
       }
    }
}
add_action('upgrader_process_complete', 'dsxmlrpc_after_update',10, 2);

//
// Disable XML-RPC Methods
function dsxmlrpc_dis_methods($xmlrpc) {
$methods = dsxmlrpc_get_option('disabled-methods');
	foreach($methods as $method) {

      unset( $xmlrpc[$method] );
		} return $xmlrpc;

}
if (dsxmlrpc_get_option('dsxmlrpc-switcher')){
	add_filter( 'xmlrpc_methods',  'dsxmlrpc_dis_methods' );
}



//
// Get XML-RPC Disabled Methods
function dsxmlrpc_get_methods($method) {
			$option = dsxmlrpc_get_option('disabled-methods');
			if(in_array($method,$option)){
				return array($method);
			}

	}


// Remove x-pingback from header
function dsxmlrpc_X_pingback_header( $headers ) {
   unset( $headers['X-Pingback'] );
         return $headers;
}

// Remove selected methods from xml rpc
$dsxmlrpc_disabled_methods = dsxmlrpc_get_option('disabled-methods');
if (is_array($dsxmlrpc_disabled_methods)) {
	if(dsxmlrpc_get_option('dsxmlrpc-switcher') && array_search('x-pingback',$dsxmlrpc_disabled_methods)) {
	add_filter( 'wp_headers', 'dsxmlrpc_X_pingback_header' );
	add_filter('pings_open', '__return_false', PHP_INT_MAX);
	}
}


if( !empty(dsxmlrpc_get_option('xmlrpc-slug')) && dsxmlrpc_get_option('dsxmlrpc-switcher')){

	add_action('wp_loaded', 'dsxmlrpc_xmlrpc_rename_wp_loaded');

}


// Rename the XML-RPC
function dsxmlrpc_xmlrpc_rename_wp_loaded(){

	$page = dsxmlrpc_cur_page();

	if ($page === 'xmlrpc.php') {
	$header_one = apply_filters('dsxmlrpc_header_1', 'HTTP/1.0 404 Not Found');
	$header_two = apply_filters('dsxmlrpc_header_2', 'Status: 404 Not Found');

	header($header_one);
	header($header_two);

	exit();
	}

	if($page !== dsxmlrpc_get_option('xmlrpc-slug')){
		return false;
	}

	@define('NO_CACHE', true);
	@define('WTC_IN_MINIFY', true);
	@define('WP_CACHE', false);

	// Prevent errors from defining constants again
	error_reporting(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR);

	include ABSPATH.'/xmlrpc.php';

	exit();

}

// Find the page being accessed
function dsxmlrpc_cur_page(){

	$blog_url = trailingslashit(get_bloginfo('url'));

	// Build the Current URL
	$url = (is_ssl() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

	if(is_ssl() && preg_match('/^http\:/is', $blog_url)){
		$blog_url = substr_replace($blog_url, 's', 4, 0);
	}

	// The relative URL to the Blog URL
	$req = str_replace($blog_url, '', $url);
	$req = str_replace('index.php/', '', $req);

	// We dont need the args
	$parts = explode('?', $req, 2);
	$relative = basename($parts[0]);

	// Remove trailing slash
	$relative = rtrim($relative, '/');
	$tmp = explode('/', $relative, 2);
	$page = end($tmp);

	return $page;

}

//
// Speed Up wordprees

		/* remove emoji */
		if ( dsxmlrpc_get_option('remove-emojis') ) {
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'wp_print_styles', 'print_emoji_styles' );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
			remove_action( 'admin_print_styles', 'print_emoji_styles' );
			remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
			remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
			remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		}
		/* slow down the heartbeat */
		if ( dsxmlrpc_get_option('slow-heartbeat') ) {
			add_filter( 'heartbeat_settings', 'dsxmlrpc_slow_heartbeat'  );
		}
		/* remove rss */
		if ( dsxmlrpc_get_option('remove-rss') ) {

			remove_action( 'wp_head', 'rsd_link' );
			remove_action( 'wp_head', 'feed_links', 2 );
			remove_action( 'wp_head', 'feed_links_extra', 3 );
		function dsxmlrpc_disable_feed() {
		wp_die( __('No feed available,please visit our <a href="'. get_bloginfo('url') .'">homepage</a>!') );
		}
		add_action('do_feed', 'dsxmlrpc_disable_feed', 1);
		add_action('do_feed_rdf', 'dsxmlrpc_disable_feed', 1);
		add_action('do_feed_rss', 'dsxmlrpc_disable_feed', 1);
		add_action('do_feed_rss2', 'dsxmlrpc_disable_feed', 1);
		add_action('do_feed_atom', 'dsxmlrpc_disable_feed', 1);
		add_action('do_feed_rss2_comments', 'dsxmlrpc_disable_feed', 1);
		add_action('do_feed_atom_comments', 'dsxmlrpc_disable_feed', 1);
		}
		/* Disable wp-json rest api */
		if ( dsxmlrpc_get_option('json-rest-api') ) {
			add_filter( 'rest_authentication_errors', function( $result ) {
				if ( ! empty( $result ) ) {
					return $result;
				}
				if ( ! is_user_logged_in() ) {
					return new WP_Error( 'restx_logged_out', 'Sorry, you must be logged in to make a request.', array( 'status' => 401 ) );
				}
				return $result;
			});
		}
		
		/* remove wlw from manifest */
		if ( dsxmlrpc_get_option('disable-wlw') ) {
			remove_action( 'wp_head', 'wlwmanifest_link' );
		}
		/* disable built-in file editor */
		if ( dsxmlrpc_get_option('disable-code-editor') && !defined('DISALLOW_FILE_EDIT') ) {
			define( 'DISALLOW_FILE_EDIT', true );
		}
		/* disable oEmbed for youtube */
		if ( dsxmlrpc_get_option('disable-oembed') ) {
			add_action( 'wp_footer', 'dsxmlrpc_disable_oembed', 11 );
		}
		/* Remove the WordPress version info url parameter. */
		if ( dsxmlrpc_get_option('remove-wp-ver') ) {
			remove_action( 'wp_head', 'wp_generator' );
			add_filter( 'script_loader_src', 'dsxmlrpc_remove_ver_param'  );
			add_filter( 'style_loader_src', 'dsxmlrpc_remove_ver_param'  );
		}
		

	/**
	 * Remove the WordPress version info url parameter.
	 */
	 function dsxmlrpc_remove_ver_param( $url ) {
		return remove_query_arg( 'ver', $url );
	}
	/* Slow down the wordpress hearbeat */
	 function dsxmlrpc_slow_heartbeat( $settings ) {
	    $settings['interval'] = 60;
		return $settings;
	}

	/**
	 * Dequeue the oEmbed script.
	 */
	 function dsxmlrpc_disable_oembed() {
		wp_dequeue_script( 'wp-embed' );
	}


	/**
	 * Fix hotlink issue.
	 */
	function dsxmlrpc_hotlinkfix() {
		if ( dsxmlrpc_get_option('hotlink-fix') ) {
			
	$home_url =  get_home_url();
$htaccess_code = '
RewriteEngine on
RewriteCond %{HTTP_REFERER} !^$
RewriteCond %{HTTP_REFERER} !^'. $home_url .' [NC]
RewriteCond %{HTTP_REFERER} !^http(s)?://(www\.)?google.com [NC]
RewriteRule \.(jpg|jpeg|png|gif)$ – [NC,F,L] ';

		dsxmlrpc_file_chmod();
		insert_with_markers(DSXMLRPC_HOME_PATH . '.htaccess' , 'DS-XML-RPC-FIX-HOTLINK', $htaccess_code);
		dsxmlrpc_get_option('htaccess protection') ? dsxmlrpc_file_protect() : '' ;
		} else {
		dsxmlrpc_file_chmod();
		insert_with_markers(DSXMLRPC_HOME_PATH . '.htaccess' , 'DS-XML-RPC-FIX-HOTLINK', '');
		dsxmlrpc_get_option('htaccess protection') ? dsxmlrpc_file_protect() : '' ;
		}
	}
