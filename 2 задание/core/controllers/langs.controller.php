<?php

class LangsController
{
	public function admin_index ()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		$limit = (int) $_COOKIE['admin_items_per_page'] ?: ADMIN_ITEMS_PER_PAGE;
		$sorting = $_COOKIE['items_sorting'] ?: DEFAULT_SORTING;
		$offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;

		return [
			'langs' => Db::connect('langs')->limit($limit)->offset($offset)->orderBy($sorting)->getAll(),
			'total' => count(Db::connect('langs')->select('id')->getAll())
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

			$lang = Lang::add($_POST['data']);
			if ($lang['code'] != 201) 
			{
				Notify::createError($lang['message']);
				Router::redirect_back();
			}

			Notify::createSuccess($lang['message']);
			Router::redirect($redirect);
		}

		return [
			'rating' => (int) (count(Db::connect('langs')->select('id')->getAll()) + 1) * 10
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

			$lang = Lang::edit($_POST['data']);
			if ($lang['code'] != 200) 
			{
				Notify::createError($lang['message']);
				Router::redirect_back();
			}

			Notify::createSuccess($lang['message']);
			Router::redirect($redirect);
		}

		$lang = Db::connect('langs')->where(['id' => (int) $_GET['id']])->get();
		if ($lang == null) 
		{
			Notify::createError(Lang::get('messages', 'Not found'));
			Router::redirect_back();
		}

		return [
			'lang' => $lang
		];
	}

	public function delete()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_REQUEST['delete']) == false)
			echo Formatting::json(['type' => 'Error', 'message' => 'Delete param not found'], 400);

		$response = Lang::delete($_REQUEST);
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

			$lang = Lang::edit($_POST['data']);
			echo Formatting::json(['message' => Lang::get('messages', $lang['message'])], $lang['code']);
		}

		die();
	}
}
