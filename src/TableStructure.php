<?php

namespace DanielJHarvey\QueryStinkers;

class TableStructure {
	
	protected $pdo;
	protected $dbName;
	protected $indexes=[];
	protected $tables;

	protected $largeTableMinimum=100;

	public function __construct(\PDO $pdo, $dbName) {
		$this->pdo = $pdo;
		$this->dbName = $dbName;
	}

	public function getTableStructure() {
		if ($this->tables) return $this->tables;
		$this->tables = $this->readTableStructure();
		return $this->tables;
	}

	protected function readTableStructure() {
		$sql = "SHOW TABLES FROM {$this->dbName}";

		$data = $this->pdo->query($sql);
		
		$tables = [];
		
		foreach ($data as $item) {
			$tableName = $item[0];
			$table = $this->getTable($tableName);
			$tables[$tableName] = $table;
		}
		
		return $tables;
	}

	protected function getTableLength($tableName) {
		$sql = "SELECT COUNT(*) FROM {$tableName}";

		$data = $this->pdo->query($sql);
		
		foreach ($data as $item) {
			return $item[0];
		}	
	}

	protected function getTable($tableName) {
		$table = [
			'tableName'=>$tableName,
			'length'=>$this->getTableLength($tableName),
			'indexes'=>$this->getTableIndexes($tableName),
			'columns'=>$this->getTableColumns($tableName)
		];
		
		$table['large'] = ($table['length'] > $this->largeTableMinimum);
	
		return $table;
	}
	
	protected function getTableIndexes($tableName) {
		if (!$this->indexes) $this->loadIndexes($this->dbName);

		if (!array_key_exists($tableName, $this->indexes)) return false;

		return $this->indexes[$tableName];

	}

	protected function loadIndexes($dbName) {
		$sql = "SELECT table_name AS `Table`,
			       index_name AS `Index`,
				          GROUP_CONCAT(column_name ORDER BY seq_in_index) AS `Columns`
						  FROM information_schema.statistics
						  WHERE table_schema = '{$dbName}'
						  GROUP BY 1,2";
		$data = $this->pdo->query($sql);

		foreach ($data as $item) {
			$tableName = $item['Table'];
			if (!array_key_exists($tableName, $this->indexes)) {
				$this->indexes[$tableName] = [];
			}
			$index=[
				'name'=>$item['Index'],
				'columns'=>explode(',',$item['Columns']),
				'tableName'=>$tableName
			];
			array_push($this->indexes[$tableName], $index);
		}
	}

	protected function getTableColumns($tableName) {
		$sql = "DESCRIBE {$tableName}";

		$data = $this->pdo->query($sql);

		$columns=[];
		
		foreach ($data as $item) {
			array_push($columns, $item['Field']);
		}

		return $columns;
	}


}
