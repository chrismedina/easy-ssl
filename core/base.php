<?php
require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'constants.php' );
require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'autoloader.php' );
require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'loader.php' );

    function essl_on_uninstall()
    {
        if ( ! current_user_can( 'activate_plugins' ) )
            return;

        // Important: Check if the file is the one
        // that was registered during the uninstall hook.
        if ( __FILE__ != WP_UNINSTALL_PLUGIN )
            return;
    }

//  BEGIN Register/Enqueue CSS and Javascript
    function essl_load_admin_js(){
        // Unfortunately we can't just enqueue our scripts here - it's too early. So register against the proper action hook to do it
        add_action( 'admin_enqueue_scripts', 'essl_enqueue_admin_js_css' );

    }

    function essl_enqueue_admin_js_css()
    {
        wp_enqueue_script( 'bootstrap', plugin_dir_url(ESSL_FILE) . ESSL_PLG_FOLDER_NAME . '/admin/js/bootstrap.min.js', array('jquery'), null, true );
        wp_enqueue_style( 'bootstrap.min', plugin_dir_url(ESSL_FILE) . ESSL_PLG_FOLDER_NAME . '/admin/css/bootstrap.min.css', array(), null);

        wp_register_style( 'custom_wp_admin_css', plugin_dir_url(ESSL_FILE) . ESSL_PLG_FOLDER_NAME . '/admin/css/style.css',false);
        wp_enqueue_style( 'custom_wp_admin_css' );

        wp_register_style( 'custom_wp_admin_toggle_css', plugin_dir_url(ESSL_FILE) . ESSL_PLG_FOLDER_NAME . '/admin/css/toggle.css',false);
        wp_enqueue_style( 'custom_wp_admin_toggle_css' );
    }
//  END Register/Enqueue CSS and Javascript


    function essl_register_my_custom_menu_page()
    {
        $menu = add_menu_page(
            __( 'Easy SSL', 'textdomain' ),
            'Easy SSL',
            'manage_options',
            'essl_menu_page',
            'essl_initialize',
            'dashicons-shield',
            6
        );

        add_action( 'load-' . $menu, 'essl_load_admin_js' );
    }

    function essl_initialize()
    {
        if( !is_admin() ){
            wp_die( __('Easy SSL is an admin only feature. Please login as admin.', 'easy-ssl'));
            exit;
        }

        // prevent Local File Inclusion
        $allowed_controllers = [ 'ESSL_Controller' ];

        if( isset( $_POST['controller'] )) {
            if( !in_array( $_POST['controller'], $allowed_controllers )) {
                wp_die( __('You do not have permission or that does not exist') );
            }
        }

        $loader = new ESSL_Loader( $_POST );
        $controller =  $loader->CreateController();
        $controller->ExecuteAction();
    }

    // Load TextDomain and Menu for Easy SSL
    function essl_initial() {
        load_plugin_textdomain( 'easy-ssl' );
        add_action( 'admin_menu', 'essl_register_my_custom_menu_page' , 1000 );
    }

    function essl_activate()
    {
        if (is_network_admin()) {
            wp_die( __( 'Easy SSL is not compatible yet with WP Multisite. The PRO version will offer this feature soon.' , 'easy-ssl'));
        }
    }

    function essl_activate_redirect($plugin)
    {
        if( strrpos($plugin, 'easy-ssl' ) !== FALSE ) {
            wp_safe_redirect(
                add_query_arg(
                    array(
                        'page' => 'essl_menu_page'
                    ),
                    admin_url('admin.php')
                )
            );
            exit;
        }
    }

$essl = ESSL_Enforce::getInstance();
add_action( 'plugins_loaded', 'essl_initial' );
add_action( 'activated_plugin', 'essl_activate_redirect' );

register_activation_hook(   __FILE__, 'essl_activate' );
register_uninstall_hook(    __FILE__, 'essl_on_uninstall' );