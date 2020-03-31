<?php

abstract class ESSL_BaseController {
    protected $urlvalues;
    protected $action;

    public function __construct($action, $urlvalues) {
        $this->action = $action;
        $this->urlvalues = $urlvalues;
    }

    public function ExecuteAction() {

        if($this->action !='') {
            return $this->{$this->action}();
        }
    }

    protected function ReturnView($viewmodel, $fullview) {
        $viewloc = 'views/' . get_class($this) . '/' . $this->action . '.php';

        if( $fullview ) {
            require_once( ESSL_DIR . 'views/' . get_class($this) . '/essl_settings.view.php' );
        } else {
            require_once( $viewloc );
        }
    }
}