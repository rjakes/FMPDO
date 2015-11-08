<?php
namespace RjakesTest\FmPdo;

use PDO;
use PHPUnit_Framework_TestCase;
use Rjakes\FmPdo\Connect;

class ConnectTest extends PHPUnit_Framework_TestCase
{
	public function testCreateOptions()
	{
		//we explicitely remove the options not compatible with sqlite.
		$dsn = 'sqlite::memory:';
		$db = new Connect($dsn, null, null);
		$this->assertEquals(true,$db->getAttribute(PDO::ATTR_ERRMODE));
		$this->assertEquals(PDO::FETCH_ASSOC,$db->getAttribute(PDO::ATTR_DEFAULT_FETCH_MODE));
	}
}