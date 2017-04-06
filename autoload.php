<?php

function PayPal_PHP_SDK_autoload($className) {
    $filePath = dirname(__FILE__).'/'.$className.".php";//dirname(__FILE__) . '/' . implode('/', $classPath) . '.php';
    if (file_exists($filePath)) {
        require_once($filePath);
    }
}

spl_autoload_register('PayPal_PHP_SDK_autoload');
