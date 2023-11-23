<?php
class Log
{
	private function __construct() { /* ... @return ClassName */ }  // We protect from creation through "new ClassName"
	private function __clone() { /* ... @return ClassName */ }  // We protect from creation through "cloning"
	private function __wakeup() { /* ... @return ClassName */ }  // We protect from creation through "unserialize"

	public static function set(int $user_id, string $message = null, string $controller = null, string $action = null, int $target_id = null, array $extra = null)
	{
		Db::connect('logs')->insert([
			'date_create' => date('Y-m-d H:i:s'),
			'user_id' => $user_id ?: $_SESSION['user_id'],
			'message' => strip_tags($message),
			'controller' => strtolower($controller),
			'action' => strtolower($action),
			'target_id' => (int) $target_id,
			'extra' => $extra ? json_encode($extra) : null,
			'user_ip' => $_SERVER['REMOTE_ADDR'],
		]);
	}
	public static function get($params = null, $limit = null)
	{
		$logs = [];
		$items_per_page = !empty($limit) ? $limit : DEFAULT_ITEMS_PER_PAGE;

		if (!empty($params['date_from'])) :
			if (!empty($params['controller'])) :
				$log_items = Db::connect('logs')->where(['controller' => $params['controller']])->between('date_create', $params['date_from'], ($params['date_to'] ?: date('Y-m-d H:i:s')))->orderBy('date_create DESC')->limit($items_per_page)->getAll();
			else :
				$log_items = Db::connect('logs')->between('date_create', $params['date_from'], ($params['date_to'] ?: date('Y-m-d H:i:s')))->orderBy('date_create DESC')->limit($items_per_page)->getAll();
			endif;
		else :
			if (!empty($params['controller'])) :
				$log_items = Db::connect('logs')->where(['controller' => $params['controller']])->between('date_create', $params['date_from'], ($params['date_to'] ?: date('Y-m-d H:i:s')))->orderBy('date_create DESC')->limit($items_per_page)->getAll();
			else :
				$log_items = Db::connect('logs')->orderBy('date_create DESC')->limit($items_per_page)->getAll();
			endif;
		endif;

		foreach ($log_items as $log) {
			$user = Db::connect('users')->select('id, username, surname, name')->where(['id' => (int) $log->user_id])->get();

			array_push($logs, [
				'id' => (int) $log->id,
				'user' => [
					'username' => base64_decode($user->username),
					'fullname' => $user->surname . ' ' . $user->name,
					'shortname' => $user->surname . ' ' . mb_substr($user->name, 0, 1, 'UTF-8') . '.'
				],
				'date' => Formatting::date($log->date_create, 'd.m.y H:i:s'),
				'message' => Lang::get('logs', strip_tags($log->message)),
			]);
		}

		return $logs;
	}

	public static function critical(string $message = null, string $controller = 'app')
	{
		Db::connect('logs_critical')->insert([
			'date_create' => date('Y-m-d H:i:s'),
			'user_id' => (int) $_SESSION['user_id'],
			'message' => strip_tags($message),
			'controller' => strtolower($controller),
		]);
	}
}
