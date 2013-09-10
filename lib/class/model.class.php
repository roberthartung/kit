<?php
	namespace kit;
  
  use PDO;
  use kit\db\queryException;
  use kit\db\query\select;
  use kit\db\query\delete;
  use kit\db\query\update;
  use PDOStatement;
  
	class model implements modelInterface
	{
		use loaderTrait { __construct as loader; }
		
		protected $query;
		
		protected $result;
		
		protected $columns;
		
		protected $executed = false;
		
		protected $data;
		
		protected $position = 0;
		
		protected $select;
		
		protected $error;
		
		public function __construct()
		{
			$this->position = 0;
			$this->loader();
		}
		
		public function getColumns()
		{
			return $this->columns;
		}
		
		public function getError()
		{
			return Array('msg' => $this->error[2], 'code' => $this->error[1]);
		}
		
		/*
		public function getManipulator($offset = 0)
		{
			$this->execute();
			return $this->result->fetchObject('kit\\db\\result\\row', Array($this));
		}
		*/
		
		/**
		 * @deprecated
		 */

		public function execute()
		{
			if($this->executed)
			{
				return;
			}
			
			$this->executed = true;
			$this->result = $this->db->query($this->buildQuery());
			$this->columns = Array();
			for($c=0;$c<$this->result->columnCount();$c++)
			{
				$meta = $this->result->getColumnMeta($c);
				$this->columns[$meta['name']] = $meta;
			}
			//$this->data = $this->result->fetchAll(PDO::FETCH_ASSOC);
			return $this->result;
		}
		
		/*
		public function next()
		{
			$this->execute();			
			return $this->result->fetch(PDO::FETCH_ASSOC);
		}
		*/
		
		private function bindValues(PDOStatement $stmnt, array $data)
		{
			foreach($data AS $column => $value)
			{
				if(is_null($value))
				{
					$stmnt->bindValue(':'.$column, $value, PDO::PARAM_NULL);
				}
				elseif(is_numeric($value))
				{
					$stmnt->bindValue(':'.$column, $value, PDO::PARAM_INT);
				}
				elseif(is_bool($value))
				{
					$stmnt->bindValue(':'.$column, $value, PDO::PARAM_BOOL);
				}
				else
				{
					$stmnt->bindValue(':'.$column, $value, PDO::PARAM_STR);
				}
			}
		}
		
		public function insert($data)
		{
			if($data instanceof select)
			{
				$columns = $data->getColumns();
				
				$query = "INSERT INTO ".$this->table_name." (`".implode('`,`', $columns)."`) ".$data;
				$stmnt = $this->db->prepare($query);
				
				if(!$stmnt->execute())
				{
					$error = $stmnt->errorInfo();
					throw new queryException($error[2], $error[1], $stmnt);
				}
				
				// if we inserted more than one row return true
				if($stmnt->rowCount() > 1)
				{
					return true;
				}
				
				return $this->db->lastInsertId();
			}
		
			$data = array_intersect_key($data, $this->columns);
			$columns = array_keys($data);
			$query = "INSERT INTO ".$this->table_name." (`".implode('`,`', $columns)."`) VALUES (:".implode(', :', $columns).")";
			$stmnt = $this->db->prepare($query);
			
			$is_multiple_insert = false;
			$multiple_columns = Array();
			foreach($data AS $k => $v)
			{
				if(is_array($v))
				{
					$is_multiple_insert = true;
					$multiple_columns[] = $k;
					break;
				}
			}
			
			if($is_multiple_insert)
			{
				$cnt = count($data[$multiple_columns[0]]);
				$insert_ids = Array();
				for($i=0;$i<$cnt;$i++)
				{
					$values = Array();
					foreach($data AS $k => $v)
					{
						if(is_array($v))
						{
							$values[':'.$k] = $v[$i];
						}
						else
						{
							$values[':'.$k] = $v;
						}
					}
					
					if(!$stmnt->execute($values))
					{
						$error = $stmnt->errorInfo();
						throw new queryException($error[2], $error[1], $stmnt);
					}
					
					$insert_ids[] = $this->db->lastInsertId();
				}
				
				return $insert_ids;
			}
			else
			{
				$this->bindValues($stmnt, $data);
				
				if(!$stmnt->execute())
				{
					$error = $stmnt->errorInfo();
					throw new queryException($error[2], $error[1], $stmnt);
				}
				
				return $this->db->lastInsertId();
			}
		}
		
		/**
		 * Updates a row in this model
		 */
		
		public function update(array $data, array $where = Array())
		{
			$data = array_intersect_key($data, $this->columns);
			
			$columns = array_keys($data);
			
			$prepared_data = Array();
			foreach($columns AS $col)
			{
				$prepared_data[$col] = ':'.$col;
			}
			
			$qry_update = new update($this->table_name);
			$qry_update->addColumns($prepared_data);
			if($this->select != null)
				$qry_update->where($this->select->getWhere());
			
			if(count($where))
				$qry_update->where($where);
			
			$stmnt = $this->db->prepare($qry_update);
			
			$this->bindValues($stmnt, $data);
			
			if(!$stmnt->execute())
			{
				$error = $stmnt->errorInfo();
				throw new queryException($error[2], $error[1], $stmnt);
			}
			
			return true;
		}
		
		public function delete(array $where)
		{
			$qry_delete = new delete($this->table_name);
			if(count($where))
				$qry_delete->where($where);
			
			$stmnt = $this->db->prepare($qry_delete);
			
			//$this->bindValues($stmnt, $data);
			
			if(!$stmnt->execute())
			{
				$error = $stmnt->errorInfo();
				throw new queryException($error[2], $error[1], $stmnt);
			}
			
			return true;
		}
	}
?>