<?php

include_once("../vendor/autoload.php");
include_once("../src/QueryChecker.php");
include_once("../src/QueryParser.php");
include_once("../src/TableStructure.php");

use PHPUnit\Framework\TestCase;

class StinkyCase extends TestCase {

	protected $dataDir;

	function getData($fileName) {
		$this->getDataDir();
		$json=file_get_contents($this->dataDir.$fileName.'.json');
		return json_decode($json,true);
	}

	protected function getDataDir() {
		$this->dataDir=dirname(__FILE__).'/data/';
	}

}
	
