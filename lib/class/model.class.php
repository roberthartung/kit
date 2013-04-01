<?php
	namespace kit;
	
	use \Iterator;
	
	class model implements modelInterface, Iterator
	{
		use loaderTrait;
		
		protected $query;
		
		protected $result;
		
		protected $columns;
		
		protected $executed = false;
		
		protected $data;
		
		protected $position = 0;
		
		public function __construct()
		{
			$this->position = 0;
		}
		
		public function getColumns()
		{
			return $this->columns;
		}
		
		public function getManipulator($offset = 0)
		{
			$this->execute();
			return $this->result->fetchObject('kit\\db\\result\\row', Array($this));
		}
		
		public function execute()
		{
			if($this->executed)
			{
				return;
			}
			
			$this->executed = true;
			$this->result = $this->db->query($this->buildQuery());
			$this->columns = Array();
			for($c=0;$c<$this->result->columnCount();$c++)
			{
				$meta = $this->result->getColumnMeta($c);
				$this->columns[$meta['name']] = $meta;
			}
			//$this->data = $this->result->fetchAll(PDO::FETCH_ASSOC);
			return $this->result;
		}
		
		public function next()
		{
			$this->execute();			
			return $this->result->fetch(PDO::FETCH_ASSOC);
		}
	}
?>