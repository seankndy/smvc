<?php
namespace SeanKndy\SMVC\Database;

class Query
{
	protected $where;
	protected $sql = '';
	
    public static function select($table, array $columns = []) {
		$query = new Query();
		$query->sql = "select " . implode(',', $columns) . " from `$table`";
		return $query;
	}
	
	public function where(array $where) {
		foreach ($where as $k => $v) {
			$this->where 
		$this->where = $where;
		return $this;
	}
	
	public function __toString() {
		$sql = $this->sql;
		if ($this->where) {
			$sql .= " where foo";
		}
		return $sql;
	}
}
/*
echo Query::select('mikrotiks', ['name','ip']);
echo "\n";
echo Query::select('mikrotiks', ['name','ip'])->where(['id' => 1]);
*/
