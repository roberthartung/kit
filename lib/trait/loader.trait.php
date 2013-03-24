<?php
	namespace kit;
	
	trait loaderTrait
	{
		private $kit;
		
		private $template_parser;
		
		private $cfg;
		
		public function __construct()
		{
			$this->kit = kit::getInstance();
			$this->cfg = cfg::getInstance();
			$this->template_parser = template\parser::getInstance();
		}
	}
?>