<?php
class LogsController
{
	public function admin_index ()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		$limit = (int) $_COOKIE['admin_items_per_page'] ?: ADMIN_ITEMS_PER_PAGE;
		$sorting = $_COOKIE['items_sorting'] ?: 'id DESC';
		$offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;
		$where = [];
		$_REQUEST['controller'] ? $where['controller'] = $_REQUEST['controller'] : null;
		$_REQUEST['user'] ? $where['user_id'] = $_REQUEST['user'] : null;

		return [
			'logs' => Db::connect('logs')->where($where)->limit($limit)->offset($offset)->orderBy($sorting)->getAll(),
			'users' => Db::connect('users')->select('id, username, surname, name')->getAll(),
			'controllers' => Db::connect('controllers')->where(['lang_code' => $_COOKIE['lang'] ? : DEFAULT_LANG])->getAll(),
			'total' => count(Db::connect('logs')->select('id')->where($where)->getAll())
		];
	}
}
