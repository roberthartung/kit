<?php
	namespace kit;
	
	use PDO;
	use Exception;
	use kit\db\queryException;
	
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
			
			if(!$result)
			{
				$error = $this->conn->errorInfo();
				throw new queryException($error[2], $error[1], $args[0]);
			}
			
			if($method === 'query')
			{
				/*
				if(!$result->execute())
				{
					throw new Exception;
				}
				*/
				
				// If this was a string query
				if(is_string($args[0]))
				{
					return new db\result($result);
				}
				elseif($args[0] instanceof db\query)
				{
					return new db\result($result, $args[0]);
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
		
		/**
		 * @todo use db\query\insert class
		 */
		
		public function insert($table, array $data)
		{
			$columns = array_keys($data);
			$values = Array();
			foreach(array_values($data) AS $val)
			{
				if($val === null)
				{
					$values[] = 'NULL';
				}
				elseif(is_numeric($val))
				{
					$values[] = $val;
				}
				else
				{
					$values[] = $this->quote($val);
				}
			}
			
			return $this->conn->query("INSERT INTO ".$table." (".implode(', ', $columns).") VALUES (".implode(',', $values).")");
		}
	}
?>