<?php
defined('ESSL') or die('Restricted access');
/**
 * Classes Autoloader.
 * It loads automatically the right class on class instantation.
 * We use 'ESSL_' as a prefix for our classes.
 * @param  Class $class Class name
 * @return
 */
function essl_autoload($class)
{
    // Check if the class name has our prefix.
    if (strpos($class, 'ESSL_') !== false) {
        // Class file path.
        $class_path = ESSL_CLASSES . $class . '.php';

        // Controllers file path.
        $controller_path = ESSL_CONTROLLERS . $class . '.php';

        //Models file path.
        $model_path = ESSL_MODELS . $class . '.php';

        $interface_path = ESSL_INTERFACES . $class . '.php';

        // If the class file exists, let's load it.
        if (file_exists($class_path)) {
            require_once $class_path;
        } elseif(file_exists($controller_path)) { // Check if controller
            require_once $controller_path;
        } elseif(file_exists($model_path)) { // Model Path
            require_once $model_path;
        } elseif(file_exists($interface_path)) { // Interfaces Path
            require_once $interface_path;
        }

    }
}


spl_autoload_register('essl_autoload');
