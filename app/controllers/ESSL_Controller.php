<?php

class ESSL_Controller extends ESSL_BaseController
{

    protected  $options = array(
        'essl' => [],
        'essl_file'
    );

    protected $time_length  = 300;

    protected $response = [];

    //Default view
    function index()
    {
        $model = new ESSL_Settings_Model;
        $errors = [];

        // $_POST exists ?
        if( ESSL_Form_Validation::exists() ) {
            //Permissions ?
            if( !is_admin() || ! current_user_can( 'update_plugins' ))  wp_die( 'Sorry you do not have access to this feature. Please login with appropriate permission' );

            //NONCE check
            if (!isset($_POST['essl_aiowz_tkn'])) die("Hmm .. looks like you didn't send any credentials.. No CSRF for you! ");
            if (!wp_verify_nonce($_POST['essl_aiowz_tkn'],'essl-csrf-index-nonce')) die("Hmm .. looks like you didn't send any credentials.. No CSRF for you! ");

            //get existing options
            $option_array = $model->get_essl_options();

            if( !isset($_POST['wp_ssl'])) $option_array['wp_ssl'] = false;
            if( !isset($_POST['hsts'])) $option_array['hsts'] = false;

            $option_array = array_merge( $option_array, $_POST );

            //add or update option
            $model->set_essl_options( $option_array );

        }

        $file_name = ( $model->get_file_backup_option() !== FALSE ) ?  true : false;

        $this->ReturnView( array( 'settings' => [ 'essl' => $model->get_essl_options(),
                                                  'essl_file' => $file_name ],
                                                  'errors' => $model->getErrors() ), true   );
    }

    //REVERT
    function revert_configuration()
    {
        //Permissions ?
        if( !is_admin() || ! current_user_can( 'update_plugins' ))  wp_die( 'Sorry you do not have access to this feature. Please login with appropriate permission' );
        if ( !isset( $_POST['essl_aiowz_rc_tkn'] )) die("Hmm .. looks like you didn't send any credentials.. No CSRF for you! ");
        if ( !wp_verify_nonce( $_POST['essl_aiowz_rc_tkn'],'essl-csrf-rc-nonce' )) die("Hmm .. looks like you didn't send any credentials.. No CSRF for you! ");

            $model = new ESSL_Settings_Model;
            $backup_file = $model->get_file_backup_option();

            if( isset( $backup_file )){
                $copy_file_dir_pos = strrpos( $backup_file, DIRECTORY_SEPARATOR );
                if( !$copy_file_dir_pos ){
                    $this->response['serverconfig']['output']['revert'] = __("Could not revert due to DIRECTORY_SEPARATOR / file not found issue", 'easy-ssl');
                    $this->server_configuration();
                    return;
                }

                $copy_file_dir = substr( $backup_file, 0, $copy_file_dir_pos );

                //is it .htaccess or web.config file
                if( strpos( $backup_file, '.htaccess') !== FALSE ) {
                    $specify_config_filename = '.htaccess';
                }elseif( strpos( $backup_file, 'web.config' ) !== FALSE ){
                    $specify_config_filename = 'web.config';
                }else{
                    $this->server_configuration();
                    return;
                }

                $config = new ESSL_Config;
                if( ! $config->rollbackCopying( ABSPATH, $backup_file, $copy_file_dir, $specify_config_filename ) )
                {
                    $this->response['serverconfig']['output']['revert'] = $config->getRollbackError();
                    $this->server_configuration();
                    return;
                }

                //delete backup file option
                $model->delete_file_backup_option();

                //htaccess_ssl /  webconfig_ssl option , turn off/false
                $essl_options = $model->get_essl_options();
                if(isset($essl_options['htaccess_ssl'])) $essl_options['htaccess_ssl'] = false;
                if(isset($essl_options['webconfig_ssl'])) $essl_options['webconfig_ssl'] = false;

                $model->set_essl_options( $essl_options );

                $this->response['errors'] = $model->getErrors();
            }


        $this->server_configuration( false );
    }

