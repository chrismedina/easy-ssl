<?php
/**
 * Created by PhpStorm.
 * User: Godspleb
 * Date: 3/24/2020
 * Time: 12:23 AM
 */

class ESSL_Config {

    protected $copy_error_output = false;
    protected $write_error_output = false;
    protected $modify_error_output = false;
    protected $rollback_error = false;

    protected $config_file = 'blank';

    protected $copied_file = '';
    protected $copied_files = [] ;

    function backupConfigFile( $source_dir, $destination_dir)
    {
        if( !is_admin() || !current_user_can( 'update_plugins' )){
            $this->copy_error_output = 'Sorry you do not have access to this feature. Please login with appropriate permission';
            return false;
        }

        $source = $source_dir . $this->config_file;
        $copy_to = $destination_dir . DS . $this->config_file . '-' . date("m.d.y-H.m.s");
        $this->copy_error_output = $this->errorUponCopying( $source, $destination_dir , $copy_to);
        if( ! $this->copy_error_output ) return true;
    }

    public function errorUponCopying( $source, $copy_to_dir, $copy_to){
        if( !is_admin() || !current_user_can( 'update_plugins' )){
            return 'Sorry you do not have access to this feature. Please login with appropriate permission';
        }

        if( file_exists($copy_to_dir) && file_exists( $source ) ){
            if( ! copy( $source, $copy_to ) ) {
                $error_output = __('Error occurred when copying file. Please make sure permissions are set properly.');
                return $error_output;
            }
            $this->copied_file = $copy_to;
            array_push( $this->copied_files, $copy_to);
        }else{
            $error_output = "Either backups directory doesn't exist. It should be :" . $copy_to_dir .
                ' Or the source file was not found. The source config should be in :' . $source;
            return $error_output;
        }
        return false;
    }

    public function rollbackCopying( $original_source_path, $copied_file = '', $rollback_directory, $specify_config_filename = false )
    {
        if( !is_admin() || !current_user_can( 'update_plugins' )){
            $this->rollback_error = 'Sorry you do not have access to this feature. Please login with appropriate permission';
            return false;
        }

        if( file_exists($copied_file) && file_exists($rollback_directory) ){
            if($specify_config_filename) return rename( $copied_file, $original_source_path . $specify_config_filename );
            return rename( $copied_file, $original_source_path . $this->config_file );
        }else{
            $this->rollback_error = __("There was a problem rolling back copied file to the original. Either the copied file doesn't exist or a path doesn't exist", 'easy-ssl');
        }
        return false;
    }

    public function customErrorHandler($errno, $errstr, $errfile, $errline)
    {
        $this->write_error_output .= $errstr;
    }


    public function writeFreshConfig($filename, $content, $overwrite = false)
    {
        if( !is_admin() || !current_user_can( 'update_plugins' )){
            $this->write_error_output = 'Sorry you do not have access to this feature. Please login with appropriate permission';
            return false;
        }

        try{
            $silly_error = 'silly error';
            set_error_handler( array( $this, 'customErrorHandler' ) );
            $fhandle = fopen( $filename, ($overwrite) ? 'x' : 'w' );
            restore_error_handler();
        }catch(\Exception $e){
            $this->write_error_output = $e->getMessage();
            return false;
        }

        if($fhandle){
            fwrite($fhandle, $content);
        }else{
            $this->write_error_output .= __('File already exists or could not be open.');
            return false;
        }
        fclose($fhandle);

        return true;
    }

    public function getRollbackError()
    {
        return $this->rollback_error;
    }

    public function getConfigFilename()
    {
        return $this->config_file;
    }

    public function getCopiedFile()
    {
        return $this->copied_file;
    }

    public function exampleConfig()
    {

    }

    function setCopyErrorOutput($error)
    {
        $this->copy_error_output .= $error;
    }
}