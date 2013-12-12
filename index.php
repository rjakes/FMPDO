<?php

require_once(__DIR__ . '/Bootstrap.php');

if (!defined('PDO::ATTR_DRIVER_NAME')) {
    die ('<span style="color: red">PDO unavailable</span>');
}else{
    echo '<span style="color: blue">Installed PDO Drivers:</span><br>';
    var_dump(PDO::getAvailableDrivers());
}
echo '<br><br>';


$id = '3';
$property = "locale";

$db_config = array(
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'port' => '3306',
    'database' => 'fmpdo',
    'user' => 'root',
    'password' => 'root'
);


try
{
    $fmpdo = new Fmpdo($db_config);
}
catch (Exception $e)
{
    print_r('<pre style="color:red;">Failed to connect to database:<br></pre>');
    print("<pre>Here are the settings that you are trying to use:<br></pre>");
    var_dump($db_config);
    print("<pre>Here is the exception from PDO:<br></pre>");
    echo "<pre>".$e."</pre>";
    exit;
}


echo '<span style="color: blue">Test FMPDO->__contruct():</span><br>';
var_dump($fmpdo);
echo '<br><br>';

echo '<span style="color: blue">Test FMPDO->getAPIVersion():</span><br>';
var_dump($fmpdo->getAPIVersion());
echo '<br><br>';

echo '<span style="color: blue">Test FMPDO->getProperty() (where property = '.$property.'):</span><br>';
var_dump($fmpdo->getProperty($property));
echo '<br><br>';

echo '<span style="color: blue">Test FMPDO->isError() (where we expect an error object):</span><br>';
$error = new Error("test error class");
$test_result = FMPDO::isError($error) ? "Test passed" : "Test failed";
var_dump($test_result);

echo '<span style="color: blue">Test FMPDO->isError() (where we don\'t expect an error object):</span><br>';
$record = new Record('category');
$test_result = ! FMPDO::isError($record) ? "Test passed" : "Test failed";
var_dump($test_result);
echo '<br><br>';

echo '<span style="color: blue">Test FMPDO->createRecord() (for category table):</span><br>';
$createdRecord = $fmpdo->createRecord('category');
var_dump($createdRecord);
echo '<br><br>';


echo '<span style="color: blue">Test FMPDO->getRecordByID() (where id = '.$id.'):</span><br>';
$record = $fmpdo->getRecordByID('category', $id);
var_dump($record);
echo '<br><br>';


echo '<span style="color: blue">Test FMPDO->newFindCommand() (for category table):</span><br>';
$findCommand = $fmpdo->newFindCommand('category');
var_dump($findCommand);
echo '<br><br>';

echo '<span style="color: blue">Test Find->addFindCriterion() (where find criterion is type=city ):</span><br>';
$findCommand->addFindCriterion("type", "city");
var_dump($findCommand);
echo '<br><br>';

echo '<span style="color: blue">Test Find->addSortRule() (mixed sort rules out of order):</span><br>';
$findCommand->addSortRule("name", "1", "descend");
$findCommand->addSortRule("date_created", "0", "ascend");
var_dump($findCommand);
echo '<br><br>';

echo '<span style="color: blue">Test Find->setLimit() (limit = 2):</span><br>';
$findCommand->setLimit("2");
var_dump($findCommand);
echo '<br><br>';

echo '<span style="color: blue">Test Find->execute() (for above find object):</span><br>';
$result = $findCommand->execute();
var_dump($result);
echo '<br><br>';

echo '<span style="color: blue">Test FMPDO->newFindAllCommand() (for category table):</span><br>';
$findCommand = $fmpdo->newFindAllCommand('category');
var_dump($findCommand);
echo '<br><br>';

echo '<span style="color: blue">Test Find(All)->execute() (for category table):</span><br>';
$result = $findCommand->execute();
var_dump($findCommand);
echo '<br><br>';

echo '<span style="color: blue">Test Result->getRecords():</span><br>';
var_dump($result->getRecords());
echo '<br><br>';

echo '<span style="color: blue">Test Result->getFirstRecord():</span><br>';
var_dump($result->getFirstRecord());

echo '<span style="color: blue">Test Record->commit() (existing record):</span><br>';
$record = $result->getFirstRecord();
$record->setField("type", "town");
$record->commit();
echo '<br><br>';

echo '<span style="color: blue">Test Record->commit() (new record):</span><br>';
$record = $fmpdo->createRecord('category');
$record->setField("type", "town");
$record->setField("name", "Wolcott");
$record->commit();
echo '<pre>New recordid = '.$record->getRecordId().'</pre><br><br>';

echo '<span style="color: blue">Test FMPDO->newEditCommand():</span><br>';
$editCommand = $fmpdo->newEditCommand('category', '1');
var_dump($editCommand);
echo '<br><br>';

echo '<span style="color: blue">Test Edit->setField():</span><br>';
$editCommand->setField('type', 'foobar');
var_dump($editCommand);
echo '<br><br>';


$editCommand = $fmpdo->newEditCommand('category', '1');
$editCommand->setField('type', 'city');
$editCommand->execute();
echo '<span style="color: blue">Test Edit->execute():</span><br>';
$result = $editCommand->execute();
echo "<pre>" . ($result==TRUE ? "Edit Succeeded" : "Edit Failed") . "</pre>";

echo '<br><br>';



echo '<span style="color: blue">Test Record->getField() (where field = type):</span><br>';
    var_dump($record->getField('type'));
echo '<br><br>';

echo '<span style="color: blue">Test Record->getRecordId() (for record queried above):</span><br>';
var_dump($record->getRecordId());
echo '<br><br>';


echo '<span style="color: blue">Test Error->__construct() :</span><br>';
$error = new Error("test error message", "-1");
var_dump($error);
echo '<br><br>';





