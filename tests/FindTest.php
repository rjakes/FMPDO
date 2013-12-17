<?php

require_once 'Bootstrap.php';
use \PDO;


require_once 'PdoStub.php';

class FindTest extends PHPUnit_Framework_TestCase
{
	public function setUp(){
	
		$dbConfig = array(
				'dsn' => 'sqlite::memory:',
				'username' => null,
				'password' => null,
		);
		$this->fmPdo = new FmPdo($dbConfig);
	}
	
	public function testFindConstruct(){
		
		//Query Stubbing
		$queryStub = $this->getMock('MockPdoStatement',array('execute','fetchAll'));
		
		$queryStub
		->expects($this->once())
		->method('execute')
		->will($this->returnSelf());
		
		$queryStub
		->expects($this->any())
		->method('fetchAll')
		->will($this->returnValue(array(array('id' => 1,'username' => 'baptiste'))));
		
		//PDO stubbing
		$pdoStub = $this->getMock('MockPdo',array('prepare'));
		
		$pdoStub->expects($this->once())
		->method('prepare')
		->with($this->equalTo("SELECT * FROM users WHERE username=:username  ;"))
		->will($this->returnValue($queryStub));
		
		$this->fmPdo->setConnection($pdoStub);
		
		$findC = new Find('users');
		$findC->addFindCriterion('username','baptiste');
		
		$resultSet = $findC->execute();
		$this->assertTrue(is_a($resultSet,'Result'));
		
		$record = $resultSet->getFirstRecord();
		$this->assertTrue(is_a($record,'Record'));
		
		$this->assertEquals('baptiste',$record->getField('username'));
		$this->assertEquals('1',$record->getField('id'));
	}
}