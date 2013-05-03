<?php
	namespace kit\db;
	
	use PDOStatement;
	use PDO;
	use Iterator;
	use kit\db\result\offsetException;
	
	class result implements Iterator
	{
		private $result;
		
		private $colums;
		
		private $column_primary;
		
		private $index = 0;
		
		private $data;
		
		private $query;
		
		private $num_rows = null;
		
		private $wrappers = Array();
		
		private $columns = Array();
		
		public function __construct(PDOStatement $result, query $query = null)
		{
			$this->result = $result;
			$this->query = $query;
			for($c=0;$c<$this->result->columnCount();$c++)
			{
				$meta = $this->result->getColumnMeta($c);
				$this->columns[$meta['name']] = $meta;
			}
			
			// var_dump($this->result);
			
			
			/*
			for($c=0;$c<$result->columnCount();$c++)
			{
				$metaData = $result->getColumnMeta($c);
				
				if(in_array('primary_key', $metaData['flags']))
				{
					$this->column_primary = $metaData;
				}
			}
			*/
			
			// we moved this out of here to be able to use the result class (instead of any other class) for wrappers
			// this way you can call execute() on a model or even make a plain $db->query("SELECT ...") and add wrapper
			// to it
			// $this->fetchData();
		}
		
		private function fetchData()
		{
			if($this->num_rows === null)
			{
				$this->data = $this->result->fetchAll(PDO::FETCH_CLASS, 'kit\\db\\result\\row', Array($this->columns, $this->query, $this->wrappers));
				$this->num_rows = count($this->data);
			}
		}
		
		public function addWrapper($wrapper)
		{
			$this->wrappers[] = $wrapper;
			
			return $this;
		}
		
		public function num_rows()
		{
			$this->fetchData();
			
			return $this->num_rows;
		}
		
		public function rewind()
		{
        $this->index = 0;
    }

    public function current()
		{
			$this->fetchData();
			
      return $this->data[$this->index];
    }

    public function key()
		{
  		return $this->index;
    }

    public function next()
		{
    	$this->index++;
    }

    public function valid()
		{
			$this->fetchData();
  		return isset($this->data[$this->index]);
    }
		
		/*
		public function next()
		{
			return $this->result->fetch(PDO::FETCH_ASSOC);
		}
		*/
		
		public function getRow($offset = 0)
		{
			$this->fetchData();
			
			if(!isset($this->data[$offset]))
			{
				throw new offsetException();
			}
			
			return $this->data[$offset];
		}
	}
?>