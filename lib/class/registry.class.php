<?php
	namespace kit;
		
	use Exception;
	
	final class registry
	{
		use singletonTrait;
		
		public function __get($key)
		{
			throw new Exception('Unknown key "'.$key.'"');
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