<?php

// checks a MySQL query for use of indexes
// requires either a tables array (created by this object)
// or a PDO connection to allow it to build an array of indexes etc
// for best performance, use $stinkers->getTables() first time and cache it
// and repass each time

namespace DanielJHarvey\QueryStinkers;

class Stinkers {

	protected $dbName;
	protected $pdo;
	protected $tables;

	public function __construct($dbName, $tables=false, \PDO $pdo=NULL) {
		$this->dbName = $dbName;
		$this->pdo = $pdo;
		if ($tables) {
			$this->tables = $tables;
		}
	}

	public function getTables() {
		if ($this->tables) return $this->tables;
		$tableStructure = $this->createTableStructure($this->pdo, $this->dbName);
		$tables = $tableStructure->getTableStructure();
		$this->tables = $tables;
		return $tables;
	}

	public function checkQuery($sql) {
		$tables = $this->getTables();
		$queryChecker = $this->createQueryChecker($tables);
		$warning = $queryChecker->checkQuery($sql);
		if (!$warning) false;
		return $this->returnStackTrace();
	}

	protected function createQueryChecker($tables) {
		$queryParser = $this->createQueryParser($tables);
		$queryChecker = new \DanielJHarvey\QueryStinkers\QueryChecker($tables, $queryParser);
		return $queryChecker;
	}

	protected function createQueryParser($tables) {
		$queryParser = new \DanielJHarvey\QueryStinkers\QueryParser($tables);
		return $queryParser;
	}

	protected function createTableStructure(\PDO $pdo, $dbName) {
		$tableStructure = new \DanielJHarvey\QueryStinkers\TableStructure($pdo, $dbName);

		return $tableStructure;
	}

	// return stack trace where there is a problematic
	protected function returnStackTrace() {
		try {
			throw new \Exception();
		} catch (\Exception $e) {
			return $e->getTrace();
		}
	}
}
	