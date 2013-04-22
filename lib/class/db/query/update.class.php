<?php
	namespace kit\db\query;
	
	final class update extends base
	{
		protected $table;
		
		public function __construct($table)
		{
			$this->table = $table;
		}
		
		public function __toString()
		{
			$q = 'UPDATE';
			
			$q .= ' '. $this->table;
			
			$q .= ' SET ';
			
			$q .= $this->convertKeyAndValue($this->columns);
			
			if(count($this->where))
			{
				$q .= ' WHERE (';
				$where = Array();
				foreach($this->where AS $k => $v)
				{
					$where[] = '`'.$k."` = '".$v."'";
				}
				$q .= implode(') && (', $where);
				$q .= ')';
			}
			
			return $q;
		}
	}
?>