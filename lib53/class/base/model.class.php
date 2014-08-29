<?php
	namespace kit\base;
	
	use kit\singleton;
	use kit\kit as core;
	use kit\cfg;
	use kit\template\parser;

	abstract class model extends singleton {
		protected static $instance;
		
		protected $kit;
			
		protected $template_parser;
		
		protected $cfg;
		
		protected $db;
		
		public function loader() {
			$this->kit = core::getInstance();
			$this->cfg = cfg::getInstance();
			$this->template_parser = parser::getInstance();
			$this->db = $this->kit->getDatabase();
		}
	}
?>