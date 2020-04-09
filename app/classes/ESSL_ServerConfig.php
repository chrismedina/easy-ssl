<?php
/**
 * Created by PhpStorm.
 * User: Godspleb
 * Date: 3/21/2020
 * Time: 12:19 AM
 */


//HELPER class for Configurations
class ESSL_ServerConfig
{
    protected $absolute_path = ABSPATH;

    public function getConfigObject($copy_to_directory)
    {
        //copy any pre-existing .htaccess or web.config
        if( $this->isWindows() && $this->webConfigExists() ){
            $webconfig = new ESSL_Webconfig;

            $webconfig->backupConfigFile( $this->absolute_path ,$copy_to_directory );
            if( ! $webconfig->getErrorOutput() ) return $webconfig;
        }

        if( $this->isApache() && $this->htaccessExists() ){
            $htaccess = new ESSL_htaccess;

            $htaccess->backupConfigFile( $this->absolute_path ,$copy_to_directory );
            if( ! $htaccess->getErrorOutput() ) return $htaccess;
        }

        return false;
    }

    public function isApache()
    {
        if( strpos( $_SERVER['SERVER_SOFTWARE'], 'Apache') !== false) return true;

        return false;
    }

    public function isWindows()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return true;
        }
        return false;
    }

    /**
     * ToDo: Make this operational
     */
    public function isNginx()
    {

    }

    public function isModRewrite_module()
    {
        if(function_exists('apache_get_modules')){
            return in_array('mod_rewrite', apache_get_modules());
        }else{
            return $this->isModRewrite();
        }
    }

    public function isModRewrite() {
        if(isset($_SERVER['HTTP_MOD_REWRITE'])){
            if ($_SERVER['HTTP_MOD_REWRITE'] == 'on') {
                return true;
            }
        }
        return false;
    }

    public function htaccessExists()
    {
        if(file_exists($this->absolute_path . '.htaccess')) return $this->absolute_path . '.htaccess';

        return false;
    }

    public function webConfigExists()
    {
        if(file_exists($this->absolute_path . 'web.config')) return true;

        return false;
    }
}