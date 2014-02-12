<?php
define('BASE_PATH', realpath(dirname(__FILE__)));

function autoloader($class_name) {
	$class_name = ltrim($class_name, '\\');
	$file_name = '';
	$namespace = '';
	if ($lastNsPos = strrpos($class_name, '\\')) {
		$namespace = substr($class_name, 0, $lastNsPos);
		$class_name = substr($class_name, $lastNsPos + 1);
		$file_name = str_replace('\\', DIRECTORY_SEPARATOR, $namespace).DIRECTORY_SEPARATOR;
	}
	$file_name .= str_replace('_', DIRECTORY_SEPARATOR, $class_name).'.php';
	var_dump(BASE_PATH.'/'.$file_name, $file_name); 
	//exit;
	if (is_readable(BASE_PATH.'/'.$file_name)) require BASE_PATH.'/'.$file_name;
}

spl_autoload_register('autoloader');