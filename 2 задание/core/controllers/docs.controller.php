<?php
class DocsController
{
	public function admin_index()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		$limit = (int) $_COOKIE['admin_items_per_page'] ?: ADMIN_ITEMS_PER_PAGE;
		$status = $_COOKIE['display_status'] == 1 ? [0, 1] : [1];
		$sorting = $_COOKIE['items_sorting'] ?: DEFAULT_SORTING;
		$offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;

		$where = [];
		$where['lang_code'] = $_COOKIE['lang'] ?: DEFAULT_LANG;
		$_GET['category'] ? $where['category_id'] = Db::connect('docs_categories')->select('id, sef')->where(['sef' => strip_tags($_GET['category'])])->get()->id : '';

		return [
			'docs' => Db::connect('docs')->where($where)->in('status', $status)->limit($limit)->offset($offset)->orderBy($sorting)->getAll(),
			'docs_categories' => Db::connect('docs_categories')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->getAll(),
			'total' => count(Db::connect('docs')->select('id')->where($where)->in('status', $status)->getAll())
		];
	}

	public function admin_view()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		return [
			'doc' => $doc = Db::connect('docs')->where(['sef' => strip_tags($GLOBALS['router']->getSef()), 'lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->get(),
			'docs' => Db::connect('docs')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->orderBy('rating asc')->getAll(),
			'docs_categories' => Db::connect('docs_categories')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->getAll(),
		];
	}

	public function admin_add()
	{
		App::check_auth($redirect = $_REQUEST['redirect'] ? base64_decode($_REQUEST['redirect']) : CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_POST['add']))
		{
			if ($_POST['data'] == null) 
			{
				Notify::createError(Lang::get('messages', 'Data not sending'));
				Router::redirect_back();
			}

			$_POST['data']['content'] = json_encode($_POST['part'], JSON_HEX_QUOT | JSON_HEX_TAG);

			$doc = Docs::add($_POST['data']);
			if ($doc['code'] != 201) 
			{
				Notify::createError($doc['message']);
				Router::redirect_back();
			}

			Notify::createSuccess($doc['message']);
			Router::redirect($redirect);
		}

		return [
			'langs' => Db::connect('langs')->getAll(),
			'docs_categories' => Db::connect('docs_categories')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->getAll(),
		];
	}

	public function admin_edit()
	{
		App::check_auth($redirect = $_REQUEST['redirect'] ? base64_decode($_REQUEST['redirect']) : CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_POST['edit'])) 
		{
			if ($_POST['data'] == null) 
			{
				Notify::createError(Lang::get('messages', 'Data not sending'));
				Router::redirect_back();
			}

			$_POST['data']['content'] = json_encode($_POST['part'], JSON_HEX_QUOT | JSON_HEX_TAG);

			$doc = Docs::edit($_POST['data']);
			if ($doc['code'] != 200)
			{
				Notify::createError($doc['message']);
				Router::redirect_back();
			}

			Notify::createSuccess($doc['message']);
			Router::redirect($redirect);
		}

		if ($GLOBALS['router']->getSef() == null) 
		{
			Notify::createError(Lang::get('messages', 'ID not sending'));
			Router::redirect_back();
		}

		$item = Db::connect('docs')->where(['id' => (int) $GLOBALS['router']->getSef()])->get();
		if ($item == null) 
		{
			Notify::createError(Lang::get('messages', 'Not found'));
			Router::redirect_back();
		}

		return [
			'langs' => Db::connect('langs')->getAll(),
			'doc' => $item,
			'docs_categories' => Db::connect('docs_categories')->orderBy('title ASC')->getAll(),
		];
	}

	public function index()
	{
		$limit = $_COOKIE['items_per_page'] ?: DEFAULT_ITEMS_PER_PAGE;
		$status = $_COOKIE['display_status'] == 1 ? [0, 1] : [1];
		$sorting = $_COOKIE['items_sorting'] ?: DEFAULT_SORTING;
		$offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;

		$where = [];
		$where['lang_code'] = $_COOKIE['lang'] ?: DEFAULT_LANG;
		$_GET['category'] ? $where['category_id'] = Db::connect('docs_categories')->select('id, sef')->where(['sef' => strip_tags($_GET['category'])])->get()->id : '';

		return [
			'docs' => Db::connect('docs')->where($where)->in('status', $status)->limit($limit)->offset($offset)->orderBy($sorting)->getAll(),
			'docs_categories' => Db::connect('docs_categories')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->getAll(),
			'total' => count(Db::connect('docs')->select('id')->where($where)->in('status', $status)->getAll())
		];
	}

	public function view()
	{

		if(!$doc = Db::connect('docs')->where(['sef' => strip_tags($GLOBALS['router']->getSef()), 'lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->get())
      Router::redirect('/docs');

    if($doc->status != 1)
      Router::get_code(404, true);

		Db::connect('docs')->where(['id' => (int) $doc->id])->update(['views' => (int) $doc->views + 1]);

		return [
			'doc' => $doc,
			'docs' => Db::connect('docs')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->orderBy('rating asc')->getAll(),
			'docs_categories' => Db::connect('docs_categories')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->getAll(),
		];
	}

	public function delete()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_REQUEST['delete']) == false)
			echo Formatting::json(['type' => 'Error', 'message' => 'Delete param not found'], 400);

		$response = Docs::delete($_REQUEST);
		echo Formatting::json($response['data'], $response['code']);
	}
}