<?php
	namespace kit;
	
	trait singletonTrait {
		private static $instance;
		
		public static function getInstance()
		{
			if(!self::$instance instanceof self)
			{
				self::$instance = new self;
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