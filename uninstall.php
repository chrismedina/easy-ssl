<?php
// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

if ( ! current_user_can( 'delete_plugins' ) )
    return;

$essl_model = new ESSL_Settings_Model;

$essl_model->deleteOptions();

?>