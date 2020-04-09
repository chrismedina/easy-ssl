<?php
/**
 * Created by PhpStorm.
 * User: production
 * Date: 4/1/20
 * Time: 1:42 AM
 */

class ESSL_Settings_Model{

    // Option that should be arrays need [] otherwise it breaks on Windows / Plesk
    protected  $options = array(
        'essl' => [],
        'essl_file'
    );

    //Settings Keys
    protected $essl_option_values = [ 'wp_ssl' => false , 'htaccess_ssl' => false, 'webconfig_ssl' => false, 'hsts' => false  ];

    //File Backup path and file
    protected $essl_file_value = [ 'essl_file' => '' ];

    // similar to C# annotations / doctrine , just validate within the Model
    // type supported: 'bool', 'string', 'number', 'float', 'array'
    protected $essl_validation_schema = array(
        'wp_ssl' => array(
            'required' => false,
            'min' => 2,
            'max' => 3,
            'type' => 'scalar'
        ),

        'hsts' => array(
            'required' => false,
            'min' => 2,
            'max' => 3,
            'type' => 'scalar'
        ),

        'htaccess_ssl' => array(
            'required' => false,
            'min' => 0,
            'max' => 1,
            'type' => 'bool'
        ),

        'webconfig_ssl' => array(
            'required' => false,
            'min' => 0,
            'max' => 1,
            'type' => 'bool'
        )
    );

    // TODO Add Suffix support into validation
    //'suffix' => '.htaccess, web.config',
    protected $file_validation_schema = array(
        'essl_file'     => array(
            'required' => false,
            'min' => 10,
            'max' => 1000,
            'type' => 'string',
        )
    );

    public function __construct()
    {
        $this->validator = new ESSL_Form_Validation();
    }

    // User with read/write plugin permissions can get this
    function set_file_backup_option( $post_result = array())
    {
        if( current_user_can( 'update_plugins' ))
        {
            // Validate
            $post_clean = $this->returnExpectedFields( $this->file_validation_schema, $post_result, true );
            $this->validator->check( $post_clean, $this->essl_validation_schema );

            if( ! $this->validator->passed() ) return false;

            $this->essl_file_value['essl_file'] = esc_html( $post_clean['essl_file'] );

            return update_option( 'essl_file', $post_clean['essl_file'] );
        }
            return false;
    }

    function delete_file_backup_option(){
        if( current_user_can( 'update_plugins' ))
        {
            return delete_option('essl_file');
        }
    }

    // User with read/write plugin permissions can get this
    function set_essl_options( $post_result = array())
    {
        if( current_user_can( 'update_plugins' ) && !empty( $post_result ))
        {
            // Only take from $post_result keys we expect
            $post_clean = $this->returnExpectedFields( $this->essl_validation_schema, $post_result, true );

            $this->validator->check( $post_clean, $this->essl_validation_schema );

            if( ! $this->validator->passed() ) return false;

            foreach ( $post_clean as $key => $value ) {
                $this->options['essl'][$key] = (is_string($value) ) ? esc_html($value) : $value;
            }

            return update_option( 'essl', $this->options['essl'] );
        }

        return false;
    }

    // User with read/write plugin permissions can get this
    function get_file_backup_option()
    {
        if( current_user_can( 'update_plugins' )) :
            $this->essl_file_value['essl_file'] = get_option( 'essl_file' );

            $post_clean = $this->returnExpectedFields( $this->file_validation_schema, $this->essl_file_value, false );
            $this->validator->check( $post_clean, $this->file_validation_schema, false );

            if( ! $this->validator->passed() ) return false;

            $this->essl_file_value['essl_file'] = $post_clean['essl_file'];
        else :
            return false;
        endif;

        return $this->essl_file_value['essl_file'];
    }

    // General ESSL Settings BOOLEAN Options -  *.* Available to everyone
    function get_essl_options()
    {
        $this->options['essl'] = get_option( 'essl' );

        $post_clean = $this->returnExpectedFields( $this->essl_validation_schema, $this->options['essl'], false );
        $this->validator->check( $post_clean, $this->essl_validation_schema, false );

        if( ! $this->validator->passed() ) return false;
        $this->options['essl'] = $post_clean;

        if( empty( $this->options['essl'] )) $this->options['essl'] = $this->essl_option_values;

        return $this->options['essl'];
    }


    // Delete Options - user with read/write plugin permissions can
    function deleteOptions()
    {
        if( current_user_can( 'update_plugins' )) :
            foreach( $this->options as $key => $value ) {
                delete_option( $key );
            }
        else :
            return false;
        endif;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        if( isset( $this->validator )){
            if( method_exists( $this->validator, 'errors' )) return $this->validator->errors();
        }
        return false;
    }

    /*
     * Compare $source to $expected and only return keys that match
     * Will only trim() string types
     *
     * Similar to array_intersect_key()
     */
    public static function returnExpectedFields( $expected = array(), $source = array(), $trim_whitespace = false, $scalar_only = true  )
    {
        $build_array = array();

        foreach ( $expected as $key => $value ) {
            // isset vs. !empty ,  !empty returns true on a false boolean
            if ( isset( $source[$key] )) {
                if( $scalar_only === false  ) :
                    $build_array[$key] = ( $trim_whitespace === true && is_string($source[$key] )) ? trim( $source[$key] ) : $source[$key];
                elseif( $scalar_only === true && is_scalar( $source[$key] )):
                    $build_array[$key] = ( $trim_whitespace === true && is_string($source[$key] )) ? trim( $source[$key] ) : $source[$key];
                endif;
            }
        }

        return $build_array;
    }
}