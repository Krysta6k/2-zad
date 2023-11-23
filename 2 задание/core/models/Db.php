<?php
require_once(ROOT . '/vendor/autoload.php');

class Db extends App {
	private function __construct() { /* ... @return ClassName */ }  // We protect from creation through "new ClassName"
	private function __clone() { /* ... @return ClassName */ }  // We protect from creation through "cloning"
	private function __wakeup() { /* ... @return ClassName */ }  // We protect from creation through "unserialize"


	public static function connect (?string $table_name = null)
	{
		if($conf = Storage::json('check', CONF, 'db') == false)
			return false;

		$db = new Buki\Pdox(Storage::json('get', CONF, 'db'));

		if ($table_name != null)
			$db = $db->table($table_name);

		return $db;
	}
}
