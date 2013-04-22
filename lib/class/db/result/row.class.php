<?php
	namespace kit\db\result;
	
	use kit\db\query\update;
	use kit\db\query;
	
	class row
	{
		protected $columns;
		
		protected $changed = Array();
		
		protected $column_pk = null;
		
		private $query;
		
		public function __construct(array $columns, query $query = null)
		{
			$this->columns = $columns;
			$this->query = $query;
			
			foreach($this->columns AS $column_name => $column_info)
			{
				if(in_array('primary_key', $column_info['flags']))
				{
					$this->column_pk = $column_name;
					break;
				}
			}
		}
		
		public function __call($func, $args)
		{
			if(strpos($func, 'set') === 0)
			{
				$col_name = substr($func,4);
				if(array_key_exists($col_name, $this->columns))
				{
					if($this->$col_name != $args[0])
					{
						$this->$col_name = $args[0];
						$this->changed[$col_name] = $args[0];
					}
					return true;
				}
			}
			elseif(strpos($func, 'get') === 0)
			{
				$col_name = substr($func,4);
				if(array_key_exists($col_name, $this->columns))
				{
					return $this->$col_name;
				}
			}
			
			return null;
		}
		
		public function update(array $data = Array())
		{
			if(!count($this->changed))
			{
				return null;
			}
			
			$data = array_merge($data, $this->changed);
			
			if($this->query != null)
			{
				if(($model = $this->query->getModel()) !== null)
				{
					return $model->update($data);
				}
			}
			
			/*
			echo '<pre>';
			
			
			$valid_columns = $this->columns;
			if($this->column_pk != null)
			{
				unset($valid_columns[$this->column_pk]);
			}
			
			$keys = array_keys($valid_columns);
			$table_name = $valid_columns[$keys[0]]['table'];
			var_dump($valid_columns);
			
			$data = array_intersect_key($data, $this->columns);
			
			$query = new update($table_name);
			
			if($this->column_pk != null)
			{
				$query->where(Array($this->column_pk => $this->{$this->column_pk}));
			}
			
			var_dump((string) $query);
			
			//var_dump($this->columns);
			*/
		}
	}
?>