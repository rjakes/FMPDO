<?php

require_once('config.php');

if (!defined('PDO::ATTR_DRIVER_NAME')) {
    die ('<span style="color: red">PDO unavailable</span>');
}else{
    echo '<span style="color: blue">Installed PDO Drivers:</span><br>';
    var_dump(PDO::getAvailableDrivers());
}
echo '<br><br>';

include(__DIR__ . '/FMPDO.php');

$id = '3';
$property = "locale";

$fmpdo = new FMPDO($sql_config);
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
$error = new FMPDO_Error("test error class");
$test_result = FMPDO::isError($error) ? "Test passed" : "Test failed";
var_dump($test_result);

echo '<span style="color: blue">Test FMPDO->isError() (where we don\'t expect an error object):</span><br>';
$record = new FMPDO_Record();
$test_result = FMPDO::isError($record) ? "Test passed" : "Test failed";
var_dump($test_result);
echo '<br><br>';

echo '<span style="color: blue">Test FMPDO->newFindCommand() (for category table):</span><br>';
$findCommand = $fmpdo->newFindCommand('category');
var_dump($findCommand);
echo '<br><br>';

echo '<span style="color: blue">Test FMPDO_Command_Find->addFindCriterion() (where find criterion is type=city ):</span><br>';
$findCommand->addFindCriterion("type", "city");
var_dump($findCommand->_findCriteria);
echo '<br><br>';

echo '<span style="color: blue">Test FMPDO_Command_Find->addSortRule() (mixed sort rules out of order):</span><br>';
$findCommand->addSortRule("name", "1", "descend");
$findCommand->addSortRule("date_created", "0", "ascend");
var_dump($findCommand->_sortRules);
echo '<br><br>';

echo '<span style="color: blue">Test FMPDO_Command_Find->execute() (for above find object):</span><br>';
$result = $findCommand->execute();
var_dump($result);
echo '<br><br>';

echo '<span style="color: blue">Test FMPDO_Result->getRecords():</span><br>';
var_dump($result->getRecords());
echo '<br><br>';

echo '<span style="color: blue">Test FMPDO_Result->getFirstRecord():</span><br>';
var_dump($result->getFirstRecord());



echo '<span style="color: blue">Test FMPDO->newEditCommand():</span><br>';
$editCommand = $fmpdo->newEditCommand('category', '1');
var_dump($editCommand);
echo '<br><br>';

echo '<span style="color: blue">Test FMPDO_Command_Edit->setField():</span><br>';
$editCommand->setField('type', 'foobar');
var_dump($editCommand);
echo '<br><br>';

echo '<span style="color: blue">Test FMPDO_Command_Edit->execute():</span><br>';
$result = $editCommand->execute();
echo "<pre>" . $result==TRUE ? "Edit Succeeded" : "Edit Failed" . "</pre>";
//$editCommand = $fmpdo->newEditCommand('category', '1');
//$editCommand->setField('type', 'city');
//$editCommand->execute();

echo '<br><br>';

echo '<span style="color: blue">Test FMPDO->getRecordByID() (where id = '.$id.'):</span><br>';
$record = $fmpdo->getRecordByID('category', $id);
var_dump($record);
echo '<br><br>';


echo '<span style="color: blue">Test FMPDO_Record->getField() (where field = type):</span><br>';
    var_dump($record->getField('type'));
echo '<br><br>';

echo '<span style="color: blue">Test FMPDO_Record->getRecordId() (for record queried above):</span><br>';
var_dump($record->getRecordId());
echo '<br><br>';





