<?php
	namespace kit\db\query;
	
	final class select extends base
	{
		protected $from = 'DUAL';
		
		protected $from_alias;
		
		public function __toString()
		{
			$q = 'SELECT';
			
			if(!count($this->columns))
			{
				$q .= ' *';
			}
			else
			{
				$q .= implode(', ', $this->columns);
			}
			
			$q .= ' FROM '.$this->from;
			
			if($this->from_alias !== null)
			{
				$q .= ' as '.$this->from_alias;
			}
			
			if(count($this->joins))
			{
				$q .= ' '.implode(' ', $this->joins);
			}
			
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
		
		public function from($tbl, $alias = null)
		{
			$this->from = $tbl;
			$this->from_alias = $alias;
		}
	}
?>