    //Check current web server, OS, and for .htaccess or web.confg
    function server_configuration( $know_config_already = false)
    {
        //Permissions ?
        if( !is_admin() || ! current_user_can( 'update_plugins' ))  wp_die( 'Sorry you do not have access to this feature. Please login with appropriate permission' );

        if( isset( $_POST['action'] )){
            if( $_POST['action']== 'server_configuration') {
                if (!isset($_POST['essl_aiowz_sc_tkn'])) die("Hmm .. looks like you didn't send any credentials.. No CSRF for you! ");
                if (!wp_verify_nonce($_POST['essl_aiowz_sc_tkn'],'essl-csrf-sc-nonce')) die("Hmm .. looks like you didn't send any credentials.. No CSRF for you! ");
            }
        }

        $config_check = new ESSL_ServerConfig;
        $htaccess_file = false;
        $webconfig_file = false;
        $apache = false;
        $windows = false;
        $example = '';
        $critical_error = false;

        if( ! $know_config_already ) {
            if( $config_check->isWindows() ){
                $this->response['serverconfig']['output']['success'][] = __('Windows OS detected', 'easy-ssl' );
                $windows = true;
                //windows but no webconfig
                if( !$config_check->webConfigExists()){
                    $critical_error = true;
                    $this->response['serverconfig']['output']['warning'][] =  __('web.config file NOT found', 'easy-ssl' );
                    //send ExampleConfig so they can do it manually
                }else{
                    //webconfig found
                    $webconfig_file = true;
                    $this->response['serverconfig']['output']['success'][] = __('.webconfig file found in WP path', 'easy-ssl' );
                }
            }

            if( $config_check->isApache() ){
                //apache but no htaccess
                $this->response['serverconfig']['output']['success'][] = __('Apache detected', 'easy-ssl' );
                $apache = true;

                if( !$config_check->isModRewrite_module() ) {
                    $critical_error = true;
                    $this->response['serverconfig']['output']['warning'][]= __('Mod Rewrite NOT detected (.htaccess requires this)', 'easy-ssl' );
                }else{
                    $this->response['serverconfig']['output']['success'][]= __('Mod Rewrite detected (.htaccess usable)', 'easy-ssl' );
                }

                if( !$config_check->htaccessExists() ){
                    $critical_error = true;
                    $this->response['serverconfig']['output']['warning'][] = __('.htaccess file NOT found', 'easy-ssl' );
                    //send ExampleConfig so they can do it manually
                }else{
                    //htaccess found
                    $htaccess_file = true;
                    $this->response['serverconfig']['output']['success'][] = __('.htaccess file found in WP path', 'easy-ssl' );
                }
            }
        }

        $model = new ESSL_Settings_Model;
        $this->response['settings']['essl'] = $model->get_essl_options();
        $this->response['settings']['essl_file'] = $model->get_file_backup_option();

        $apache_complete = false;
        if( $apache && $htaccess_file )  $apache_complete = true;

        $windows_complete = false;
        if( $windows && $webconfig_file ) $windows_complete = true;

        $this->response['serverconfig']['example'] = $example;
        $this->response['serverconfig']['critical'] = $critical_error;
        $this->response['serverconfig']['htaccess'] = $htaccess_file;
        $this->response['serverconfig']['webconfig'] = $webconfig_file;
        $this->response['serverconfig']['apache'] = $apache;
        $this->response['serverconfig']['windows'] = $windows;
        if($apache_complete) $this->response['serverconfig']['apache_complete'] = $apache_complete;
        if($windows_complete) $this->response['serverconfig']['windows_complete'] = $windows_complete;

        $this->ReturnView( $this->response, true );
    }

