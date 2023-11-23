<?php
class Notify
{
	public $title;
	public $type;
	public $message;
	public $time;

	private function __construct() { /* ... @return ClassName */ }  // We protect from creation through "new ClassName"
	private function __clone() { /* ... @return ClassName */ }  // We protect from creation through "cloning"
	private function __wakeup() { /* ... @return ClassName */ }  // We protect from creation through "unserialize"

	public static function getNotifies()
	{
		return $_SESSION['notify'];
	}

	public static function createSuccess(string $message, int $duration = 5)
	{
		$notify = [
			'title' => Lang::get('notifications', 'Success'),
			'type' => 'success',
			'text' => $message,
			'timer' => $duration
		];

		$_SESSION['notify'][] = $notify;

		return $notify;
	}

	public static function createError(string $message, int $duration = 5)
	{
		$notify = [
			'title' =>  Lang::get('notifications', 'Error'),
			'type' => 'error',
			'text' => $message,
			'timer' => $duration
		];

		$_SESSION['notify'][] = $notify;

		return $notify;
	}

	public static function createWarning(string $message = null, int $duration = 5)
	{
		$notify = [
			'title' =>  Lang::get('notifications', 'Warning'),
			'type' => 'warning',
			'text' => $message,
			'timer' => $duration
		];

		$_SESSION['notify'][] = $notify;

		return $notify;
	}
	public static function create($operation = null, $title = null, $type = null, $message = null, $time = null)
	{
		if ($operation != null) :
			switch ($operation):
				case 'save':
					$title = Lang::get('notifications', 'Notifications');
					$type = 'success';
					$message = Lang::get('messages', 'Changes saved successfully');
					break;
				case 'update':
					$title = Lang::get('notifications', 'Notifications');
					$type = 'success';
					$message = Lang::get('messages', 'Changes saved successfully');
					break;
				case 'delete':
					$title = Lang::get('notifications', 'Notifications');
					$type = 'success';
					$message = Lang::get('messages', 'Changes saved successfully');
					break;
				case 'notfound':
					$title = Lang::get('notifications', 'Notifications');
					$type = 'warning';
					$message = Lang::get('messages', 'Not found');
					break;
				case 'notempty':
					$title = Lang::get('notifications', 'Warning');
					$type = 'danger';
					$message = Lang::get('messages', 'The item you are trying to delete contains attachments, move or delete them, and then retry the operation.');
					break;
				case 'permission':
					$title = Lang::get('notifications', 'Warning');
					$type = 'danger';
					$message = Lang::get('messages', 'Permission denied');
					break;
			endswitch;
		endif;

		$_SESSION['notify'] = array();

		$_SESSION['notify'][] = [
			'title' => $title,
			'type' => $type,
			'text' => $message,
			'timer' => $time
		];

		return;
	}

	public static function clear()
	{
		$_SESSION['notify'] = [];
		return true;
	}
}
