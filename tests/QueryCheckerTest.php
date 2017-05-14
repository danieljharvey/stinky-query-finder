<?php

include_once(dirname(__FILE__)."/StinkyCase.php");

class QueryCheckerTest extends StinkyCase {

	public function testCheckQueryData() {
		return [
			[
				[ // colourMaps is not large so always fine
					'colourMaps'=>[
						'tableName'=>'colourMaps',
						'columns'=>[],
						'alias'=>false
					]
				],
				false
			],
			[ // totallyFine is large so not always fine
				[
					'totallyFine'=>[
						'tableName'=>'totallyFine',
						'columns'=>[],
						'alias'=>false
					]
				],
				true
			],
			[
				[	
					'totallyFine'=>[
						'tableName'=>'totallyFine',
						'columns'=>['thingID','newLessonID'],
						'alias'=>false
					]
				],
				true
			],
			[
				[	
					'totallyFine'=>[
						'tableName'=>'totallyFine',
						'columns'=>['thingID','newLessonID','theDate'],
						'alias'=>false
					]
				],
				false
			],
			[
				[	
					'totallyFine'=>[
						'tableName'=>'totallyFine',
						'columns'=>['thingID','newLessonID','theDate'],
						'alias'=>'am'
					],
					'colourMaps'=>[
						'tableName'=>'colourMaps',
						'columns'=>[],
						'alias'=>'cm'
					]
				],
				false
			],
			[
				[	// this one is large and right
					'totallyFine'=>[
						'tableName'=>'totallyFine',
						'columns'=>['thingID','newLessonID','theDate'],
						'alias'=>'am'
					],
					'colourMaps'=>[ // this one is a mess but too small to care
						'tableName'=>'colourMaps',
						'columns'=>['sloppyChops'],
						'alias'=>'cm'
					]
				],
				false
			],
			[
				[	// pages is large thus matters
					'pages'=>[
						'tableName'=>'pages',
						'columns'=>['pageID'],
						'alias'=>false
					]
				],
				false
			]
		];
	}

	/**
	* @dataProvider testCheckQueryData
	*/
	public function testCheckQuery($table, $expected) {
		$tables = $this->getData('tables');

		$queryParser = $this->getMockBuilder('\DanielJHarvey\QueryStinkers\QueryParser')
			->setConstructorArgs([$tables])
			->getMock();

		$queryParser->expects($this->once())
			->method('parseQuery')
			->will($this->returnValue($table));

		$queryChecker = new \DanielJHarvey\QueryStinkers\QueryChecker($tables, $queryParser);

		$warning = $queryChecker->checkQuery("made up SQL that will be ignored");

		$this->assertEquals(
			$expected,
			$warning,
			"Could not correctly identify stinky query"
		);
	}

}	