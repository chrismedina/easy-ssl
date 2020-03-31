<?php
/**
 * Plugin Name: Easy SSL
 * Plugin URI: https://www.easyssl.cc
 * Description: One button click to activate SSL, easy. http to HTTPS for SEO is also optional.
 * Version: 1.0
 * Text Domain: easy-ssl
 * Author: Chris Medina
 * Author URI: https://www.chrismedinaphp.com
 * License: GPLv2
 */

/*  Copyright 2020  easySSL.cc  (email : support@easySSL.cc)
    This plugin was originally developed by Chris Medina (https://chrismedinaphp.com/).

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
    the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
*/

//configured for wp-content/plugins/ directory to be moved, so you must use plugin_dir_path() and plugins_url() for absolute paths and URLs. See:
//https://wordpress.org/support/plugin/easy-ssl/reviews/?filter=5
defined('ABSPATH') or die("No script kiddies please!");

define( 'ESSL_REQUIRED_PHP_VERSION', '5.6' );  // because of WordPress minimum requirements
define( 'ESSL_REQUIRED_WP_VERSION',  '3.1' );    // because of get_current_screen()

/**
 * Checks if the system requirements are met
 * @return bool True if system requirements are met, false if not
 */
function essl_requirements_met() {
    global $wp_version;
    require_once( ABSPATH.DIRECTORY_SEPARATOR.'wp-admin'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'plugin.php' );

    if ( version_compare( PHP_VERSION, ESSL_REQUIRED_PHP_VERSION, '<' ) ) {
        return false;
    }

    if ( version_compare( $wp_version, ESSL_REQUIRED_WP_VERSION, '<' ) ) {
        return false;
    }

    return true;
}

function essl_requirements_error() {
    global $wp_version;

    require_once( dirname( __FILE__ ) .DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'requirements-error.php' );
}
/*
 * Check requirements and load main class
 */
if ( essl_requirements_met() ) {
    require_once( dirname( __FILE__ ) .DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'base.php' );

} else {
    add_action( 'admin_notices', 'essl_requirements_error' );
}


add_filter( 'plugin_action_links', 'easyssl_wpmdr_add_action_plugin', 10, 5 );
function easyssl_wpmdr_add_action_plugin( $actions, $plugin_file )
{
    static $plugin;

    if (!isset($plugin))
        $plugin = plugin_basename(__FILE__);
    if ($plugin == $plugin_file) {

        $settings = array('settings' => '<a href="admin.php?page=essl_menu_page">' . __('Settings', 'General') . '</a>');
        $site_link = array('support' => '<a href="https://easyssl.cc" target="_blank">Support</a>');

        $actions = array_merge($settings, $actions);
        $actions = array_merge($site_link, $actions);

    }

    return $actions;
}