<?php
	$files = get_included_files();
	
	define('PATH_SITE_ROOT', dirname($files[0]).DIRECTORY_SEPARATOR);
	define('PATH_KIT_ROOT', dirname(dirname($files[count($files)-1])).DIRECTORY_SEPARATOR);
	
	if(!defined('PATH_KIT_LIB_CLASSES'))
	{
		define('PATH_KIT_LIB_CLASSES', PATH_KIT_ROOT.'lib'.DIRECTORY_SEPARATOR/*.'class'.DIRECTORY_SEPARATOR*/);
	}
	
	unset($files);
	
	set_include_path(get_include_path().PATH_SEPARATOR.
		PATH_SITE_ROOT.'lib'.DIRECTORY_SEPARATOR.PATH_SEPARATOR.
		PATH_KIT_ROOT.'lib'.DIRECTORY_SEPARATOR);
	
	function __kit_autoload($class)
	{
		// This function will only load classes from kit namespace
		if(strpos($class,'kit\\') !== 0)
		{
			return false;
		}
		
		// replace the namespace separator with directory separator and remove namespace from the beginning
		$class = str_replace('\\', DIRECTORY_SEPARATOR, substr($class,4));
		
		if(($pos = strpos($class, 'Controller')) !== false)
		{
			$class = substr($class,0,$pos);
			$suffix = 'controller';
		}
		elseif(($pos = strpos($class, 'Interface')) !== false)
		{
			$class = substr($class,0,$pos);
			$suffix = 'interface';
			$dir = 'interface';
		}
		elseif(($pos = strpos($class, 'View')) !== false)
		{
			$class = substr($class,0,$pos);
			$suffix = 'view';
		}
		elseif(($pos = strpos($class, 'Command')) !== false)
		{
			$class = substr($class,0,$pos);
			$suffix = 'command';
		}
		elseif(($pos = strpos($class, 'Trait')) !== false)
		{
			$class = substr($class,0,$pos);
			$suffix = 'trait';
		}
		elseif(($pos = strpos($class, 'Exception')) !== false)
		{
			$class = substr($class,0,$pos);
			$suffix = 'exception';
		}
		else
		{
			$suffix = 'class';
		}
		
		$url = $suffix.DIRECTORY_SEPARATOR.strtolower($class).'.'.$suffix.'.php';
		
		// var_dump($url);
		
		require_once($url);
	}
	
	spl_autoload_register('__kit_autoload');
	
	$kit = kit\kit::getInstance()->setup();
?>