<?php

namespace DanielJHarvey\QueryStinkers;

class QueryChecker {
	
	protected $tables;

	public function __construct($tables, \DanielJHarvey\QueryStinkers\QueryParser $queryParser) {
		$this->tables = $tables;
		$this->queryParser = $queryParser;
	}

	public function checkQuery($sql) {
		$parsedQuery = $this->queryParser->parseQuery($sql);
		$warning=false;
		foreach ($parsedQuery as $queryTable) {
			$isWarning = $this->checkProblemTable($queryTable);
			if ($isWarning) return true;
		}
		return $warning;
	}

	protected function checkProblemTable($queryTable) {
		$tableName = $queryTable['tableName'];

		$table = $this->getTable($tableName);
		if (!$table) return false;
		
		if ($table['large']==false) return false;

		$usesIndexes = $this->usesIndexes($table['indexes'],$queryTable['columns']);

		if (!$usesIndexes) return true;

		return false;
	}

	protected function getTable($tableName) {
		if (!array_key_exists($tableName, $this->tables)) {
			return false;
		}

		return $this->tables[$tableName];
	}

	protected function usesIndexes($indexes,$columns) {
		$found = false;
		foreach ($indexes as $index) {
			$thisFound=true;
			foreach ($index['columns'] as $indexColumn) {
				if (!in_array($indexColumn, $columns)) {
					$thisFound = false;
				}
			}
			if ($thisFound) $found=true;
		}
		return $found;
	}
}