<?php
class Docs_categoriesController
{
	public function admin_index()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		$limit = (int) $_COOKIE['admin_items_per_page'] ?: ADMIN_ITEMS_PER_PAGE;
		$status = $_COOKIE['display_status'] == 1 ? [0, 1] : [1];
		$sorting = $_COOKIE['items_sorting'] ?: DEFAULT_SORTING;
		$offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;

		return [
			'docs_categories' => Db::connect('docs_categories')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->limit($limit)->offset($offset)->orderBy($sorting)->getAll(),
			'total' => count(Db::connect('docs_categories')->select('id')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->in('status', $status)->getAll())
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

			$docs_category = Docs_categories::add($_POST['data']);
			if ($docs_category['code'] != 201) 
			{
				Notify::createError($docs_category['message']);
				Router::redirect_back();
			}

			Notify::createSuccess($docs_category['message']);
			Router::redirect($redirect);
		}

		return [
			'langs' => Db::connect('langs')->getAll(),
			'docs_categories' => $categories = Db::connect('docs_categories')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->getAll(),
			'rating' => (int) (count($categories) + 1) * 10
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

			$docs_category = Docs_categories::edit($_POST['data']);
			if ($docs_category['code'] != 200) 
			{
				Notify::createError($docs_category['message']);
				Router::redirect_back();
			}

			Notify::createSuccess($docs_category['message']);
			Router::redirect($redirect);
		}

		$docs_category = Db::connect('docs_categories')->where(['id' => (int) $_GET['id']])->get();
		if ($docs_category == null) 
		{
			Notify::createError(Lang::get('messages', 'Not found'));
			Router::redirect_back();
		}

		return [
			'langs' => Db::connect('langs')->getAll(),
			'docs_categories' => Db::connect('docs_categories')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->notWhere(['id' => $docs_category->id])->getAll(),
			'docs_category' => $docs_category
		];
	}

	public function delete()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_REQUEST['delete']) == false)
			echo Formatting::json(['type' => 'Error', 'message' => 'Delete param not found'], 400);

		if(!empty(Db::connect('docs')->where(['category_id' => $_REQUEST['id']])->getAll()))
			echo Formatting::json(['type' => 'Error', 'message' => 'Category not empty'], 409);

		$response = Docs_categories::delete($_REQUEST);
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

			$controller = Docs_categories::edit($_POST['data']);
			echo Formatting::json(['message' => Lang::get('messages', $controller['message'])], $controller['code']);
		}

		die();
	}
}