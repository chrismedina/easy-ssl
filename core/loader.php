<?php
/**
 * Created by PhpStorm.
 * User: chrismedina
 */

/**
 * Loads controllers
 */

class ESSL_Loader {
    private $controller;
    private $action;
    private $urlvalues;

    public function __construct($urlvalues) {

        $this->urlvalues = $urlvalues;
        if(!isset($this->urlvalues['controller'])) $this->urlvalues['controller'] = "ESSL_Controller";
        if ($this->urlvalues['controller'] == "") {
            $this->controller = "ESSL_Controller";
        } else {
            $this->controller = $this->urlvalues['controller'];
        }

        if( !isset($this->urlvalues['action']) )
            $this->urlvalues['action'] = 'index';

        if($this->urlvalues['action'] == "") {
            $this->action = "index";
        } else {
            $this->action = $this->urlvalues['action'];
        }

    }

    public function CreateController() {
        if(class_exists($this->controller)) {
            $parents = class_parents($this->controller);
            if(in_array("ESSL_BaseController", $parents)) {
                //does the class contain the requested method?
                if(method_exists($this->controller, $this->action)) {
                    return new $this->controller($this->action,$this->urlvalues);
                } else {
                    //bad method error
                    throw new Exception("Bad Method call. Method does not exist.");
                    return new Error("badUrl", $this->urlvalues);
                }
            } else {
                //bad controller error
                throw new Exception("Bad Controller.");
                return new Error("badUrl", $this->urlvalues);
            }

        } else {
            //bad controller(class doesn't exist) error
            throw new Exception("Bad Controller. Class:" . $this->controller . ", doesn't exist.");
            return new Error("badUrl", $this->urlvalues);
        }
    }
}