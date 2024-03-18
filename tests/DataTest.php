<?php

namespace Vanderbilt\REVERSE_LC;

// For now, the path to "redcap_connect.php" on your system must be hard coded.
require_once dirname(dirname(dirname(__DIR__))) . '/redcap_connect.php';
// require_once APP_PATH_DOCROOT . '/ExternalModules/tests/ModuleBaseTest.php';

final class DataTest extends \ExternalModules\ModuleBaseTest
{
    /** @var REVERSE_LC $module */
    public $module;

	public function setUp() : void {
		parent::setUp();
		
		$module = $this->module;
		$q = \ExternalModules\ExternalModules::getEnabledProjects($module->PREFIX);
		$edc_pid = @db_fetch_assoc($q)['project_id'];
		if (!empty($edc_pid)) {
		    $_GET['pid'] = $edc_pid;
		}
	}
	
	function testUADUsersHaveDAG() {
		$module = $this->module;

		$this->assertNotEmpty($_GET['pid'], "Can't run integration test without UAD project ID specified");
		unset($module->uad_data);
		$module->getUADData();

		$user_records = $module->uad_data;
		$this->assertNotCount(0,$user_records, "No user records found");

		unset($module->uad_data);
	}
	
	function testEDCPatientRecordsHaveDAG() {
		$module = $this->module;

		$this->assertNotEmpty($_GET['pid'], "Can't run integration test without EDC project ID specified");
        unset($module->edc_data);
		$records = $module->getEDCData();

		$this->assertNotCount(0,$records, "No patient records found");
		
		foreach ($records as $record) {
			$this->assertNotEmpty($record->redcap_data_access_group, "Found patient record with empty 'dag' field: record {$record->record_id}");
		}
		unset($module->edc_data);
	}
}