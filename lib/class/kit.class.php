<?php
	/**
	 * @author Robert Hartung
	 * @copyright KIT Framework, rhScripts
	 * @package kit
	 * @subpackage core
	 * @version 1.0 
	 */
	 
	namespace kit;
	
	use Exception;
	use SimpleXMLElement;
	
	class kit
	{
		use singletonTrait;
		
		/**
		 * Keeps the current request URL
		 * 
		 * @var string
		 */
		
		private $url;
		
		/**
		 * Keeps all the parts for the current request URL
		 * 
		 * @var array
		 */
		
		private $parts;
		
		/**
		 * Keeps the depth of matching between the last registered URL and the parts of the request URL
		 * 
		 * @var null|int
		 */
		
		private $depth = null;
		
		/**
		 * controller name and/or object
		 * 
		 * @var mixed
		 */
		
		private $controller = null;
		
		private $view;
		
		private $db;
		
		private $cfg;
		
		private $path;
		
		private $parameters = Array();
		
		private $controller_url;
		
		public function setup()
		{
			$root_doc = $_SERVER['DOCUMENT_ROOT'];
			$root_doc = substr(PATH_SITE_ROOT, strlen($root_doc));
			$root_doc = str_replace(DIRECTORY_SEPARATOR, '/', $root_doc);
			//$root_doc = dirname($root_doc);
			if($root_doc === '')
			{
				$root_doc = '/';
			}
			
			define('KIT_HTTP_PATH', $root_doc);
			
			if(isset($_SERVER['REQUEST_URI']))
			{
				$this->url = $_SERVER['REQUEST_URI'];
			}
			elseif(isset($_SERVER['REDIRECT_URL']))
			{
				$this->url = $_SERVER['REDIRECT_URL'];
			}
			else
			{
				throw new parserException('parser:redirect_url');
			}
			
			$this->setRequestURL(substr($this->url, strlen(KIT_HTTP_PATH)));
			
			$this->cfg = cfg::getInstance();
			
			if(!empty($this->cfg->db->hostname) && !empty($this->cfg->db->username) && !empty($this->cfg->db->database))
			{
				$this->db = new db();
			}
			
			return $this;
		}
		
		public function getParts($url)
		{
			if(strpos($url, '/') === 0)
			{
				$url = substr($url,1);
			}
			
			if(substr($url,-1,1) === '/')
			{
				$url = substr($url,0,-1);
			}
			
			/*
			if($url === false)
			{
				$url = '';
			}
			*/
			if($url === false)
			{
				return Array();
			}
			
			return explode('/', $url); //  substr($url,1), -1
			
			/*
			if($url === '/')
			{
				$parts = Array('');
			}
			else
			{
				if($url[0] !== '/')
				{
					$url = '/'.$url;
				}
				
				if(substr($url,-1,1) != '/')
				{
					$url .= '/';
				}
				
				$parts = explode('/', $url);
			}*/
			
			return $parts;
		}
		
		public function getControllerURL()
		{
			return $this->controller_url;
		}
		
		public function setRequestURL($url)
		{
			$url = parse_url($url);
			$this->url = isset($url['path']) ? $url['path'] : '';
			$this->parts = $this->getParts($this->url);
		}
		
		public function registerUrl($url, $controller)
		{
			$parts = $this->getParts($url);
			
			$controller = str_replace('/', '\\', $controller);

			// var_dump($parts, $this->parts);
			// if the current URL is / this will result in a 0-element array
			if(!count($parts) && $this->depth === null)
			{
				//if(!count($this->parts))
				//{
					$this->depth = 0;
					$this->controller = $controller;
				//}
				
				return;
			}
			
			$parameters = Array();
			$depth = 0;
			$url_parts = Array();
			
			//var_dump(count($parts), count($this->parts));
			
			foreach($parts AS $part)
			{	
				// no more parts available
				$_part = &$this->parts[$depth];
				// URL is longer than actual URL
				if(!isset($_part))
				{
					//return false;
					break;
				}
				//var_dump($part, $_part);
				
				if($part[0] == '%')
				{
					if(sprintf($part, $_part) != $_part)
					{
						//return false;
						break;
					}
					
					$parameters[] = $_part;
				}
				elseif($part !== $_part)
				{
					//return false;
					break;
				}
				
				$url_parts[] = $_part;
				
				$depth++;
			}
			
			if($depth != count($parts))
			{
				return false;
			}
			
			if($this->depth === null || $depth > $this->depth)
			{
				$this->parameters = $parameters;
				$this->depth = $depth;
				$this->controller = $controller;
				$this->path = PATH_WWW.substr($url,1);
				$this->controller_url = implode('/', $url_parts);
			}
		}
		
		public function initController()
		{
			if($this->controller === null)
			{
				throw new Exception('No controller available. Please register a URL handler for URL "'.$this->url.'"');
			}
			
			//var_dump($this->controller);
			
			$_GET += $this->parameters;
			$class = 'kit\\'.$this->controller.'Controller';
			
			$this->controller = call_user_func(Array($class, 'getInstance'));
		}
		
		/**
		 * Run method. This will render all the page details
		 * 
		 * @return void
		 * 
		 * @throws Exception
		 */
		
		public function run()
		{
			if(!$this->controller instanceof controllerInterface)
			{
				throw new Exception('controller does not implement controllerInterface');
			}
			
			$json = $this->controller->run();
			
			if($this->view !== null)
			{
				$this->view->BASE = PATH_WWW;
				$this->view->PATH = $this->path;
				$this->view->LANG = lang::getLanguage();
				$this->view->URL = $this->getControllerURL();
				$this->view->run();
				//throw new Exception('no view set');
			}
			elseif(isset($json))
			{
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode($json);
			}
		}
		
		/**
		 * Sets the current view
		 * 
		 * @param viewInterface View
		 * 
		 * @return void
		 */
		
		public function setView(viewInterface $view)
		{
			$this->view = $view;
		}
		
		/**
		 * Sets the current view
		 * 
		 * @param viewInterface View
		 * 
		 * @return viewInterface view
		 */
		
		public function getView()
		{
			return $this->view;
		}
		
		/**
		 * Returns the database object
		 */
	 
	  public function getDatabase()
	  {
	  	return $this->db;
	  }
	  
	  public function getParameters()
	  {
	  	return $this->parameters;
	  }
	  
	  public function createPrefixedClassLoader($prefix, $path, array $include_dirs = Array(), $file_ext = '.php')
	  {
	  	spl_autoload_register(function($class) use($prefix, $path, $file_ext, $include_dirs)
	  	{
	  		if(strpos($class, $prefix) !== 0)
	  		{
	  			return false;
	  		}
	  		
	  		foreach($include_dirs AS $include_dir)
	  		{
	  			if(file_exists($include_dir.$class.$file_ext))
	  			{
	  				require_once($include_dir.$class.$file_ext);
	  				return true;
	  			}
	  		}
	  		
	  		require_once($path.$class.$file_ext);
	  	});
	  }
	  
	  public function addLanguageFile($file)
	  {
	  	$lang = lang::getInstance();
	  	$lang->addFile($file);
	  	return $lang;
	  }
	}
?>