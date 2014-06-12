<?php
/*
* Test Suite
*
*/

error_reporting(E_ALL);
ini_set("display_errors", 1); 
chdir(dirname(__FILE__));

$moduleFolder = getcwd()."/../../module";
echo "<pre>";
	// Project Unit Tests
	echo "<h1>Project Unit Tests</h1>";
	$command = "phpunit -c ".$moduleFolder."/Application/tests/phpunit.xml";
	system($command);

	// Module Unit Tests
	echo "<h1>Module Unit Tests</h1>";
	$module = dir($moduleFolder);
	while (false !== ($entry = $module->read())) {
		if((strncmp($entry, ".", 1)) && ($entry != "Application")) {
			$phpunit = $moduleFolder."/".$entry."/tests/phpunit.xml";
			if(file_exists($phpunit)) {
				$command = "cd ".$moduleFolder."/".$entry."/tests; phpunit";
				echo "<h4>$entry</h4>";
				system($command);
			}
		}
	}
	$module->close();
echo "</pre>";
