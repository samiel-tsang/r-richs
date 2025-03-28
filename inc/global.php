<?php
define("BASEPATH", realpath(__DIR__."/../"));

/* ---- Import Configuration ---- */
$config = include(BASEPATH."/config/config.php");
function cfg($section = 'global') { return $GLOBALS['config'][$section] ?? ''; }	

/* ---- Include Library ---- */
spl_autoload_register(function ($class) { 
	if ($path = realpath(BASEPATH.'/'.cfg()['classPath'].str_replace('\\', '/', $class).'.php'))
		include_once($path); 
});

include_once(BASEPATH."/vendor/autoload.php");

/* ---- Exception Handler ---- */
set_exception_handler([Error\ExceptionHandler::getHandler(), 'handle']);

/* ---- Session ---- */
session_start();

/* ---- Language Handler ---- */
Pages\Language::build($_SESSION['lang'] ?? cfg()['lang'] ?? "en");

/* ---- Database Connection ---- */
if (cfg('database') != '') {
	Database\DatabaseFactory::createDatabaseByConfig(cfg('database'));
}
