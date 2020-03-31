<?php

class ESSL_Controller extends ESSL_BaseController {

    protected  $options = array(
        'essl_network' => [],
        'essl' => [],
        'transient' => [ 'essl_easyssl' => '']
    );

    protected $time_length  = 300;

    protected $option_keys = [ 'file_copied_to' => '', 'wp_ssl' => 'off' , 'htaccess_ssl' => 'off', 'webconfig_ssl' => 'off', '301' => 'off', 'hsts' => 'off'  ];

    protected $response = [];

    protected $post_actions = [ 'serverconfiguration', 'writeconfiguration', ''];

    //Default view
    function index() {
        //Protect against CSRF
        if(isset($_POST['action'])){
            if (!isset($_POST['essl_aiowz_tkn'])) die("<br><br>Hmm .. looks like you didn't send any credentials.. No CSRF for you! ");
            if (!wp_verify_nonce($_POST['essl_aiowz_tkn'],'essl-csrf-index-nonce')) die("<br><br>Hmm .. looks like you didn't send any credentials.. No CSRF for you! ");

        }

        $model = new ESSL_Settings_Model;
        //detect if $_POST request
        if(isset($_POST)){
            if(count($_POST)){
                $model->getAllSettings();
                //reset wp_ssl and hsts
                $model->resetFreeSettings();
                $model->set_essl_options();
            }
        }

        $this->ReturnView( array( 'settings' => $model->getAllSettings() ), true);
    }

    //REVERT
    function revertconfiguration()
    {
        if (!isset($_POST['essl_aiowz_rc_tkn'])) die("<br><br>Hmm .. looks like you didn't send any credentials.. No CSRF for you! ");
        if (!wp_verify_nonce($_POST['essl_aiowz_rc_tkn'],'essl-csrf-rc-nonce')) die("<br><br>Hmm .. looks like you didn't send any credentials.. No CSRF for you! ");

        $model = new ESSL_Settings_Model;
        $options = $model->get_essl_options();

        if(isset($options['essl']['file_copied_to'])){
            $copied_file = $options['essl']['file_copied_to'];

            $copy_file_dir_pos = strrpos($copied_file, DIRECTORY_SEPARATOR);
            if(!$copy_file_dir_pos){
                $this->response['serverconfig']['output'] = __("Could not revert due to DIRECTORY_SEPARATOR issue", 'easy-ssl');
                $this->serverconfiguration();
                return;
            }
                $copy_file_dir = substr($copied_file, 0, $copy_file_dir_pos);

            //is it .htaccess or web.config file
            if(strpos($copied_file, '.htaccess') >= 0) {
                $specify_config_filename = '.htaccess';
            }elseif(strpos($copied_file, 'web.config') >= 0){
                $specify_config_filename = 'web.config';
            }else{
                $this->serverconfiguration();
                return;
            }

            $config = new ESSL_Config;
            if( ! $config->rollbackCopying( ABSPATH, $copied_file, $copy_file_dir, $specify_config_filename ) )
            {
                $this->response['serverconfig']['output'] = $config->getRollbackError();
            }

            //rewrite settings , remove
                //remove file_copied_to, htaccess_ssl, and webconfig_ssl
            if(isset($options['essl']['htaccess_ssl'])) unset($options['essl']['htaccess_ssl']);
            if(isset($options['essl']['htaccess_ssl'])) unset($options['essl']['htaccess_ssl']);
            if(isset($options['essl']['file_copied_to'])) unset($options['essl']['file_copied_to']);
            if(isset($options['essl'])) $model->update_essl_options( 'essl', $options['essl'] );
        }

        $this->serverconfiguration();
    }

