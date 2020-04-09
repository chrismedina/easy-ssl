<?php
/**
 * Created by PhpStorm.
 * User: Godspleb
 * Date: 3/21/2020
 * Time: 9:43 AM
 */

class ESSL_htaccess extends ESSL_Config implements ESSL_IServerConfig
{
    protected $config_file = '.htaccess';

    protected $appended_written_lines = '';

    function getAppendedWriteLines()
    {
        return $this->appended_written_lines;
    }

    function getErrorOutput()
    {
        $all_errors = false;

        if( !empty( $this->write_error_output ))    $all_errors .= $this->write_error_output;
        if( !empty( $this->copy_error_output ))     $all_errors .= $this->copy_error_output;

        return $all_errors;
    }

    function getWriteError()
    {
        return $this->write_error_output;
    }

    public function modify( $filename )
    {
        $server_config = new ESSL_ServerConfig;

        $example_config = $this->exampleConfig();
    }

    function writeAppendEnvVariable( $file, $env_variable, $value )
    {
        if( !is_admin() || !current_user_can( 'update_plugins' )){
            $this->write_error_output = 'Sorry you do not have access to this feature. Please login with appropriate permission';
            return false;
        }

        if( !file_exists($file)) return false;

        //SetEnv HTTP_MOD_REWRITE on
        $string = "
            <IfModule mod_rewrite.c>
            # inform php that mod_rewrite is enabled
            SetEnv $env_variable $value
            </IfModule>";

        $fhandle = fopen($file, 'a');
        if($fhandle){
            fwrite($fhandle, $string);
            fclose($fhandle);

            $this->appended_written_lines = 4;
        }else{

        }

        return false;
    }

    function exampleConfig()
    {
        $example = '# BEGIN WordPress
        <IfModule mod_rewrite.c>
           # inform php that mod_rewrite is enabled
           SetEnv HTTP_MOD_REWRITE on
                RewriteEngine On
                RewriteCond %{SERVER_PORT} !^443$
                RewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]
                RewriteBase /
                RewriteRule ^index\.php$ - [L]
                RewriteCond %{REQUEST_FILENAME} !-f
                RewriteCond %{REQUEST_FILENAME} !-d
                RewriteRule . /index.php [L]
        </IfModule>
                # END WordPress';

        return $example;
    }
}