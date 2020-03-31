<?php
/**
 * Created by PhpStorm.
 * User: production
 * Date: 1/30/15
 * Time: 1:42 AM
 */

class ESSL_Settings_Model{

    // All options should be arrays [] ,  if you do not specify [] it breaks on Windows / Plesk
    protected  $options = array(
        'essl_network' => [],
        'essl' => [],
        'transient' => [ 'essl_easyssl' => '']
    );

    protected $time_length  = 300;

    protected $option_keys = [
        'file_copied_to' => '',
        'wp_ssl' => false , 'htaccess_ssl' => false, 'webconfig_ssl' => false, '301' => false, 'hsts' => false  ];


    function get_essl_options()
    {
        $this->options['essl'] = get_option( 'essl' );
        //$this->options['essl_network']  = get_option( 'essl_network' );

        if(empty($this->options['essl'])) $this->options['essl'] = $this->option_keys;
        //if( count($this->options['essl_network']) <= 0 ) $this->options['essl'] = $this->option_keys;

        return $this->options;
    }

    function set_essl_options()
    {
        //example default options write
        if(isset($_POST)){
            if(count($_POST)){
                foreach($_POST as $key => $value){
                    if(array_key_exists( $key, $this->option_keys)){
                        $this->options['essl'][$key] = $value;
                    }
                }
            }
        }

        update_option( 'essl', $this->options['essl'] );
        update_option( 'essl_network', $this->options['essl_network'] );
    }

    function update_essl_options( $option_name, $option_value)
    {
        update_option( $option_name, $option_value );
    }

    function removeOptions() {
        foreach( $this->options as $key => $value ) {
            delete_option( $key );
        }
    }

    //Get Easy SSL Settings  options / settings / transients
    function getAllSettings() {
        $this->get_essl_options();
        //$this->getFetchTransient();

        return $this->options;
    }

    function resetFreeSettings()
    {
        if(isset($this->options['essl']['wp_ssl'])) unset($this->options['essl']['wp_ssl']);
        if(isset($this->options['essl']['hsts'])) unset($this->options['essl']['hsts']);
    }

    function getTransientData( $transient_name )
    {
       return get_transient( $transient_name );
    }

    function SetTransientData( $transient_name , $data, $length )
    {
        set_transient( $transient_name, $data, $length );
    }
}