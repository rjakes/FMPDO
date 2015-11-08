<?php

require_once 'Bootstrap.php';
use \PDO;


require_once 'PdoStub.php';

class FmPdoTest extends PHPUnit_Framework_TestCase
{
	
	public function setUp(){

		$dbConfig = array(
				'dsn' => 'sqlite::memory:',
				'username' => null,
				'password' => null,
		);
		$this->fmPdo = new FmPdo($dbConfig);
	}
	
	public function testVersion(){
		$this->assertRegexp('/[0-9]+\.[0-9]+\.[0-9]+/',$this->fmPdo->getAPIVersion());
	}
	
	
	public function testIsErrorFalse(){
		$this->assertFalse($this->fmPdo->isError(new stdClass()));
	}
	
	public function testIsErrorTrue(){
		$this->assertTrue($this->fmPdo->isError(new Error()));
	}
	
	public function testGetProperty(){
		$this->fmPdo->test1 = 'TEST';
		$this->assertEquals('TEST',$this->fmPdo->getProperty('test1'));
		$this->assertNull($this->fmPdo->getProperty('test2'));
	}
	
	public function testGetConnection(){
		$this->assertTrue(is_a($this->fmPdo->getConnection(),'Pdo'));
		$this->assertTrue(is_a($this->fmPdo->getConnection(),'Connect'));
	}
	
	
	public function testErrorGetRecordByIDError(){
		$pdoStub = $this->getMockBuilder('MockPdo')
                     ->getMock();
		
		$pdoStub->expects($this->any())
		->method('prepare')
		->will($this->returnValue(null));
		
		$this->fmPdo->setConnection($pdoStub);
		$this->assertTrue(is_a($this->fmPdo->getRecordByID('error', 1),'Error'));
	}
	
	public function testGetRecordByID(){
		//Query Stubbing
		$queryStub = $this->getMock('MockPdoStatement',array('execute','fetch'));
		
		$queryStub->expects($this->once())
		->method('execute')
		->will($this->returnSelf());
		
		$queryStub->expects($this->once())
		->method('fetch')
		->will($this->returnValue(array('id' => 1,'username' => 'baptiste')));
		
		//PDO stubbing
		$pdoStub = $this->getMock('MockPdo',array('prepare'));
		
		$pdoStub->expects($this->once())
		->method('prepare')
		->with($this->equalTo("SELECT * FROM users WHERE id='1' LIMIT 1;"))
		->will($this->returnValue($queryStub));
	
		$this->fmPdo->setConnection($pdoStub);
		
		$this->assertTrue(is_a($this->fmPdo->getRecordByID('users', 1),'Record'));
	}
	
}