<?php

use Phalcon\Loader,
    Phalcon\Di\FactoryDefault\Cli as CliDI,
    Phalcon\Cli\Console as ConsoleApp;

define('VERSION', '1.0.0');

// Используем стандартный для CLI контейнер зависимостей
$di = new CliDI();

// Определяем путь к каталогу приложений
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__)));

/**
 * Регистрируем автозагрузчик, и скажем ему, чтобы зарегистрировал каталог задач
 */
$loader = new \Phalcon\Loader();
$loader->registerDirs(
    array(
        APPLICATION_PATH . '/tasks'
    )
);
$loader->register();

// Загружаем файл конфигурации, если он есть
if (is_readable(APPLICATION_PATH . '/config/config.php')) {
    $config = include APPLICATION_PATH . '/config/config.php';
    $di->set('config', $config);
}

// Создаем консольное приложение
$console = new ConsoleApp();
$console->setDI($di);



/**
 * Определяем консольные аргументы
 */
$arguments = array();
foreach ($argv as $k => $arg) {
    if ($k == 1) {
        $arguments['task'] = $arg;
    } elseif ($k == 2) {
        $arguments['action'] = $arg;
    } elseif ($k >= 3) {
        $arguments['params'][] = $arg;
    }
}

// определяем глобальные константы для текущей задачи и действия
define('CURRENT_TASK',   (isset($argv[1]) ? $argv[1] : null));
define('CURRENT_ACTION', (isset($argv[2]) ? $argv[2] : null));

try {
    // обрабатываем входящие аргументы
    $console->handle($arguments);
} catch (\Phalcon\Exception $e) {
    echo $e->getMessage();
    exit(255);
}