<?php
	namespace kit\db;
	
	use Exception;
	
	class queryException extends Exception
	{
		private $query;
		
		public function __construct($msg, $code, $query)
		{
			$this->query = $query;
			parent::__construct($msg, $code);
		}
		
		public function getQuery()
		{
			return $this->query;
		}
	}
?>