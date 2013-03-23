<?php
	/**
	 * @author Robert Hartung
	 * @copyright KIT Framework, rhScripts
	 * @package kit
	 * @subpackage helper
	 * @version 1.0 
	 */
	
	namespace kit\helper;
	
	use ReflectionClass;
	
	abstract class singleton
	{
		private function __construct()
		{
			
		}
		
		protected function __clone()
		{
			
		}
		
		/**
		 * @todo Fix Arguments
		 * @return self
		 */
		
		public static function getInstance()
		{
			if(!static::$instance instanceof static)
			{
				static::$instance = new static;
				
				/*
				$x = call_user_func_array(get_called_class().'::__construct', func_get_args());
				var_dump($x);
				*/
				
				//$reflect  = new ReflectionClass(get_called_class());
				//$instance = $reflect->newInstanceArgs(func_get_args());
			}
			
			return static::$instance;
		}
	}
?>