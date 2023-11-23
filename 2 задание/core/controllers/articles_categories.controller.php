<?php
class Articles_categoriesController
{
  public function index ()
  {

  }

	public function admin_index ()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		$limit = (int) $_COOKIE['admin_items_per_page'] ?: ADMIN_ITEMS_PER_PAGE;
		$sorting = $_COOKIE['items_sorting'] ?: DEFAULT_SORTING;
		$offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;

		$where = [];
		$where['lang_code'] = $_COOKIE['lang'] ? : DEFAULT_LANG;

		return [
			'articles_categories' => Db::connect('articles_categories')->where($where)->limit($limit)->offset($offset)->orderBy($sorting)->getAll(),
			'total' => count(Db::connect('articles_categories')->select('id')->where($where)->getAll())
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

			$article_category = Articles_categories::add($_POST['data']);
			if ($article_category['code'] != 201) 
			{
				Notify::createError($article_category['message']);
				Router::redirect_back();
			}

			Notify::createSuccess($article_category['message']);
			Router::redirect($redirect);
		}

		return [
			'langs' => Db::connect('langs')->getAll(),
			'rating' => (int) (count(Db::connect('articles_categories')->select('id')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->getAll()) + 1) * 10
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

			$article_category = Articles_categories::edit($_POST['data']);
			if ($article_category['code'] != 200) 
			{
				Notify::createError($article_category['message']);
				Router::redirect_back();
			}

			Notify::createSuccess($article_category['message']);
			Router::redirect($redirect);
		}

		$article_category = Db::connect('articles_categories')->where(['id' => (int) $_GET['id']])->get();
		if ($article_category == null) 
		{
			Notify::createError(Lang::get('messages', 'Not found'));
			Router::redirect_back();
		}

		return [
			'langs' => Db::connect('langs')->getAll(),
			'article_category' => $article_category
		];
	}

	public function delete()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_REQUEST['delete']) == false)
			echo Formatting::json(['type' => 'Error', 'message' => 'Delete param not found'], 400);

		$response = Articles_categories::delete($_REQUEST);
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

			$article_category = Articles_categories::edit($_POST['data']);
			echo Formatting::json(['message' => Lang::get('messages', $article_category['message'])], $article_category['code']);
		}

		die();
	}
}
