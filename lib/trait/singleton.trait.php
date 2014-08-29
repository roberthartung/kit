<?php
	namespace kit;
	
	trait singletonTrait {
		private static $instance;
		
		public static function getInstance()
		{
			if(!self::$instance instanceof static)
			{
				self::$instance = new static;
			}
			
			return self::$instance;
		}
		
		protected function __construct()
		{
			
		}
		
		protected function __clone()
		{ 
			
		}
	}
?>