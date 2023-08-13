<?php

namespace Main;

class Autoloader {

    static function register() {
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    static function autoload($class) {
        $c = explode('\\', $class);
        $filePath = $c[0] . '/' . $c[1] . '.lib.php';
        if (file_exists($filePath)) {
            require $filePath;
        } else {
            require '../' . $filePath;
        }
    }

}

?>