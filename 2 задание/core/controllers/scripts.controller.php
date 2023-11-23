<?php
class ScriptsController
{
	public function admin_index ()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		$limit = (int) $_COOKIE['admin_items_per_page'] ?: ADMIN_ITEMS_PER_PAGE;
		$sorting = $_COOKIE['items_sorting'] ?: DEFAULT_SORTING;
		$offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;

		$where = [];
		$where['lang_code'] = $_COOKIE['lang'] ?: DEFAULT_LANG;
		$_REQUEST['position'] ? $where['position'] = $_REQUEST['position'] : null;

		return [
			'scripts' => Db::connect('scripts')->where($where)->limit($limit)->offset($offset)->orderBy($sorting)->getAll(),
			'total' => count(Db::connect('scripts')->select('id')->where($where)->getAll())
		];
	}

	public function admin_add ()
	{
		App::check_auth($redirect = $_POST['redirect'] ? base64_decode($_POST['redirect']) : CI);
		App::check_permission($GLOBALS['router']->getController());

		if(isset($_POST['add']))
		{
			if ($_POST['data'] == null) 
			{
				Notify::createError(Lang::get('messages', 'Data not sending'));
				Router::redirect_back();
			}

			$script = Scripts::add($_POST['data']);
			if ($script['code'] != 201) 
			{
				Notify::createError($script['message']);
				Router::redirect_back();
			}

			Notify::createSuccess($script['message']);
			Router::redirect($redirect);
		}

		return [
			'rating' => (int) (count(Db::connect('scripts')->select('id')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->getAll()) + 1) * 10
		];
	}

	public function admin_edit ()
	{
		App::check_auth($redirect = $_POST['redirect'] ? base64_decode($_POST['redirect']) : CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_POST['edit'])) 
		{
			if ($_POST['data'] == null) 
			{
				Notify::createError(Lang::get('messages', 'Data not sending'));
				Router::redirect_back();
			}

			$script = Scripts::edit($_POST['data']);
			if ($script['code'] != 200) 
			{
				Notify::createError($script['message']);
				Router::redirect_back();
			}

			Notify::createSuccess($script['message']);
			Router::redirect($redirect);
		}

		$script = Db::connect('scripts')->where(['id' => (int) $_GET['id']])->get();
		if ($script == null) 
		{
			Notify::createError(Lang::get('messages', 'Not found'));
			Router::redirect_back();
		}

		return [
			'script' => $script
		];
	}

	public function delete()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_REQUEST['delete']) == false)
			echo Formatting::json(['type' => 'Error', 'message' => 'Delete param not found'], 400);

		$response = Scripts::delete($_REQUEST);
		echo Formatting::json($response['data'], $response['code']);
	}

	public function api_edit()
	{
		App::check_auth($redirect = $_POST['redirect'] ? base64_decode($_POST['redirect']) : CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_POST['edit'])) 
		{
			if ($_POST['data'] == null) 
				echo Formatting::json(['message' => Lang::get('messages', 'Data not sending')], 400);

			$controller = Scripts::edit($_POST['data']);
			echo Formatting::json(['message' => Lang::get('messages', $controller['message'])], $controller['code']);
		}

		die();
	}
}