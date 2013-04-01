<?php
	namespace kit\db\query;
	
	final class select
	{
		private $from = 'DUAL';
		
		private $from_alias;
		
		private $colums = Array();
		
		public function __toString()
		{
			$q = 'SELECT';
			
			if(!count($this->colums))
			{
				$q .= ' *';
			}
			else
			{
				$q .= implode(', ', $this->colums);
			}
			
			$q .= ' FROM '.$this->from;
			
			if($this->from_alias !== null)
			{
				$q .= ' as '.$this->from_alias;
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