<?php
	namespace kit\db\query;
	
	abstract class base
	{
		protected $where = Array();
		
		protected $joins = Array();
		
		protected $columns = Array();
		
		public function where(array $where)
		{
			$this->where = array_merge($this->where, $where);
		}
		
		public function getWhere()
		{
			return $this->where;
		}
		
		public function join_using($tbl_name, $using, $tbl_alias = null, $type = 'JOIN')
		{
			if(!is_array($using))
			{
				$using = Array($using);
			}
			$this->joins[] = $type.' '.$tbl_name.($tbl_alias != null ? ' AS '.$tbl_alias : '').' USING (`'.implode('`,`', $using).'`)';
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
				$q .= '`'.$k.'` = '.(strpos($v,':') === 0 ? $v : '`'.$v.'`').'';
				
				if($i++ < $count)
				{
					$q .= ', ';
				}
			}
			return $q;
		}
	}
?>