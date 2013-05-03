<?php
	namespace kit;
	
	//use kit\templateTrait;
	
	use kit\template\templateNotFoundException;
	
	final class template
	{
		//use templateTrait;
		
		use loaderTrait { __construct as loader; }
		
		private $content;
		
		private $file;
		
		private $file_path;
		
		private $file_name;
		
		private $data = Array();
		
		private $dir_cached;
		
		private $path_cached;
		
		private $force_compile = false;
		
		public function __set($k, $v)
		{
			$this->data[$k] = $v;
		}
		
		public function __construct($file)
		{
			$this->loader();
			
			if(!file_exists($file))
			{
				throw new templateNotFoundException($file);
			}
			
			$this->file = $file;
			$this->content = file_get_contents($this->file);
			$this->file_path = dirname($file).DIRECTORY_SEPARATOR;
			$this->file_name = basename($file);
			$this->dir_cached = PATH_SITE_ROOT.'cache'.DIRECTORY_SEPARATOR.$this->file_path;
			$this->path_cached = $this->dir_cached.$this->file_name;
		}
		
		public function get(array $data = Array(), array $data_add = Array())
		{
			ob_start();
			/*
			array $data = Array()
			*/
			if(!file_exists($this->dir_cached))
			{
				mkdir($this->dir_cached, 0777, true);
			}
			
			if(!file_exists($this->path_cached) || filemtime($this->path_cached) < filemtime($this->file) || $this->force_compile)
			{
				$template = $this->template_parser->parse($this->content, $this->file_path, $this->file_name);
				file_put_contents($this->path_cached, $template);
			}
			
			$data = array_merge($data, $this->data, $data_add);
			
			/*
			foreach($this->data AS $___k => $___v)
			{
				$$___k = $___v;
			}
			*/
			
			foreach($data AS $___k => $___v)
			{
				$$___k = $___v;
			}
			
			include($this->path_cached);
			
			return ob_get_clean();
		}
	}
?>