    function write_configuration()
    {
        //Permissions ?
        if( !is_admin() || ! current_user_can( 'update_plugins' ))  wp_die( 'Sorry you do not have access to this feature. Please login with appropriate permission' );

        if ( !isset( $_POST['essl_aiowz_wc_tkn'] )) die("Hmm .. looks like you didn't send any credentials.. No CSRF for you! Not set. " );
        if ( !wp_verify_nonce( $_POST['essl_aiowz_wc_tkn'],'essl-csrf-wc-nonce' )) die("Hmm .. looks like you didn't send any credentials.. No CSRF for you! ");

        $htaccess = false;
        $successful = false;

        $error_output = '';

        if( function_exists('fsockopen') && function_exists('file_get_contents')) {

            $copy_to_dir = ESSL_PLG_DIR . ESSL_PLG_FOLDER_NAME . DS . 'backups';

            $config_check = new ESSL_ServerConfig;

            $config_object = $config_check->getConfigObject($copy_to_dir);

            if ($config_object instanceof ESSL_Config) {

                if (get_class($config_object) == 'ESSL_htaccess') {
                    $htaccess = true;
                    if (!$config_check->isModRewrite_module()) {
                        if ( $config_object->writeAppendEnvVariable( ABSPATH . '.htaccess', 'HTTP_MOD_REWRITE', 'on' )) {
                            if (!$config_check->isModRewrite()) {
                                $config_object->setCopyErrorOutput(__('Appended to .htaccess but still no environment variable loaded. HTACCESS not working. ', 'easy-ssl'));
                            }
                        }
                    }
                }

                $error_output = $config_object->getErrorOutput();
                if ( !$error_output ) {
                    //write fresh file
                    if ( $config_object->writeFreshConfig(ABSPATH . $config_object->getConfigFilename(), $config_object->exampleConfig() )) {

                        //if writing new config, test accessing home URL in a new request (test twice)
                        $home_url = get_home_url();

                        $the_content = @file_get_contents( $home_url );

                        if ( strpos( $http_response_header[0], '200' ) && strpos( $home_url, 'https') == 0 ) {
                            $successful = true;
                        } elseif ( strpos( $http_response_header[0], '301' )) {
                            $headers = $this->get_headers_deux($home_url, 1);

                            //only returning 301 and not content
                            if (isset($headers['Location'])) {
                                $the_content = @file_get_contents( $headers['Location'] );
                                //sleep(1);
                                if ( strpos( $http_response_header[0], '200' )) {
                                    $successful = true;
                                } else {
                                    $error_output .= 'Did not get a positive 200 response so this is a problem. ROLLING BACK!';

                                    //roll back copying and rename to original location
                                    if ($copied_file = $config_object->getCopiedFile()) {
                                        if ($config_object->rollbackCopying(ABSPATH, $copied_file, $copy_to_dir)) {
                                            $error_output .= __('Rolled back copy of config and restored original. Test of HTTPS after rewrite of config did not work successfully.', 'easy-ssl');
                                        }
                                    }
                                }
                            }

                        } else {  //Not a 301 redirect that we can test, and not 200
                            $headers = $this->get_headers_deux($home_url, 0);

                            if ($copied_file = $config_object->getCopiedFile()) {
                                if ($config_object->rollbackCopying(ABSPATH, $copied_file, $copy_to_dir)) {
                                    $error_output .= __('Rolled back copy of config and restored original. Test of HTTPS after rewrite of config did not work successfully.', 'easy-ssl');
                                }
                            }
                        }

                        $error_output .= $config_object->getErrorOutput();
                    } else {

                        if ($copied_file = $config_object->getCopiedFile()) {
                            if ($config_object->rollbackCopying(ABSPATH, $copied_file, $copy_to_dir)) {
                                $error_output .= __('Rolled back copy of config and restored original. Test of HTTPS after rewrite of config did not work successfully.', 'easy-ssl');
                            }
                        }

                        $error_output .= $config_object->getWriteError();
                    }

                    //ToDo: if write fresh fails, try to modify
                }
            } else {
                $error_output = '.htaccess file was not found or mod_rewrite was not enabled in apache';
            }

            if ($successful) {
                $model = new ESSL_Settings_Model;

                if($htaccess){
                    $option_array['htaccess_ssl'] = true;
                    $this->response['serverconfig']['apache_complete'] = true;
                }else{ //windows
                    $option_array['webconfig_ssl'] = true;
                    $this->response['serverconfig']['windows_complete'] = true;
                }

                //get existing options
                $model->get_essl_options();
                //add or update option
                $model->set_essl_options( $option_array );

                $copied_file = $config_object->getCopiedFile();

                $model->set_file_backup_option( [ 'essl_file' => $copied_file ] );
            }

            $this->response['serverconfig']['errors'] = $error_output;
            $this->server_configuration();
        }else{
            $this->response['serverconfig']['errors'] = __('Required functions: fsockopen and/or file_get_contents were not found. These are required to properly test HTTPS using web config files.', 'easy-ssl');
            $this->server_configuration();
        }
    }

    function get_headers_deux($url,$format=0)
    {
        $url=parse_url($url);
        $end = "\r\n\r\n";
        $fp = fsockopen($url['host'], (empty($url['port'])?80:$url['port']), $errno, $errstr, 30);
        if ($fp)
        {
            $out  = "GET / HTTP/1.1\r\n";
            $out .= "Host: ".$url['host']."\r\n";
            $out .= "Connection: Close\r\n\r\n";
            $var  = '';
            fwrite($fp, $out);
            while (!feof($fp))
            {
                $var.=fgets($fp, 1280);
                if(strpos($var,$end))
                    break;
            }
            fclose($fp);

            $var=preg_replace("/\r\n\r\n.*\$/",'',$var);
            $var=explode("\r\n",$var);
            if($format)
            {
                foreach($var as $i)
                {
                    if(preg_match('/^([a-zA-Z -]+): +(.*)$/',$i,$parts))
                        $v[$parts[1]]=$parts[2];
                }
                return $v;
            }
            else
                return $var;
        }
    }

}