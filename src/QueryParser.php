<?php

namespace DanielJHarvey\QueryStinkers;

class QueryParser {
	
	protected $tables;

	public function __construct($tables) {
		if (!$tables || !is_array($tables)) {
			throw new \Exception("Invalid table data supplied!");
		}
		$this->tables = $tables;
	}

	public function parseQuery($sql) {
		$parts = explode('WHERE', $sql);
		$foundTables=$this->parseQueryTables($parts[0], $this->tables);
		
		if (!array_key_exists(1,$parts)) return $foundTables;

		foreach ($foundTables as $foundTable=>$table) {
			$columns=$this->parseWhere($parts[1], $table);
			$table['columns']=$columns;
			$foundTables[$foundTable] = $table;
		}
		return $foundTables;
	}

	protected function parseWhere($sql, $table) {
		$parts=preg_split('/\.\s+|[^a-z0-9.\']+/i', $sql);
		
		$columns=[];
		foreach ($parts as $part) {
			if (strlen($part) == 0) continue;
			if ($table['alias'] && strpos($part,'.') !== false) {
				$elements = explode('.',$part);
				if ($elements[0]==$table['alias']) {
					$part=$elements[1];
					if ($this->checkTableHasColumn($table['tableName'], $part)) {
					array_push($columns, $part);
				}
				}
			} else {
				if ($this->checkTableHasColumn($table['tableName'], $part)) {
					array_push($columns, $part);
				}
			}
			
		}
		return $columns;
	}

	protected function checkTableHasColumn($tableName, $columnName) {
		if (!array_key_exists($tableName, $this->tables)) {
			return false;
		}

		$table = $this->tables[$tableName];
		
		if (in_array($columnName, $table['columns'])) {
			return true;
		}

		return false;
	}

	protected function parseQueryTables($sql) {
		$foundTables=[];
		foreach ($this->tables as $tableName=>$table) {
			if (strpos($sql, $tableName) !== false) {
				$foundTables[$tableName] = [
					'tableName'=>$tableName,
					'columns'=>[],
					'alias'=>$this->findAlias($sql, $tableName)
				];
			}
		}
		return $foundTables;
	}

	protected function findAlias($sql, $tableName) {

		$startPos = strpos($sql,$tableName);
		$endPos = $startPos + strlen($tableName) + 1;

		if ($endPos > strlen($sql)) return false;

		$nextSpacePos = strpos($sql, ' ', $endPos);

		$alias=substr($sql, $endPos, ($nextSpacePos - $endPos));

		$cleanAlias = str_replace(',', '', $alias);

		return $cleanAlias;
	}
}
