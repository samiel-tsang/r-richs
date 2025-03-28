<?php
namespace Database;

class Sql {
	public static function select($table) { return new self('select', $table); }
	public static function insert($table) { return new self('insert', $table); }
	public static function update($table) { return new self('update', $table); }
	public static function delete($table) { return new self('delete', $table); }
	
	private $op;
	private $table;
	private $field;
	private $where;
	private $order;
	private $limit;
	private $having;
	
	public function __construct($operation, $table) {
		$this->op = strtoupper($operation); 
		$this->table = (is_array($table))?"`$table[0]` $table[1]":"`$table`";
		$this->field = '*';
		$this->value = '';
		$this->where = '';
		$this->order = '';
		$this->limit = '';
		$this->having = '';
	}
	
	public function table($table) {
		$this->table = (is_array($table))?"`$table[0]` $table[1]":"`$table`";
		return $this;
	}
	
	public function where($oper = [], $inBetweenOp = 'AND') {
		$where = "$oper[0] $oper[1] $oper[2]";
		return $this->whereOp($where, $inBetweenOp);
	}
	
	public function whereBetween($field, $val1, $val2, $inBetweenOp = 'AND') {
		$where = "$field BETWEEN $val1 AND $val2";
		return $this->whereOp($where, $inBetweenOp);
	}
	
	public function whereOp($where, $inBetweenOp = 'AND') {
		if ($this->op == 'INSERT') return $this;
		
		if (!empty($this->where)) {
			$this->where .= " $inBetweenOp $where";
		} else {
			$this->where = $where;
		}
		return $this;
	}
	
	public function emptyWhere(String $where = "", $inBetweenOp = 'AND') {
		$this->where = '';
		return $this->whereOp($where, $inBetweenOp);
	}

	// having
	public function having($oper = [], $inBetweenOp = 'AND') {
		$having = "$oper[0] $oper[1] $oper[2]";
		return $this->havingOp($having, $inBetweenOp);
	}
	
	public function havingBetween($field, $val1, $val2, $inBetweenOp = 'AND') {
		$having = "$field BETWEEN $val1 AND $val2";
		return $this->havingOp($having, $inBetweenOp);
	}
	
	public function havingOp($having, $inBetweenOp = 'AND') {
		if ($this->op == 'INSERT') return $this;
		
		if (!empty($this->having)) {
			$this->having .= " $inBetweenOp $having";
		} else {
			$this->having = $having;
		}
		return $this;
	}
	
	public function emptyHaving(String $having = "", $inBetweenOp = 'AND') {
		$this->having = '';
		return $this->whereOp($having, $inBetweenOp);
	}
	
	
	public function order($field, $ord = 'ASC') {
		if ($this->op != 'SELECT') return $this;
		
		if (!empty($this->order)) {
			$this->order .= ", $field $ord";
		} else {
			$this->order = "$field $ord";
		}
		return $this;
	}
	
	public function limit($count, $offset=0) {
		if ($this->op != 'SELECT') return $this;
		
		$this->limit = '';
		if ($count)
			$this->limit .= "$count";
		if ($offset)
			$this->limit .= " OFFSET $offset";
		return $this;
	}
	
	public function leftJoin($table, $cond) {
		return $this->tableJoin('LEFT JOIN', $table, $cond);
	}
	
	public function rightJoin($table, $cond) {
		return $this->tableJoin('RIGHT JOIN', $table, $cond);
	}
	
	public function tableJoin($type, $table, $cond) {
		if ($this->op != 'SELECT') return $this;
		
		$joinType = strtoupper($type);
		$this->table .= " $joinType ".((is_array($table))?"`$table[0]` $table[1]":"`$table`")." ON $cond";
		return $this;
	}
	
	public function setFieldValue($fields = '*') {
		if (!is_array($fields)) {
			$this->field = $fields;
			return $this;
		}
		
		if ($this->op == 'DELETE') {
			return $this;
		} else if ($this->op == 'INSERT') {
			$this->field = implode(',', array_keys($fields));
			$this->value = implode(',', array_values($fields));
		} else if ($this->op == 'UPDATE') {
			$this->value = [];
			foreach ($fields as $name => $value) {
				$this->value[] = "`$name` = $value";
			}
			$this->field = implode(", ", $this->value);
		} else {
			$this->field = array_values($fields);
		}
		
		return $this;
	}
	
	public function execute($idx = 0) {
		return db($idx)->exec($this);
	}
	
	public function prepare($idx = 0) {
		$stm = db($idx)->prepare($this);
		$stm->setFetchMode(\PDO::FETCH_NAMED);
		return $stm;
	}
	
	public function __toString() {
		if ($this->op == 'INSERT') {
			$sql = "$this->op $this->table ($this->field) VALUES ($this->value)";
		} else if ($this->op == 'UPDATE') {
			$sql = "$this->op $this->table SET $this->field";
			if (!empty($this->where))
				$sql .= " WHERE $this->where";
		} else if ($this->op == 'DELETE') {
			$sql = "$this->op FROM $this->table";
			if (!empty($this->where))
				$sql .= " WHERE $this->where";
		} else {
			$sql = "$this->op $this->field FROM $this->table";
			if (!empty($this->where))
				$sql .= " WHERE $this->where";
			if (!empty($this->having))
				$sql .= " HAVING $this->having"	;		
			if (!empty($this->order)) 
				$sql .= " ORDER BY $this->order";
			if (!empty($this->limit))
				$sql .= " LIMIT $this->limit";
		}
		return $sql;	
	}
}