<?php
/**
 * Created by PhpStorm.
 * User: production
 * Date: 1/19/20
 * Time: 2:54 AM
 */

//Constants

$plugin_name = 'easy-ssl';
$plugin_folder_name = dirname( dirname(plugin_basename(__FILE__)) );
$current_folder = dirname( dirname(__FILE__) );

if(!defined('DS')) define( 'DS', DIRECTORY_SEPARATOR );

define( 'ESSL', $plugin_name );

if(!defined('ESSL_PLG_DIR')) define( 'ESSL_PLG_DIR', dirname($current_folder).DS );


define( 'ESSL_DIR', $current_folder . DS . 'app' . DS );

define( 'ESSL_PLG_FOLDER_NAME', $plugin_folder_name );

define( 'ESSL_CLASSES',ESSL_DIR.'classes'.DS );

define( 'ESSL_CONTROLLERS', ESSL_DIR.'controllers'.DS );

define( 'ESSL_MODELS', ESSL_DIR.'models'.DS );

define( 'ESSL_VIEWS', ESSL_DIR.'views'.DS );

define ( 'ESSL_INTERFACES', ESSL_DIR.'interfaces'.DS);

define( 'ESSL_FILE', ESSL_PLG_DIR.'easy-ssl.php' );