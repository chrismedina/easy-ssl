<?php
/**
 * Created by PhpStorm.
 * User: Godspleb
 * Date: 3/20/2020
 * Time: 6:36 PM
 */

class ESSL_Enforce
{
    private static $essl_instance = null;
    protected $essl_options = null;

    function __construct(){

        $essl_model = new ESSL_Settings_Model();
        $this->essl_options = $essl_model->get_essl_options();

        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || $_SERVER['SERVER_PORT'] == 443)
            $_SERVER['HTTPS'] = 'on';

        if(isset( $this->essl_options['essl']['wp_ssl'] ) || isset( $this->essl_options['essl']['htaccess_ssl'] ) || isset( $this->essl_options['essl']['webconfig_ssl']) )
        {
            if(! is_ssl() ) {
                if(isset($this->essl_options['essl']['wp_ssl'])){
                    if ($this->essl_options['essl']['wp_ssl'] == "on") {
                        //echo "<!-- WP Easy SSL on -->";
                        add_action('template_redirect', array($this, 'wp_ssl'));
                    }
                }
            }
        }

        if(isset($this->essl_options['essl']['hsts'])){
            if ($this->essl_options['essl']['hsts'] == 'on') {
                //echo "<!-- Easy SSL  HSTS on -->";
                add_action('send_headers', array($this, 'hsts'));
            }
        }
    }

    public static function getInstance()
    {
        if(!isset(self::$essl_instance)){
            self::$essl_instance = new self;
        }

        return self::$essl_instance;
    }

    public function wp_ssl()
    {
        if (!is_ssl()) {
            wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], 301);
            exit();
        }
    }

    function hsts()
    {
        //https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Strict-Transport-Security
        header('Strict-Transport-Security: max-age=63072000; includeSubDomains; preload');
    }

    private function __clone() {}
    public function __sleep() {}
    public function __wakeup() {}
}