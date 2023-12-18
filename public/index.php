<?php

define('BASE_DIR', dirname(__DIR__));
require_once BASE_DIR .'/vendor/autoload.php';
require_once BASE_DIR . '/Config/constants.php';

try {
    if (!preg_match('/assets/i', $_SERVER['REQUEST_URI'])) {
        \Core\Router::dispatch($_SERVER['REQUEST_URI']);
    }
}
catch (PDOException $PDOException) {
    var_dump("PDOException", $PDOException);
} catch (Exception $exception) {
    var_dump("BaseException", $exception);
}


