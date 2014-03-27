<?php
	namespace kit;
		
	use SimpleXMLElement;
	
	final class lang
	{
		use singletonTrait;
		
		private static $languages = Array();
		
		private static $language = null;
		
		private static $best_language = null;
		
		private static $best_language_q = 0;
		
		public function __get($key)
		{
			$this->$key = new self;
			return $this->$key;
		}
		
		public function __set($key, $value)
		{
			if(empty($key)) {
				var_dump(self::getLanguage());
				throw new \Exception;
			}
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
	  	// Try to update prefered user language
	  	$this->getUserLanguage();
	  	
	  	return $this;					
		}
		
		public function get()
		{
			$args = func_get_args();
			$lang = self::getLanguage();
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
			return self::$language == null ? self::$best_language : self::$language;
		}
		
		public function getUserLanguage() {
			if(!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
				return;
			}
			
			$accepted_languages = preg_split('/,\s*/', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			
			foreach ($accepted_languages as $accepted_language) {
				$res = preg_match ('/^([a-z]{1,8}(?:-[a-z]{1,8})*)'.'(?:;\s*q=(0(?:\.[0-9]{1,3})?|1(?:\.0{1,3})?))?$/i', $accepted_language, $matches);
				if(!$res) {
					continue;
				}
				
				$lang_codes = explode ('-', $matches[1]);
				
				if (isset($matches[2])) {
					$lang_quality = (float)$matches[2];
				} else {
					$lang_quality = 1.0;
				}
				
				foreach($lang_codes AS $lang_code) {
					if($lang_quality > self::$best_language_q && in_array($lang_code, self::$languages)) {
						self::$best_language_q = $lang_quality;
						self::$best_language = $lang_code;
					}
				}
			}
		}
	}
?>