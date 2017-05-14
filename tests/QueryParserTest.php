<?php

include_once(dirname(__FILE__)."/StinkyCase.php");

class QueryParserTest extends StinkyCase {

	public function testPHPUnit() {
		$this->assertFalse(
			false,
			"Yeah, things are really happening"
		);
	}

	/**
	* @expectedException \Exception
	*/
	public function testLoadQueryCheckerBroken() {
		$notArray = "string of json or something";
		$queryParser = new \DanielJHarvey\QueryStinkers\QueryParser($notArray);
	}

	public function testLoadQueryChecker() {
		$tables = $this->getData('tables');

		$queryParser = new \DanielJHarvey\QueryStinkers\QueryParser($tables);

		$this->assertInstanceOf(
			"\DanielJHarvey\QueryStinkers\QueryParser",
			$queryParser,
			"Did not create class properly"
		);
	}

	public function parseQueryColumnsData() {
		return [
			[
				'SELECT * FROM totallyFine',
				[
					'totallyFine'=>[
						'tableName'=>'totallyFine',
						'columns'=>[],
						'alias'=>false
					]
				]
			],
			[
				'SELECT * FROM colourMaps WHERE thingID=100',
				[
					'colourMaps'=>[
						'tableName'=>'colourMaps',
						'columns'=>['thingID'],
						'alias'=>false
					]
				]
			],
			[
				'SELECT * FROM colourMaps WHERE colourMapID=56',
				[
					'colourMaps'=>[
						'tableName'=>'colourMaps',
						'columns'=>['colourMapID'],
						'alias'=>false
					]
				]
			],
			[
				'SELECT * FROM colourMaps cm, totallyFine a WHERE a.thingID=cm.thingID AND cm.colourMapID=56',
				[
					'colourMaps'=>[
						'tableName'=>'colourMaps',
						'columns'=>['thingID','colourMapID'],
						'alias'=>'cm'
					],

					'totallyFine'=>[
						'tableName'=>'totallyFine',
						'columns'=>['thingID'],
						'alias'=>'a'
					]
				]
			]
		];
	}

	/**
	* @dataProvider parseQueryColumnsData
	*/
	public function testParseQueryColumns($sql, $expected) {
		$tables = $this->getData('tables');

		$queryParser = new \DanielJHarvey\QueryStinkers\QueryParser($tables);

		$columns=$queryParser->parseQuery($sql);

		$this->assertEquals(
			$expected,
			$columns,
			"Could not correctly parse out columns in query"
		);
	}
}

