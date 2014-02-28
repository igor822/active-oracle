<?php
if (!defined('BASE_PATH')) define('BASE_PATH', realpath(dirname(__FILE__)));

putenv('ORACLE_HOME=/usr/lib/oracle/11.2/client64');
putenv('LD_LIBRARY_PATH=/usr/lib/oracle/11.2/client64/lib');
putenv('TNS_ADMIN=/usr/lib/oracle/11.2/client64/network/admin');

function autoloader($class_name) {
	if (strpos($class_name, 'PHPUnit') !== false) return false;
	$class_name = ltrim($class_name, '\\');
	$file_name = '';
	$namespace = '';
	if ($lastNsPos = strrpos($class_name, '\\')) {
		$namespace = substr($class_name, 0, $lastNsPos);
		$class_name = substr($class_name, $lastNsPos + 1);
		$file_name = str_replace('\\', DIRECTORY_SEPARATOR, $namespace).DIRECTORY_SEPARATOR;
	}
	$file_name .= str_replace('_', DIRECTORY_SEPARATOR, $class_name).'.php';
	if (is_readable(BASE_PATH.'/'.$file_name)) {
		require_once BASE_PATH.'/'.$file_name;
	} 
	
	if (!class_exists($namespace.'\\'.$class_name, false) && !interface_exists($namespace.'\\'.$class_name, false)) {
		trigger_error('Unable to load class: '.$class_name, E_USER_WARNING);
	} 
}

spl_autoload_register('autoloader');