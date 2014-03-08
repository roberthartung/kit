<?php
	namespace kit;
	
	final class pagination {
		private $qry;
		
		private $total;
		
		private $variable;
		
		private $page = 1;
		
		private $offset = 0;
		
		private $pages_count;
		
		private $limit;
		
		private $view;
		
		public function __construct($qry, $view, $limit = 50, $base_url = '', $variable = 'page') {
			// Variables
			$this->qry = $qry;
			$this->view= $view;
			$this->limit = $limit;
			// get total count
			$this->total = $this->qry->countAll();
			// Calc pages
			$this->pages_count = ceil($this->total / $this->limit);
			// set current pages
			if(isset($_GET[$variable]) && intval($_GET[$variable]) && $_GET[$variable] >= 1 && $_GET[$variable] <= $this->pages_count) {
				$this->page = $_GET[$variable];
			}
			// offset
			$this->offset = ($this->page-1) * $this->limit;
			// attach variables to view
			$pages = Array();
			foreach(range(1,$this->pages_count) AS $page) {
				$pages[] = $page;
			}
			$this->view->pagination_pages = $pages;
			$this->view->pagination_page = $this->page;
			if(substr($base_url,-1,1) != '&' && substr($base_url,-1,1) != '?' && substr($base_url,-5,5) != '&amp;') {
				$base_url .= '?';
			}
			$this->view->pagination_base_url = $base_url;
		}
		
		public function execute() {
			return $this->qry->limit($this->offset, $this->limit)->execute();
		}
	}
?>