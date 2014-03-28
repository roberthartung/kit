<?php
	session_start();
	session_name('kit.session');
	
	$files = get_included_files();
	
	if(!defined('PATH_SITE_ROOT')) {
		define('PATH_SITE_ROOT', dirname($files[0]).DIRECTORY_SEPARATOR);
	}
	define('PATH_KIT_ROOT', dirname(dirname($files[count($files)-1])).DIRECTORY_SEPARATOR);
	
	if(!defined('PATH_KIT_LIB_CLASSES'))
	{
		define('PATH_KIT_LIB_CLASSES', PATH_KIT_ROOT.'lib'.DIRECTORY_SEPARATOR/*.'class'.DIRECTORY_SEPARATOR*/);
	}
	
	$path_www = substr(str_replace(DIRECTORY_SEPARATOR, '/', PATH_SITE_ROOT), strlen($_SERVER['DOCUMENT_ROOT']));
	define('PATH_WWW', '/'.$path_www);
	
	require_once(PATH_KIT_LIB_CLASSES.'exception'.DIRECTORY_SEPARATOR.'filenotfound.exception.php');
	require_once(PATH_KIT_ROOT.'lib'.DIRECTORY_SEPARATOR.'func'.DIRECTORY_SEPARATOR.'helpers.func.php');
	
	unset($files);
	
	set_include_path(get_include_path().PATH_SEPARATOR.
		PATH_SITE_ROOT.'lib'.DIRECTORY_SEPARATOR.PATH_SEPARATOR.
		PATH_KIT_ROOT.'lib'.DIRECTORY_SEPARATOR);
	
	function __kit_autoload($class)
	{
		// This function will only load classes from kit namespace
		if(strpos($class,'kit\\') !== 0)
		{
			$class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
			
			$path = PATH_SITE_ROOT.'lib'.DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.strtolower($class).'.class.php';
			
			if(file_exists($path))
			{
				require_once($path);
				return true;
			}
			else
			{
				throw new Exception('File not found: '.$path);
			}
			
			return false;
		}
		
		$class = str_replace('\\', DIRECTORY_SEPARATOR, substr($class,4));
		
		// replace the namespace separator with directory separator and remove namespace from the beginning
		
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
		elseif(($pos = strpos($class, 'Model')) !== false)
		{
			$class = substr($class,0,$pos);
			$suffix = 'model';
		}
		else
		{
			$suffix = 'class';
		}
		
		$url = $suffix.DIRECTORY_SEPARATOR.strtolower($class).'.'.$suffix.'.php';
		
		if(!file_exists(PATH_SITE_ROOT.'lib'.DIRECTORY_SEPARATOR.$url) && !file_exists(PATH_KIT_ROOT.'lib'.DIRECTORY_SEPARATOR.$url))
		{
			throw new kit\fileNotFoundException($url);
		}
		
		require_once($url);
	}
	
	spl_autoload_register('__kit_autoload');
	
	// As we're using constants here, we need to include the site's default first
	if(file_exists(PATH_SITE_ROOT.'inc/defaults.inc.php'))
	{
		require_once(PATH_SITE_ROOT.'inc/defaults.inc.php');
	}
	
	require_once(PATH_KIT_ROOT.'inc/defaults.inc.php');
	
	// The config uses an object, so we want to object to be created first and then be overwritten by the user's project after
	require_once(PATH_KIT_ROOT.'cfg/default.cfg.php');
	
	if(file_exists(PATH_SITE_ROOT.'cfg/kit.cfg.php'))
	{
		require_once(PATH_SITE_ROOT.'cfg/kit.cfg.php');
	}
	
	if(!defined('KIT_NO_SETUP')) {
		$kit = kit\kit::getInstance()->setup();
	}
?>