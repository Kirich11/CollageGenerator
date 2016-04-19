<?php
if (!defined('APP_PATH')) {
    define('APP_PATH', __DIR__.('/../../current'));
}

return new \Phalcon\Config(
    array(

    'database' => array(
        'adapter'    => 'Mysql',
        'host'       => 'localhost',
        'username'   => 'root',
        'password'   => '123',
        'dbname'     => 'contest',
        'charset'    => 'utf8'
    ),

    'application' => array(
        'modelsDir'      => APP_PATH . '/models/',
        'viewsDir'       => APP_PATH . '/views/',
        'uploadDir'       => APP_PATH . '/public/files/',
        'logDir'       => APP_PATH . '/log/',
        'baseUri'        => '',
        'galleryLimit'   => 30
    )
    )
);
