<?php
	namespace kit\db\query;
	
	final class delete extends base
	{
		protected $table;
		
		public function __construct($table)
		{
			$this->table = $table;
		}
		
		public function __toString()
		{
			$q = 'DELETE FROM ';
			
			$q .= ' '. $this->table;
			
			if(count($this->where))
			{
				$q .= ' WHERE (';
				$where = Array();
				foreach($this->where AS $k => $v)
				{
					if(is_array($v)) {
						$where[] = '`'.$k."` IN ('".implode("','", $v)."')";
					} else {
						$where[] = '`'.$k."` = '".$v."'";
					}
					
					
				}
				$q .= implode(') && (', $where);
				$q .= ')';
			}
			
			return $q;
		}
	}
?>