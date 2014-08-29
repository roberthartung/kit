<?php
	namespace kit;
	
	abstract class singleton {
		public static function getInstance() {
			
			if(static::$instance == null) {
				static::$instance = new static;
			}
			
			return static::$instance;
		}
	}
?>