    function serverconfiguration( $know_config_already = false)
    {
        $config_check = new ESSL_ServerConfig;
        $htaccess_file = false;
        $webconfig_file = false;
        $apache = false;
        $windows = false;
        $example = '';
        $critical_error = false;
        $output = '';

        //Protect against CSRF
        if(isset($_POST['action'])){
            if( $_POST['action']== 'serverconfiguration') {
                if (!isset($_POST['essl_aiowz_sc_tkn'])) die("<br><br>Hmm .. looks like you didn't send any credentials.. No CSRF for you! ");
                if (!wp_verify_nonce($_POST['essl_aiowz_sc_tkn'],'essl-csrf-sc-nonce')) die("<br><br>Hmm .. looks like you didn't send any credentials.. No CSRF for you! ");
            }
        }


        if( ! $know_config_already ) {
            $output = '<table class="table table-bordered table-condensed">';
            if( $config_check->isWindows() ){
                $output .= '<tr> <td class="alert-success"><li>' . __('Windows OS detected', 'easy-ssl' ) . ' </li> </td></tr>';
                $windows = true;
                //windows but no webconfig
                if( !$config_check->webConfigExists()){
                    $critical_error = true;
                    $output .= '<tr> <td class="alert-warning"> <li>' . __('.webconfig file NOT found', 'easy-ssl' ) . ' </li> </td></tr>';
                    //send ExampleConfig so they can do it manually
                }else{
                    //webconfig found
                    $webconfig_file = true;
                    $output .= '<tr> <td class="alert-success"> <li>' . __('.webconfig file found in WP path', 'easy-ssl' ) . ' </li> </td></tr>';
                }
            }

            if( $config_check->isApache() ){
                //apache but no htaccess
                $output .= '<tr> <td class="alert-success"> <li>' . __('Apache detected', 'easy-ssl' ) . ' </li> </td></tr>';
                $apache = true;

                if( !$config_check->isModRewrite_module() ) {
                    $critical_error = true;
                    $output .= '<tr> <td class="alert-warning"> <li>' . __('Mod Rewrite NOT detected (.htaccess requires this)', 'easy-ssl' ) . ' </li> </td></tr>';
                }else{
                    $output .= '<tr><td class="alert-success">  <li>' . __('Mod Rewrite detected (.htaccess usable)', 'easy-ssl' ) . ' </li> </td></tr>';
                }

                if( !$config_check->htaccessExists() ){
                    $critical_error = true;
                    $output .= '<tr> <td class="alert-warning"> <li>' . __('.htaccess file NOT found', 'easy-ssl' ) . '</li> </td></tr>';
                    //send ExampleConfig so they can do it manually
                }else{
                    //htaccess found
                    $htaccess_file = true;
                    $output .= '<tr> <td class="alert-success"> <li>' . __('.htaccess file found in WP path', 'easy-ssl' ) . '</li> </td></tr>';
                }
            }
            $output .= '</table>';
        }


        $model = new ESSL_Settings_Model;
        $this->response['settings'] = $model->getAllSettings();

        $apache_complete = false;
        if($apache && $htaccess_file) $apache_complete = true;

        $windows_complete = false;
        if($windows && $webconfig_file) $windows_complete = true;

        $this->response['serverconfig']['output'] = $output;
        $this->response['serverconfig']['example'] = $example;
        $this->response['serverconfig']['critical'] = $critical_error;
        $this->response['serverconfig']['htaccess'] = $htaccess_file;
        $this->response['serverconfig']['webconfig'] = $webconfig_file;
        $this->response['serverconfig']['apache'] = $apache;
        $this->response['serverconfig']['windows'] = $windows;
        if($apache_complete) $this->response['serverconfig']['apache_complete'] = $apache_complete;
        if($windows_complete) $this->response['serverconfig']['windows_complete'] = $windows_complete;

        $this->ReturnView($this->response, true);
    }

    function writeconfiguration()
    {
        if (!isset($_POST['essl_aiowz_wc_tkn'])) die("<br><br>Hmm .. looks like you didn't send any credentials.. No CSRF for you! ");
        if (!wp_verify_nonce($_POST['essl_aiowz_wc_tkn'],'essl-csrf-wc-nonce')) die("<br><br>Hmm .. looks like you didn't send any credentials.. No CSRF for you! ");

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
                        //echo 'APPENDING - MOD REWRITE CHECK before attemtping a write:' . $config_check->isModRewrite();
                        //Give it one last try by adding environment variable to
                        if ($config_object->writeAppendEnvVariable(ABSPATH . '.htaccess', 'HTTP_MOD_REWRITE', 'on')) {
                            if (!$config_check->isModRewrite()) {
                                $config_object->setCopyErrorOutput(__('Appended to .htaccess but still no environment variable loaded. HTACCESS not working. ', 'easy-ssl'));
                            }
                        }
                    }
                }

                $error_output = $config_object->getErrorOutput();
                if (!$error_output) {
                    //write fresh file
                    if ($config_object->writeFreshConfig(ABSPATH . $config_object->getConfigFilename(), $config_object->exampleConfig())) {
                        //wrote successfully

                        //if writing new config, test accessing home URL in a new request (test twice)
                        $home_url = get_home_url();

                        /*$context = stream_context_create(array(
                            'http' => array(
                                'ignore_errors' => true
                            )
                        ));*/

                        $the_content = @file_get_contents($home_url);

                        if (strpos($http_response_header[0], '200') && strpos($home_url, 'https') == 0) {
                            $successful = true;
                        } elseif (strpos($http_response_header[0], '301')) {
                            $headers = $this->get_headers_deux($home_url, 1);

                            //only returning 301 and not content
                            if (isset($headers['Location'])) {
                                $the_content = @file_get_contents($headers['Location']);
                                //sleep(1);
                                if (strpos($http_response_header[0], '200')) {
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
                            //echo "response was 301";
                        } else {  //Not a 301 redirect that we can test, and not 200
                            $headers = $this->get_headers_deux($home_url, 0);
                            //echo "2nd GET_HEADERS_DEUX:";
                            //var_dump( $headers);
                            //echo '2nd request no 301 or 200 found';
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
                    $_POST['htaccess_ssl'] = 'on';
                }else{
                    $_POST['webconfig_ssl'] = 'on';
                }

                $copied_file = $config_object->getCopiedFile();
                $_POST['file_copied_to'] = $copied_file;

                if(isset($_POST)){
                    if(count($_POST)){
                        $model->set_essl_options();
                    }
                }

                if ($htaccess) {
                    $this->response['serverconfig']['apache_complete'] = true;
                } else {
                    //windows
                    $this->response['serverconfig']['windows_complete'] = true;
                }
            }

            $this->response['serverconfig']['errors'] = $error_output;
            $this->serverconfiguration();
        }else{
            $this->response['serverconfig']['errors'] = __('Required functions: fsockopen and/or file_get_contents were not found. These are required to properly test HTTPS using web config files.', 'easy-ssl');
            $this->serverconfiguration();
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