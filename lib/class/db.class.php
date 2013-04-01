<?php
	namespace kit;
	
	use PDO;
	use Exception;
	
	class db
	{
		use loaderTrait { __construct as loader; }
		
		private $conn;
		
		public function __construct()
		{
			// PDO::getAvailableDrivers()
			
			// Load
			$this->loader();
			
			switch($this->cfg->db->type)
			{
				case 'mysql' :
					$dsn = 'mysql:dbname='.$this->cfg->db->database.';host='.$this->cfg->db->hostname;
				break;
			}
			
			$this->conn = new PDO($dsn, $this->cfg->db->username, $this->cfg->db->password);
		}
		
		public function __call($method, $args)
		{
			$result = call_user_func_array(Array($this->conn, $method), $args);
			
			if($method === 'query')
			{
				if(!$result->execute())
				{
					throw new Exception;
				}
				
				if(is_string($args[0]))
				{
					return new db\result($result);
				}
			}
			
			
			
			
			
			/*
			if($method == 'query')
			{
				
			}
			*/
			
			return $result;
			
			/*
			if($method === 'query' && count($args) === 1 && $this->cfg->db->orm)
			{
				return $result->fetchObject('kit\\db\\result', Array($result));
			}
			
			return call_user_func_array(Array($this->conn, $method), $args);
			*/
		}
	}
?>