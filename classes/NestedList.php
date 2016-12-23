<?php
	class NestedList {
		
		class Node {
			private $data;
			private $next;
			private $child;

			public function __construct($data) {
				$this->data = $data;
				$this->next = null;
				$this->child = null;
			}

			public function setData($data) {
				$this->data = $data;
			}

			public function setNext($nextNode) {
				$this->next = $nextNode;
			}

			public function setChild($childNode) {
				$this->child = $childNode;
			}

			public function getData() {
				return $this->data;
			}

			public function getNext() {
				return $this->next;
			}

			public function getChild() {
				return $this->child;
			}
		} // End Node class

		private $head;
		private $tail;
		private $current;

		public function __construct() {
			$this->head = null;
			$this->tail = null;
			$this->current = null;
		}

		public function appendItem($data) {
			// Initialize Node only if this is the first element in NestedList
			if (is_null($this->head)) {
				$this->head = new Node($data);
				$this->tail = $this->head;
			} else {

				$temp = new Node($data);

				$this->tail->setNext($temp);
				$this->tail = $temp;
			}
		}

		public function appendChild($data) {
			if (is_null($this->head)) {
				exit("Error: Cannot append a child to an empty NestedList");
			}

			$temp = new Node($data);
			$this->tail->setChild($temp);
		}

		/* Constructor that allows a variable number of arguments
		public function __construct() {
			$argv = func_get_args();
			switch(func_num_args()) {
				case 1:
					self::__construct1($argv[0]);
					break;
			}
		}

		public function __construct1($data) {
			$this->data = $data;
		}
		*/

		public function setPrev($nestedList) {
			$this->prev = $nestedList;
		}

		public function setNext($nestedList) {
			$this->next = $nestedList;
		}

		public function setChild($nestedList) {
			$this->child = $nestedList;
		}

		public function setData($data) {
			$this->data = $data;
		}

		public function getPrev() {
			return $this->prev;
		}

		public function getNext() {
			return $this->next;
		}

		public function getChild() {
			return $this->child;
		}

		public function getData() {
			return $this->getData();
		}
	}
?>
