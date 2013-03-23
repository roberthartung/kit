<?php
	namespace kit;
	
	use kit\kit;
	use kit\template\parser;
	
	trait loaderTrait
	{
		private $kit;
		
		private $template_parser;
		
		public function __construct()
		{
			$this->kit = kit::getInstance();
			$this->template_parser = template\parser::getInstance();
		}
	}
?>