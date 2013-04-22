<?php
	namespace kit\db;
	
	use PDOStatement;
	use PDO;
	use Iterator;
	use Exception;
	
	class result implements Iterator
	{
		private $result;
		
		private $colums;
		
		private $column_primary;
		
		private $index = 0;
		
		private $data;
		
		private $query;
		
		public function __construct(PDOStatement $result, query $query = null)
		{
			$this->result = $result;
			$this->query = $query;
			$columns = Array();
			for($c=0;$c<$result->columnCount();$c++)
			{
				$meta = $result->getColumnMeta($c);
				$columns[$meta['name']] = $meta;
			}
			
			// var_dump($this->result);
			$this->data = $this->result->fetchAll(PDO::FETCH_CLASS, 'kit\\db\\result\\row', Array($columns, $this->query));
			
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
		}
		
		public function rewind()
		{
        $this->index = 0;
    }

    public function current()
		{
        return $this->data[$this->index];
    }

    public function key() {
        return $this->index;
    }

    public function next() {
        ++$this->index;
    }

    public function valid()
		{
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
			if(!isset($this->data[$offset]))
			{
				throw new Exception;
			}
			
			return $this->data[$offset];
		}
	}
?>