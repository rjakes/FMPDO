<?php
namespace RjakesTest\FmPdo;

use PHPUnit_Framework_TestCase;
use Rjakes\FmPdo\Error;
use Rjakes\FmPdo\FmPdo;
use stdClass;

class FmPdoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var FmPdo
     */
    private $fmPdo;

    public function setUp()
    {

        $dbConfig = array(
            'dsn'      => 'sqlite::memory:',
            'username' => null,
            'password' => null,
        );
        $this->fmPdo = new FmPdo($dbConfig);
    }

    public function testVersion()
    {
        $this->assertRegexp('/[0-9]+\.[0-9]+\.[0-9]+/', $this->fmPdo->getAPIVersion());
    }


    public function testIsErrorFalse()
    {
        $this->assertFalse($this->fmPdo->isError(new stdClass()));
    }

    public function testIsErrorTrue()
    {
        $this->assertTrue($this->fmPdo->isError(new Error()));
    }

    public function testGetProperty()
    {
        $this->fmPdo->test1 = 'TEST';
        $this->assertEquals('TEST', $this->fmPdo->getProperty('test1'));
        $this->assertNull($this->fmPdo->getProperty('test2'));
    }

    public function testGetConnection()
    {
        $this->assertInstanceOf('\PDO', $this->fmPdo->getConnection());
        $this->assertInstanceOf('\Rjakes\FmPdo\Connect', $this->fmPdo->getConnection());
    }


    public function testErrorGetRecordByIDError()
    {
        $pdoStub = $this->getMockBuilder('\RjakesTest\FmPdo\MockPdo')
            ->getMock();

        $pdoStub->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue(null));

        $this->fmPdo->setConnection($pdoStub);
        $this->assertInstanceOf('\Rjakes\FmPdo\Error', $this->fmPdo->getRecordByID('error', 1));
    }

    public function testGetRecordByID()
    {
        //Query Stubbing
        $queryStub = $this->getMock('MockPdoStatement', array('execute', 'fetch'));

        $queryStub->expects($this->once())
            ->method('execute')
            ->will($this->returnSelf());

        $queryStub->expects($this->once())
            ->method('fetch')
            ->will($this->returnValue(array('id' => 1, 'username' => 'baptiste')));

        //PDO stubbing
        $pdoStub = $this->getMock('\RjakesTest\FmPdo\MockPdo', array('prepare'));

        $pdoStub->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo("SELECT * FROM users WHERE id='1' LIMIT 1;"))
            ->will($this->returnValue($queryStub));

        $this->fmPdo->setConnection($pdoStub);

        $this->assertInstanceOf('\Rjakes\FmPdo\Record', $this->fmPdo->getRecordByID('users', 1));
    }
}
