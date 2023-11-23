<?php
class AppController 
{
	public function admin_index ()
	{
		App::check_auth($redirect = CI);
		App::check_permission($GLOBALS['router']->getController());

		return [];
	}

	public function admin_config ()
	{
		App::check_auth(SITE_URL);
		App::check_permission($GLOBALS['router']->getController());

		if(isset($_POST['update'])) 
		{
			if (!$info = Db::connect('information')->where(['lang_code' => $_COOKIE['lang'] ? : DEFAULT_LANG])->get()) {
				Notify::createError(Lang::get('messages', 'Not found'));
				Router::redirect_back();
			}

			if ((UPLOAD_ERR_OK === $_FILES['image']['error'])) 
			{
				if (!empty($info->img)) foreach (json_decode($info->img) as $img) Images::remove($img);

				$filename = explode('.', $_FILES['image']['name']);

				$_POST['app']['img'] = json_encode([
					'fullsize' => $fullsize = '/img/' . $GLOBALS['router']->getController() . '/fullsize/' . str_replace(' ', '_', date('ymdHis')) . '.' . $filename[1],
					'thumb' => $thumb = '/img/' . $GLOBALS['router']->getController() . '/thumb/' . str_replace(' ', '_', date('ymdHis')) . '.' . $filename[1]
				]);

				Images::upload($_FILES['image']['tmp_name'], ROOT . $fullsize, $filename[1], null, 500);
				Images::upload($_FILES['image']['tmp_name'], ROOT . $thumb, $filename[1], null, 75);
			}

			if (isset($_POST['deleteImage']) && $_POST['deleteImage'] == "on") {
				if (!empty($info->img)) foreach (json_decode($info->img) as $img) Images::remove($img);
			}

			$_POST['app']['requisites'] = json_encode($_POST['requisites']);
			Db::connect('information')->where(['id' => $info->id])->update($_POST['app']);

			Router::redirect(CI);
		}

		return [
			'app' => Db::connect('information')->where(['lang_code' => $_COOKIE['lang'] ? : DEFAULT_LANG])->get(),
			'langs' => Db::connect('langs')->getAll(),
			'seo' => [
				'title' => Lang::get('titles', 'Setup application'),
				'robots' => [
					'i' => 'noindex',
					'f' => 'nofollow'
				]
			]
		];
	}

	public function admin_db ()
	{
		App::check_auth($redirect = CI);
		App::check_permission($GLOBALS['router']->getController());

		if(isset($_POST['save'])) {
			Storage::json('set', CONF, 'db', $_POST['db']);
			Router::redirect(CI);
		}

		return [
			'db' => Storage::json('get', CONF, 'db'),
			'seo' => [
				'title' => Lang::get('titles', 'Setup database'),
				'robots' => [
					'i' => 'noindex',
					'f' => 'nofollow'
				]
			]
		];
	}

	public function admin_extensions ()
	{
		App::check_auth($redirect = $_POST['redirect'] ? base64_decode($_POST['redirect']) : CI);
		App::check_permission($GLOBALS['router']->getController());

		if($ext = $GLOBALS['router']->getSef()) 
		{
			if(Storage::json('check', CONF, $ext) == true) 
			{
				return [
					'extension_path' => $extension_path = str_replace(ROOT, '', EXTENSIONS) . DS . $ext,
					'extension_info' => Storage::json('get', $extension_path . DS, 'info'),
					'extension_params' => Storage::json('get', CONF, $ext)
				];
			} else {
				return [
					'extension_path' => $extension_path = str_replace(ROOT, '', EXTENSIONS) . DS . $ext,
					'extension_info' => Storage::json('get', $extension_path . DS, 'info'),
				];
			}
		}

		if(isset($_POST['save'])) 
		{
			Storage::json('set', CONF, $_POST['name'], $_POST['ext']);
			Router::redirect($redirect);
		}

		return [
			'seo' => [
				'title' => Lang::get('titles', 'Extensions'),
				'robots' => [
					'i' => 'noindex',
					'f' => 'nofollow'
				]
			]
		];
	}

	function currency ()
	{
		$valute_rates = json_decode(file_get_contents('https://www.cbr-xml-daily.ru/daily_json.js'));

		if($_GET['cur'] == 'us')
		{ 
			$value = $_GET['val'] * $valute_rates->Valute->USD->Value;
		}

		echo $value;
		die();
	}

	public function change_language()
	{
		Storage::cookie('set', ['title' => 'lang', 'val' => $_REQUEST['code'] ?: DEFAULT_LANG]);
		Router::redirect_back();
	}

	// Cookie
	public function cookie ()
	{
		if (isset($_REQUEST['title']))
			Storage::cookie($_REQUEST['action'] ?: 'set', ['title' => $_REQUEST['title'], 'val' => $_REQUEST['val']]);

		die(Formatting::json(['info' => 'Cookie ' . $_REQUEST['title'] . ' set value: ' . $_REQUEST['val']]));
	}

	// Session
	public function session ()
	{
		if (isset($_REQUEST['title']))
			Storage::session($_REQUEST['action'] ?: 'set', ['title' => $_REQUEST['title'], 'val' => $_REQUEST['val']]);

		die(Formatting::json(['info' => 'Cookie ' . $_REQUEST['title'] . ' set value: ' . $_REQUEST['val']]));
	}

	//Translit
	public function translit ()
	{
		if (!empty($_REQUEST['value']))
		{
			$code = 200;
			$data = [
				'string' => Text::translit($_REQUEST['value'], $_REQUEST['type']),
			];
		} else {
			$code = 400;
			$data = [];
		}

		die(Formatting::json($data, $code));
	}

	public function generate_password ()
	{
		if (!empty($_POST['generate']))
		{
			$code = 200;
			$data = [
				'password' => App::generate_password((int) $_POST['length'] ? : random_int(8, 15)),
			];
		} else {
			$code = 400;
			$data = [];
		}

		die(Formatting::json($data, $code));
	}

	public function relogin() 
	{
		App::check_auth('/cabinet');

		if(!empty($_GET['id']) && !empty($new_user = Db::connect('users')->select('*')->where(['id' => $_GET['id']])->get()))
		{
			if($new_user->usergroup >= 2)
			{
				$_SESSION['username'] = base64_decode($new_user->username);
				$_SESSION['user_id'] = $new_user->id;
				$_SESSION['usergroup'] = $new_user->usergroup;
				$_SESSION['userstatus'] = $new_user->status;
			}
		}

		Router::redirect('/cabinet');
	}

	public function multiple()
	{
		App::check_auth(SITE_URL);
		App::check_permission($GLOBALS['router']->getController());

		$items = explode(',', $_POST['values']);
		$controller = $_POST['controller'];

		if($_POST['clone'] == true)
			foreach ($items as $value)
				$controller::clone(['id' => $value]);

		if($_POST['delete'] == true)
			foreach ($items as $value)
				$controller::delete(['id' => $value]);

		Router::redirect('/admin/' . $controller);
	}
}