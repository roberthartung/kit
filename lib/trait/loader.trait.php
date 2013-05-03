<?php
	namespace kit;
	
	trait loaderTrait
	{
		protected $kit;
		
		protected $template_parser;
		
		protected $cfg;
		
		protected $db;
		
		public function __construct()
		{
			$this->kit = kit::getInstance();
			$this->cfg = cfg::getInstance();
			$this->template_parser = template\parser::getInstance();
			$this->db = $this->kit->getDatabase();
		}
	}
?>