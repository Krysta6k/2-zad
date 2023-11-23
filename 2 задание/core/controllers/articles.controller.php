<?php
class ArticlesController 
{
  public function index ()
  {
    $limit = DEFAULT_ITEMS_PER_PAGE;
		$sorting = $_COOKIE['items_sorting'] ?: DEFAULT_SORTING;
		$offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;

		$where = [];
		$where['status'] = 1;
		$where['lang_code'] = $_COOKIE['lang'] ? : DEFAULT_LANG;

    return [
      'articles' => Db::connect('articles')->where($where)->limit($limit)->offset($offset)->orderBy('publication_date DESC')->getAll(),
			'articles_categories' => Db::connect('articles_categories')->where(['status' => 1, 'lang_code' => $_COOKIE['lang'] ? : DEFAULT_LANG])->getAll(),
      'total' => count(Db::connect('articles')->select('id')->where($where)->getAll()),
    ];
  }
	public function category()
	{
		if ((!$article_category = Db::connect('articles_categories')->where(['sef' => strip_tags($GLOBALS['router']->getSef()), 'lang_code' => $_COOKIE['lang'] ? : DEFAULT_LANG])->get()) || ($article_category->status != 1))
			Router::get_code(404, true);

		Db::connect('articles_categories')->where(['sef' => (int) $article_category->sef])->update(['views' => (int) $article_category->views + 1]);

		$limit = $_COOKIE['items_per_page'] ?: DEFAULT_ITEMS_PER_PAGE;
		$sorting = $_COOKIE['items_sorting'] ?: DEFAULT_SORTING;
		$offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;

		$where = [];
		$where['category_id'] = $article_category->id;
		$where['status'] = 1;
		$where['lang_code'] = $_COOKIE['lang'] ? : DEFAULT_LANG;

		return [
			'article_category' => $article_category,
			'articles' => Db::connect('articles')->where($where)->limit($limit)->offset($offset)->orderBy('publication_date DESC')->getAll(),
			'articles_categories' => Db::connect('articles_categories')->where(['status' => 1, 'lang_code' => $_COOKIE['lang'] ? : DEFAULT_LANG])->getAll(),
			'seo' => [
				'title' => $article_category->seo_title ?: $article_category->title,
				'description' => $article_category->seo_description,
				'keywords' => $article_category->seo_keywords,
				'robots' => [
					'i' => $article_category->robots_index,
					'f' => $article_category->robots_follow
				]
			]
		];
	}

  public function detail ()
  {
    if(!$article = Db::connect('articles')->where(['sef' => strip_tags($GLOBALS['router']->getSef())])->get())
      Router::redirect('/articles');

    if($article->status != 1)
      Router::get_code(404, true);

		Db::connect('articles')->where(['id' => (int) $article->id])->update(['views' => (int) $article->views + 1]);

    return [
      'article' => $article,
      'article_category' => Db::connect('articles_categories')->select('title, sef')->where(['id' => $article->category_id])->get(),
			'author' => Db::connect('users')->select('name, surname, img, position')->where(['id' => $article->author_id])->get(),
			'similar_articles' => Db::connect('articles')->where(['status' => 1, 'category_id' => $article->category_id])->limit(3)->orderBy('RAND()')->getAll(),
      'seo' => [
        'title' => $article->seo_title ?: $article->title,
        'description' => strip_tags($article->seo_title ?: $article->description),
        'robots' => [
          'i' => $article->robots_index ?: 'index',
          'f' => $article->robots_follow ?: 'follow',
        ]
      ]
    ];
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
		$_GET['category'] ? $where['category_id'] = Db::connect('articles_categories')->select('id, sef')->where(['sef' => strip_tags($_GET['category'])])->get()->id : '';

		return [
			'articles' => Db::connect('articles')->where($where)->limit($limit)->offset($offset)->orderBy($sorting)->getAll(),
			'articles_categories' => Db::connect('articles_categories')->getAll(),
			'total' => count(Db::connect('articles')->select('id')->where($where)->getAll())
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

			$_POST['data']['publication_date'] = date('Y-m-d H:i:s');
			$article = Articles::add($_POST['data']);
			if ($article['code'] != 201) 
			{
				Notify::createError($article['message']);
				Router::redirect_back();
			}

			Notify::createSuccess($article['message']);
			Router::redirect($redirect);
		}

		return [
			'articles_categories' => Db::connect('articles_categories')->getAll(),
			'langs' => Db::connect('langs')->getAll(),
			'rating' => (int) (count(Db::connect('articles')->select('id')->getAll()) + 1) * 10
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

			$article = Articles::edit($_POST['data']);
			if ($article['code'] != 200) 
			{
				Notify::createError($article['message']);
				Router::redirect_back();
			}

			Notify::createSuccess($article['message']);
			Router::redirect($redirect);
		}

		$article = Db::connect('articles')->where(['id' => (int) $_GET['id']])->get();
		if ($article == null) 
		{
			Notify::createError(Lang::get('messages', 'Not found'));
			Router::redirect_back();
		}

		return [
			'article' => $article,
			'langs' => Db::connect('langs')->getAll(),
			'articles_categories' => Db::connect('articles_categories')->getAll(),
		];
	}

	public function delete()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_REQUEST['delete']) == false)
			echo Formatting::json(['type' => 'Error', 'message' => 'Delete param not found'], 400);

		$response = Articles::delete($_REQUEST);
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

			$controller = Articles::edit($_POST['data']);
			echo Formatting::json(['message' => Lang::get('messages', $controller['message'])], $controller['code']);
		}

		die();
	}
}