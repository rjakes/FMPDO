<?php
class MockPdo extends Pdo {
	public function __construct() {}
}


class MockPdoStatement extends PdoStatement {
	public function __construct() {}
}
