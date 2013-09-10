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
				$q .= $this->escape_columns ? (' `'.implode('`, `', $this->columns).'`') : (' '.implode(', ', $this->columns));
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
					if($k[0] === '%')
					{
						$where[] = substr($k,1)." LIKE '".$v."%'";
					}
					else
					{
						$where[] = (strpos($k, '.') === false ? '`'.$k.'`' : $k)." = '".$v."'";
					}
				}
				$q .= implode(') && (', $where);
				$q .= ')';
			}
			
			if(count($this->group_by))
			{
				$q .= ' GROUP BY '.implode(', ', $this->group_by);
			}
			
			if(count($this->order_by))
			{
				$order = Array();
				
				foreach($this->getOrderBy() AS $col => $type)
				{
					$order[] = $col." ".$type;
				}
				
				$q .= ' ORDER BY '.implode(' , ', $order);
			}
			
			if($this->limit != null)
			{
				$q .= ' LIMIT '.$this->limit;
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