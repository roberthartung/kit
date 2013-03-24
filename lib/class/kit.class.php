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
			
			$this->parts = $this->getParts($this->url);
			
			$this->cfg = cfg::getInstance();
			
			if(!empty($this->cfg->db->hostname) && !empty($this->cfg->db->username) && !empty($this->cfg->db->password) && !empty($this->cfg->db->database))
			{
				$this->db = new db();
			}
			
			return $this;
		}
		
		private function getParts($url)
		{
			return explode('/', substr($url,1), -1);
			
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
		
		public function registerUrl($url, $controller)
		{
			$parts = $this->getParts($url);
			
			$controller = str_replace('/', '\\', $controller);
			
			// if the current URL is / this will result in a 0-element array
			if(!count($parts))
			{
				if(!count($this->parts))
				{
					$this->depth = 0;
					$this->controller = $controller;
				}
				
				return;
			}
			
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
			
			$this->controller->run();
			
			if($this->view === null)
			{
				throw new Exception('no view set');
			}
			
			$this->view->run();
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
	}
?>