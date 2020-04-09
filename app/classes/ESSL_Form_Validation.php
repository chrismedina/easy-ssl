<?php
/**
 * Created by PhpStorm.
 * User: production
 * Date: 1/23/15
 * Time: 6:48 AM
 */
class ESSL_Form_Validation
{
    private $_errors = array();
    private $_passed = false;
    private $_values_that_passed = array();

    //return only expected fields (sanity check)
    public static function expectedFields( $expected = array(), $source = array() , $removeSubmit = false, $trim_whitespace = true ) {
        if ($removeSubmit) unset( $source['submit'] ); //kill submit value

        $build_array = array();

        foreach ( $expected as $key => $value ) {
            if ( !empty($source[$value]) ) {
                $build_array[$value] = $trim_whitespace === false ? $source[$value] : trim( $source[$value] );
            }
        }

        return $build_array;
    }

    public static function exists($type = 'post') {
        switch($type) {
            case 'post':
                return (!empty($_POST)) ? true : false;
                break;
            case 'get':
                return (!empty($_GET)) ? true: false;
                break;
            default:
                return false;
                break;
        }
    }

    public function checkType( $value, $type )
    {
        if($type === 'scalar') return is_scalar($value);
        if($type === 'bool') return is_bool($value);
        if($type === 'string') return is_string($value);
        if($type === 'number' || $type === 'numeric' ) return is_numeric($value);
        if($type === 'float') return is_float($value);
        if($type === 'array') return is_array($value);
        return false;
    }

    /**
     * check for values , minimum length, maximum length and data types
     * works with $_POST and regular array structures
     *
     * Is TYPE specific if type is specified
     *
     * @param        $source
     * @param array  $items
     * @param string $request_type
     *
     * @return $this
     */
    public function check( $source, $items = array(), $request_type = "POST" ) {

        //Check if valid request type (POST , GET, etc)
        if(!empty($request_type)) {
            $this->validRequest($request_type);
        }

        foreach($items as $item => $rules) {
            foreach($rules as $rule => $rule_value) {

                $value = ( isset($source[$item]) && is_string( $source[$item] )) ? $source[$item] : '';

                if( $rule === 'required' && $rule_value === true && empty($value) ) {
                    $this->addError( "{$item} is required." );
                } else if ( !empty($value) ) {

                    switch( $rule ) {
                        case 'min':
                            if( strlen($value) < $rule_value ) {
                                $this->addError("{$item} must be a minimum of {$rule_value} characters.");
                            }
                            break;
                        case 'max':
                            if( strlen($value) > $rule_value ) {
                                $this->addError("{$item} must be a maximum of {$rule_value} characters.");
                            }
                            break;
                        case 'matches':
                            if( $value != $source[$rule_value] ) {
                                $this->addError("{$rule_value} must match {$item}.");
                            }
                            break;
                        case 'suffix':
                            $fileinfo = pathinfo( $value,PATHINFO_EXTENSION );
                            if( strpos( $rule_value, $fileinfo) === FALSE ){
                                $this->addError( 'Invalid file extension ' . $fileinfo . '. Supported: ' . $rule_value );
                            }
                            break;
                        case 'type':
                            if ( ! $this->checkType( $item, $rule_value ) ){
                                $this->addError("Data Type {$rule_value} must match {$item}.");
                            }
                            break;
                    }

                }

            }
        }

        if(empty($this->_errors)) {
            $this->_passed = true;
        }

        return $this;
    }

    public function addError($error) {
        $this->_errors[] = $error;
    }

    public function passed() {
        return $this->_passed;
    }

    public function errors() {
        return $this->_errors;
    }

    public function valuesThatPassed() {
       return ( !empty ($this->_values_that_passed) ) ? $this->_values_that_passed :  false;
    }

    public function isErrors() {
        if( !empty( $this->_errors ) ) {
            return true;
        } else {
            return false;
        }
    }

    public function email($email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            $this->addError( 'Email is not valid.' );
            return false;
        }
    }

    public function validRequest( $request_type = 'POST' ) {
        if( $_SERVER['REQUEST_METHOD']==strtoupper( $request_type ) ) {
            return true;
        } else {
            $this->addError( 'Invalid request type.' );
            return false;
        }
    }

    public function validElement( $source, $allowed = array() ) {
        foreach( $allowed as $key => $val ) {
            if( $key==$source ) {
                return true;
                break;
            }
        }
        return false;
    }

}