<?php
	/**
	 * @author Robert Hartung
	 * @copyright KIT Framework, rhScripts
	 * @package kit
	 * @subpackage core
	 * @version 1.0 
	 */
	 
	namespace kit;
	
	use kit\helper\singleton;
	
	class kit extends singleton
	{
		protected static $instance;
		
		private $url;
		
		private $parts;
		
		private $depth = null;
		
		private $controller = null;
		
		protected function __construct()
		{
			
		}
		
		protected function __clone() { 
			
		}
		
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
			
			$url = parse_url(substr($this->url, strlen(KIT_HTTP_PATH))); 
			
			$this->url = $url['path'];
			
			$this->parts = explode('/', $this->url);
			
			return $this;
		}
		
		public function registerUrl($url, $controller)
		{
			$parts = explode('/', substr($url,1));
			
			foreach($parts AS $depth => $part)
			{
				if($part !== $this->parts[$depth])
				{
					break;	
				}
			}
			
			if($this->depth === null || $depth > $this->depth)
			{
				$this->depth = $depth;
				$this->controller = $controller;
			}
		}
		
		public function initController()
		{
			$class = 'kit\\'.$this->controller.'Controller';
			
			$this->controller = call_user_func(Array($class, 'getInstance'));
		}
		
		public function run()
		{
			$this->controller->run();
			$this->view->run();
		}
		
		public function setView($view)
		{
			$this->view = $view;
		}
	}
?>