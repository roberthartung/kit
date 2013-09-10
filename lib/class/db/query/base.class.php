<?php
	namespace kit\db\query;
	
	abstract class base
	{
		protected $where = Array();
		
		protected $group_by = Array();
		
		protected $order_by = Array();
		
		protected $joins = Array();
		
		protected $columns = Array();
		
		protected $escape_columns = true;
		
		protected $limit;
		
		public function getColumns()
		{
			$keys = array_keys($this->columns);
			if(is_string($keys[0]))
			{
				return $keys;
			}
			else
			{
				return array_values($this->columns);
			}
		}
		
		public function disableColumnEscaping()
		{
			$this->escape_columns = false;
		}
		
		public function where(array $where)
		{
			$this->where = array_merge($this->where, $where);
		}
		
		public function order_by($column, $type = 'ASC')
		{
			$this->order_by[$column] = $type;
		}
		
		public function limit($offset, $length = null)
		{
			$this->limit = $length == null ? $offset : "$offset, $length";
		}
		
		public function group_by($group_by)
		{
			if(!is_array($group_by))
			{
				$group_by = Array($group_by);
			}
			
			$this->group_by = array_merge($this->group_by, $group_by);
		}
		
		public function getWhere()
		{
			return $this->where;
		}
		
		public function getOrderBy()
		{
			return $this->order_by;
		}
		
		public function join_using($tbl_name, $using, $tbl_alias = null, $type = 'JOIN')
		{
			if(!is_array($using))
			{
				$using = Array($using);
			}
			$this->joins[] = $type.' '.$tbl_name.($tbl_alias != null ? ' AS '.$tbl_alias : '').' USING (`'.implode('`,`', $using).'`)';
		}
		
		public function join_on($tbl_name, $on, $tbl_alias = null, $type = 'JOIN')
		{
			$this->joins[] = $type.' '.$tbl_name.($tbl_alias != null ? ' AS '.$tbl_alias : '').' ON '.$on;
		}
		
		public function addColumn($k, $v)
		{
			$this->addColumns(Array($k => $v));
		}
	 
		public function addColumns(array $columns)
		{
			$this->columns = array_merge($this->columns, $columns);
		}
		
		/**
		 * Helper function to convert names and values
		 */
		
		protected function convertKeyAndValue($a)
		{
			$count = count($a);
			$i = 1;
			$q = '';
			foreach($a AS $k => $v)
			{
				$q .= '`'.$k.'` = '.(strpos($v,':') === 0 ? $v : "'".$v."'").'';
				
				if($i++ < $count)
				{
					$q .= ', ';
				}
			}
			return $q;
		}
	}
?>