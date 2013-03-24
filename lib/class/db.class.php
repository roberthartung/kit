<?php
	namespace kit;
	
	use PDO;
	
	class db
	{
		use loaderTrait { __construct as loader; }
		
		public function __construct()
		{
			// $host, $user, $pass, $db, $type = KIT_DEFAULT_DB_TYPE
			// PDO::getAvailableDrivers()
			
			$this->loader();
			
			switch($this->cfg->db->type)
			{
				case 'mysql' :
					$dsn = 'mysql:dbname='.$this->cfg->db->database.';host='.$this->cfg->db->hostname;
				break;
			}
			
			$this->db = new PDO($dsn, $this->cfg->db->username, $this->cfg->db->password);
		}
		
		public function __call($method, $args)
		{
			
		}
	}
?>