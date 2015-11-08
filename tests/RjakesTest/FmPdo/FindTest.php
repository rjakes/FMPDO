<?php
namespace RjakesTest\FmPdo;

use PHPUnit_Framework_TestCase;
use Rjakes\FmPdo\Find;
use Rjakes\FmPdo\FmPdo;
use Rjakes\FmPdo\Record;
use Rjakes\FmPdo\Result;

class FindTest extends PHPUnit_Framework_TestCase
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

    public function testFindConstruct()
    {

        //Query Stubbing
        $queryStub = $this->getMock('\RjakesTest\FmPdo\MockPdoStatement', array('execute', 'fetchAll'));

        $queryStub
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnSelf());

        $queryStub
            ->expects($this->any())
            ->method('fetchAll')
            ->will($this->returnValue(array(array('id' => 1, 'username' => 'baptiste'))));

        //PDO stubbing
        $pdoStub = $this->getMock('\RjakesTest\FmPdo\MockPdo', array('prepare'));

        $pdoStub->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo("SELECT * FROM users WHERE username=:username  ;"))
            ->will($this->returnValue($queryStub));

        $this->fmPdo->setConnection($pdoStub);

        $findC = new Find('users', $this->fmPdo);
        $findC->addFindCriterion('username', 'baptiste');

        $resultSet = $findC->execute();
        $this->assertInstanceOf('\Rjakes\FmPdo\Result', $resultSet);

        /** @var Record $record */
        $record = $resultSet->getFirstRecord();
        $this->assertInstanceOf('\Rjakes\FmPdo\Record', $record);

        $this->assertEquals('baptiste', $record->getField('username'));
        $this->assertEquals('1', $record->getField('id'));
    }
}
