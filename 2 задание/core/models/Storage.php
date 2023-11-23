<?php
class Storage extends App
{
	public static function get_default ($results = [])
	{
		return array_merge($results, [
			'information' => Db::connect('information')->where(['lang_code' => $_COOKIE['lang'] ? : DEFAULT_LANG])->get(),
			'session_user' => $_SESSION['user_id'] ? Db::connect('users')->select('id, name, surname, patronymic, username, phone, email, sex, birthday, usergroup, img')->where('id', (int) $_SESSION['user_id'])->get() : null,
			'controller_info' => $controller = Db::connect('controllers')->where(['name' => $GLOBALS['router']->getController(), 'lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->get(),
			'social_networks' => Db::connect('social_networks')->where(['status' => 1])->getAll(),
			'seo' => self::get_seo($controller, $results['seo']),
		]);
	}

	public static function get_seo ($controller, $data = null) 
	{
		$controller_data = [
			'seo_title' => $controller->seo_title ? : $controller->title,
			'seo_description' => $controller->seo_description,
			'seo_keywords' => $controller->seo_keywords,
			'robots' => [
				'i' => $controller->robots_index,
				'f' => $controller->robots_follow
			]
		];

		return [
			'title' => preg_replace('/\"([^\"]*)\"/ismU','&laquo;$1&raquo;', $data['title'] ? : $controller_data['seo_title']),
			'description' => preg_replace('/\"([^\"]*)\"/ismU','&laquo;$1&raquo;', $data['description'] ? : $controller_data['seo_description']),
			'keywords' => preg_replace('/\"([^\"]*)\"/ismU','&laquo;$1&raquo;', $data['keywords'] ? : $controller_data['seo_keywords']),
			'img' => $data['img'] ? $data['img'] : '/img/splash.png',
			'robots' => [
				'i' => $data['robots']['i'] ? : $controller_data['robots']['i'],
				'f' => $data['robots']['f'] ? : $controller_data['robots']['f']
			]
		];
	}

	public static function json ($action, $path, $name, $params = null)
	{
		switch ($action) {
			case 'check':
				return file_exists(ROOT . $path . DS . $name . '.json') ? true : false;

			case 'set':
				if (!file_exists(ROOT . $path)) 
				{
					mkdir(ROOT . $path, 0755, true);
					$message = 'Directory created';
				}

				file_put_contents(ROOT . $path . DS . $name . '.json', json_encode($params));
				echo Formatting::json(['info' => $message, 'params' => $params]);

			case 'get':
				$array = json_decode(file_get_contents(ROOT . $path . DS . $name . '.json'), true);
				return $params ? $array[$params] : $array;

			case 'put':
				if (!file_exists(ROOT . $path)) 
				{
					mkdir(ROOT . $path, 0755, true);
					$message = 'Directory created';
				}

				$items_list = json_decode(file_get_contents(ROOT . $path . DS . $name . '.json'), true);

				file_put_contents(ROOT . $path . DS . $name . '.json', json_encode(array_merge($items_list, $params), JSON_UNESCAPED_UNICODE));
				unset($items_list);
				break;
		}
	}
	
  // Cookie
	public static function cookie ($action, $params)
	{
		if ($action == 'set')
			setcookie($params['title'], $params['val'], time() + 360 * 24 * 60 * 60, "/", $_SERVER['SERVER_NAME']);

		if ($action == 'unset')
			setcookie($params['title'], '', 0, "/", $_SERVER['SERVER_NAME']);
	}

	// Session
	public static function session ($action, $params)
	{
		if ($action == 'set')
			$_SESSION[$params['title']] = $params['val'];

		if ($action == 'unset')
			unset($_SESSION[$params['title']]);
	}

  // Memcache
  public static function memcache ($action, $params) 
  {
    if(($action == 'init') && ($params['status'] == true))
    {
      $memcache = new Memcache;
      $memcache->connect(self::json('get', CONF, 'memcache')['server'], self::json('get', CONF, 'memcache')['port']) or die("Не могу подключиться");
    }

		if(($action == 'flush') && ($params['clear_cache'] == true))
    {
			$memcache->flush();
		}
  }
}
