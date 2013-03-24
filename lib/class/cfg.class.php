<?php
	/**
	 * Config Object for KIT
	 * 
	 * @package config
	 */
	 
	namespace kit;
	
	final class cfg
	{
		use singletonTrait;
		
		public function __get($key)
		{
			$this->$key = new self;
			return $this->$key;
		}
		
		public function __set($key, $value)
		{
			$this->$key = $value;
		}
		
		public function __unset($key)
		{
			if(isset($this->$key))
			{
				$this->$key = null;
			}
		}
		
		public function __isset($key)
		{
			foreach($this AS $k => $v)
			{
				if($k == $key)
					return true;
			}
			
			return false;
		}
	}
?>