<?php

require_once(__DIR__ . '/../../Controller/ApiController.php');
require_once(__DIR__ . '/../../Controller/Component/DBComponent.php');
App::uses('APIComponent', 'Controller/Component');
App::uses('AppShell', 'Console/Command');
App::uses('Model', 'Model');

class CleanTemporaryTablesShell extends AppShell {

	private $console = true;

	private function setEnvironment() {
		$htaccess = file_get_contents(__DIR__ . '/../../../.htaccess');
		$pattern = '/.*SetEnv server_location "(.*)"/';
		if (preg_match_all($pattern, $htaccess, $matches)) {
			putenv('server_location=' . $matches[1][0]);
			$_SERVER['server_location'] = $matches[1][0];
		}
	}

	private function output($text) {
		$this->out($text);
		if (!$this->console) {
			echo $text . "\n";
		}
	}

	public function main($console = true) {
		$this->console = $console;
		$this->setEnvironment();
		$this->output("Dropping temporary tables");
		$oDb = DBComponent::getInstance('sessions', 'swarmdata');
		$sSQL = "SHOW TABLES FROM sessions LIKE 'sessions\_%\_%'";
		$aRes = $oDb->fetchAll($sSQL);
		foreach ($aRes as $oRow) {
			$table = array_pop($oRow['TABLE_NAMES']);
			$oDb->query("DROP TABLE IF EXISTS sessions.$table");
		}
		$this->output("Done!");
	}

}
