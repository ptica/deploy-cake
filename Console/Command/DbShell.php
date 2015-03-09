<?php

Class DbShell extends Shell {

	public function main() {
		if (empty($this->args)) {
			$this->help();
			return;
		}

		$this->create();
	}

	/**
	* help
	*
	*/
	public function help() {
		$this->hr();
		$this->out('Usage: cake Deploy.db create <db_name> [-c <db_config>] [-u <new_username>]');
		$this->hr();
		$this->out('Parameters:');
		$this->out('	<db_name>');
		$this->out('		(eg. project_db)');
		$this->out();
		$this->out('	<db_config>');
		$this->out('		database configuration (default: "default")');
		$this->out();
		$this->hr();
	}

	public function create() {
		$db_name  = $this->args[0];

		$db_config = 'default';
		if (!empty($this->params['c'])) {
			$db_config = $this->params['c'];
		}

		$sql = "CREATE DATABASE `$db_name` COLLATE 'utf8_czech_ci'; CREATE USER `{$db_name}`@localhost IDENTIFIED BY '{$db_name}_pass'; GRANT ALL PRIVILEGES ON `$db_name`.* TO `$db_name`@localhost IDENTIFIED BY '{$db_name}_pass'; FLUSH PRIVILEGES";
		echo $sql;

		try {
			$dblink = new PDO('mysql:host=localhost;', 'root',  '', array(PDO::ATTR_PERSISTENT => false));
			$dblink->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$dblink->exec($sql);
		} catch (Exception $e) {
			die('DB Error'. $e->getMessage());
		}

		#App::uses('ConnectionManager', 'Model');
		#$db = ConnectionManager::getDataSource($db_config);

		#if ($db->isConnected()) {
		#	$db->query("CREATE DATABASE `$db_name` COLLATE 'utf8_czech_ci'; GRANT ALL PRIVILEGES ON `$db_name`.* TO ``@localhost IDENTIFIED BY ''");
		#} else {
		#	echo "CREATE DATABASE `$db_name` COLLATE 'utf8_czech_ci'; GRANT ALL PRIVILEGES ON `$db_name`.* TO `$db_name`@localhost IDENTIFIED BY '{$db_name}_pass'";
		#}
	}
}
