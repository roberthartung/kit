<?php
	namespace kit;
	
	//use kit\templateTrait;
	
	use kit\template\templateNotFoundException;
	
	final class template
	{
		//use templateTrait;
		
		use loaderTrait { __construct as loader; }
		
		private $content;
		
		public function __construct($file)
		{
			$this->loader();
			
			if(!file_exists($file))
			{
				throw new templateNotFoundException($file);
			}
			
			$this->content = file_get_contents($file);
		}
		
		public function get()
		{
			return $this->template_parser->parse($this->content);
		}
	}
?>