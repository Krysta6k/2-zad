<?php

class UsergroupsController
{
	public function admin_index()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		$limit = (int) $_COOKIE['admin_items_per_page'] ?: ADMIN_ITEMS_PER_PAGE;
		$sorting = $_COOKIE['items_sorting'] ?: DEFAULT_SORTING;
		$offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;
		$where = [];
		$where['lang_code'] = $_COOKIE['lang'] ?: DEFAULT_LANG;

		return [
			'usersgroups' => Db::connect('users_groups')->where($where)->limit($limit)->offset($offset)->orderBy($sorting)->getAll(),
			'total' => count(Db::connect('users_groups')->select('id')->where($where)->getAll())
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

			$usergroup = Usergroups::add($_POST['data']);
			if ($usergroup['code'] != 201) 
			{
				Notify::createError($usergroup['message']);
				Router::redirect_back();
			}

			Notify::createSuccess($usergroup['message']);
			Router::redirect($redirect);
		}

		return [
			'rating' => (int) (count(Db::connect('users_groups')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->getAll()) + 1) * 10
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

			$usergroup = Usergroups::edit($_POST['data']);
			if ($usergroup['code'] != 200) 
			{
				Notify::createError($usergroup['message']);
				Router::redirect_back();
			}

			Notify::createSuccess($usergroup['message']);
			Router::redirect($redirect);
		}

		$usergroup = Db::connect('users_groups')->where(['id' => (int) $_GET['id']])->get();
		if ($usergroup == null) 
		{
			Notify::createError(Lang::get('messages', 'Not found'));
			Router::redirect_back();
		}

		return [
			'usergroup' => $usergroup
		];
	}

	public function delete()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_REQUEST['delete']) == false)
			echo Formatting::json(['type' => 'Error', 'message' => 'Delete param not found'], 400);

		if(!empty(Db::connect('users')->where(['usergroup' => $_REQUEST['id']])->getAll()))
			echo Formatting::json(['type' => 'Error', 'message' => 'Usergroup not empty'], 409);

		$response = Usergroups::delete($_REQUEST);
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

			$controller = Usergroups::edit($_POST['data']);
			echo Formatting::json(['message' => Lang::get('messages', $controller['message'])], $controller['code']);
		}

		die();
	}
}
