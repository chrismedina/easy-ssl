<?php
// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

delete_opts('easyssl_options');

// for site options in Multisite
delete_opts('easyssl_network_options');

function delete_opts( $option_name) {
    delete_option( $option_name );


    //https://developer.wordpress.org/plugins/plugin-basics/uninstall-methods/  Multisite
    delete_site_option( $option_name );
}

?>