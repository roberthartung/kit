<?php
	namespace kit;
		
	use SimpleXMLElement;
	
	final class lang
	{
		use singletonTrait;
		
		private static $languages = Array();
		
		private static $language = 'de';
		
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
		
		public function getData($lang)
		{
			return $this->data[$lang];
		}
		
		private function parseXML($obj, $xml)
		{
			foreach($xml->children() AS $k => $v)
	  	{
 				if($v->count() > 0)
  			{
  				$this->parseXML($obj->$k, $v);
  			}
  			else
  			{
  				$obj->$k =(string)$v;
  			}
	  	}
		}
		
		public function addFile($file)
		{
	  	$xml = new SimpleXMLElement($file, null, true);
	  	
	  	foreach($xml->children() AS $child)
	  	{
	  		$language = $child->getName();
	  		if(!in_array($language, self::$languages))
				{
					self::$languages[] = $language;
				}
	  	}
	  	
	  	$this->parseXML($this, $xml);
	  	
	  	return $this;					
		}
		
		public function get()
		{
			$args = func_get_args();
			$lang = self::$language;
			$path = $this->$lang;
			$identifier = array_shift($args);
			while(isset($path->$identifier))
			{
				$path = $path->$identifier;
				$identifier = array_shift($args);
			}
			
			if($path instanceof self || empty($path))
				return implode('.', func_get_args());
			
			return $path;
		}
		
		public static function setLanguage($l)
		{
			if(!in_array($l, self::$languages))
			{
				throw new Exception('Language "'.$l.'" not found.');
			}
			
			return self::$language = $l;
		}
		
		public static function getLanguage()
		{
			return self::$language;
		}
	}
?>