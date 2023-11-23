<?php
require_once(ROOT . '/vendor/autoload.php');
use Sendpulse\RestApi\ApiClient;

class UserController
{
	const SUPERDMIN = 1;
	const ADMIN = 2;
	const EMPLOYEE = 3;
	const USER = 4;

	public function index()
	{
		$response = App::check_auth(SITE_URL);
		die();
	}

	public function login()
	{
		unset($_SESSION['username']);
		unset($_SESSION['user_id']);
		unset($_SESSION['usergroup']);
		unset($_SESSION['userstatus']);

		$results = [
			'form' => [
				'action' => '/user/login',
				'btn' => 'login'
			]
		];

		if (isset($_POST['login'])) 
		{
			if (empty($_POST['user']['password'])) 
			{
				Notify::createError(Lang::get('messages', Lang::get('messages', 'Password not sending')));
				Router::redirect_back();
			}

			if($_POST['user']['phone']) 
			{
				if (!$user = Db::connect('users')->select('id, name, surname, email, phone, username, usergroup, status, pass_hash')->where(['phone' => str_replace([' ', '(', ')', '-'], '', $_POST['user']['phone'])])->get()) 
				{
					if(empty($user->phone))
					{
						Notify::createError(Lang::get('messages', 'Not found'));
						Router::redirect_back();
					}
				}
			}

			if($_POST['user']['email']) 
			{
				if (!$user = Db::connect('users')->select('id, name, surname, email, phone, username, usergroup, status, pass_hash')->where(['email' => $email = strip_tags($_POST['user']['email'])])->orWhere(['username' => base64_encode($email)])->get()) 
				{
					if(empty($user->email))
					{
						Notify::createError(Lang::get('messages', 'Not found'));
						Router::redirect_back();
					}
				}
			}

			if (password_verify(str_replace(' ', '', strip_tags($_POST['user']['password'])), $user->pass_hash))
			{
				$_SESSION = [
					'username'    => base64_decode($user->username),
					'user_id'     => $user->id,
					'usergroup'   => $user->usergroup,
					'userstatus'  => $user->status
				];

				Log::set($user->id, 'User logged in by phone number', $GLOBALS['router']->getController(), $GLOBALS['router']->getAction());
			} else {
				Notify::createError(Lang::get('messages', 'Password is\'n correct. Try again'));
				Router::redirect_back();
			}

			Router::redirect(base64_decode($_POST['redirect']) ?: '/cabinet');
		}

		return $results;
	}

	public function vk_login()
	{
		unset($_SESSION['username']);
		unset($_SESSION['user_id']);
		unset($_SESSION['usergroup']);
		unset($_SESSION['userstatus']);

		$config = Storage::json('get', CONF, 'oauth_vk');

		if (isset($_GET['code'])) {
			$oauth = new VK\OAuth\VKOAuth();
			$client_id = $config['client_id'];
			$client_secret = $config['client_secret'];
			$redirect_uri = $config['redirect_uri'];
			$code = $_GET['code'];

			$response = $oauth->getAccessToken($client_id, $client_secret, $redirect_uri, $code);

			$user = !empty($response['email']) ?
				Db::connect('users')->select('id, name, surname, email, username, usergroup, status, vk_user_id')->where(['vk_user_id' => $response['user_id']])->orWhere(['email' => strip_tags($response['email'])])->get() : 
				Db::connect('users')->select('id, name, surname, email, username, usergroup, status, vk_user_id')->where(['vk_user_id' => $response['user_id']])->get();

			if ($user && !empty($user->email)) {
				$_SESSION = [
					'username'    => base64_decode($user->username),
					'user_id'     => $user->id,
					'usergroup'   => $user->usergroup,
					'userstatus'  => $user->status
				];

				Db::connect('users')->where('id', $user->id)->update(
					[
						'email' => !empty($user->email) ? $user->email : strip_tags($response['email']),
						'vk_token' => $response['access_token']
					]
				);

				Log::set($user->id, 'User logged in by VK', $GLOBALS['router']->getController(), $GLOBALS['router']->getAction());

				Router::redirect(base64_decode($_POST['redirect']) ?: SITE_URL . '/admin');
			} else {
				$fields = [
					'activities', 'about', 'blacklisted', 'blacklisted_by_me', 'books', 'bdate', 'can_be_invited_group', 'can_post', 'can_see_all_posts', 'can_see_audio',
					'can_send_friend_request', 'can_write_private_message', 'career', 'common_count', 'connections', 'contacts', 'city', 'country', 'crop_photo',
					'domain', 'education', 'exports', 'followers_count', 'friend_status', 'has_photo', 'has_mobile', 'home_town', 'photo_100', 'photo_200', 'photo_200_orig',
					'photo_400_orig', 'photo_50', 'sex', 'site', 'schools', 'screen_name', 'status', 'verified', 'games', 'interests', 'is_favorite', 'is_friend', 'is_hidden_from_feed',
					'last_seen', 'maiden_name', 'military', 'movies', 'music', 'nickname', 'occupation', 'online', 'personal', 'photo_id', 'photo_max', 'photo_max_orig', 'quotes',
					'relation', 'relatives', 'timezone', 'tv', 'universities'
				];

				$user_data = json_decode(file_get_contents('https://api.vk.com/method/users.get?' . http_build_query([
					'user_ids' => $response['user_id'],
					'access_token' => $response['access_token'],
					'fields' => $fields,
					'v' => '5.131'
				])), true);

				$avatar = json_encode([
					'fullsize' => $fullsize = '/img/' . $GLOBALS['router']->getController() . '/fullsize/' . str_replace(' ', '_', date('ymdHis')) . '.jpg',
					'thumb' => $thumb = '/img/' . $GLOBALS['router']->getController() . '/thumb/' . str_replace(' ', '_', date('ymdHis')) . '.jpg'
				]);

				foreach ($user_data['response'] as $n_user) {
					$new_user = [
						'surname' => $n_user['last_name'],
						'name' => $n_user['first_name'],
						'birthday' => date('Y-m-d', strtotime($n_user['bdate'])),
						'sex' => $n_user['sex'] == 2 ? 1 : 2,
						'email' => $response['email'],
						'username' => base64_encode($n_user['domain']),
						'pass_hash' => password_hash(strip_tags($password = App::generate_password(10)), PASSWORD_DEFAULT),
						'usergroup' => $usergroup = 4,
						'status' => $status = 0,
						'code' => bin2hex(random_bytes(16)),
						'img' => $avatar,
						'registration_date' => date('Y-m-d H:i:s'),
						'vk_token' => $response['access_token'],
						'vk_user_id' => $n_user['id']
					];

					Images::save_image_from_url($n_user['photo_max_orig'], ROOT . $fullsize, 'jpg', 640);
					Images::save_image_from_url($n_user['photo_max_orig'], ROOT . $thumb, 'jpg', 128);

					$_SESSION = [
						'username' => $n_user['domain'],
						'user_id' => Db::connect('users')->insert($new_user),
						'usergroup' => $usergroup,
						'status' => $status,
					];

					Log::set($_SESSION['user_id'], 'User registred and logged in by VK', $GLOBALS['router']->getController(), $GLOBALS['router']->getAction());
				}

				Router::redirect(base64_decode($_POST['redirect']) ?: SITE_URL . '/admin');
			}
		}

		$vk = new VK\Client\VKApiClient();

		$oauth = new VK\OAuth\VKOAuth();
		$client_id = $config['client_id'];
		$redirect_uri = $config['redirect_uri'];
		$display = VK\OAuth\VKOAuthDisplay::PAGE;
		$scope = [VK\OAuth\Scopes\VKOAuthUserScope::PHOTOS, VK\OAuth\Scopes\VKOAuthUserScope::EMAIL];
		$state = bin2hex(random_bytes(16));

		$browser_url = $oauth->getAuthorizeUrl(VK\OAuth\VKOAuthResponseType::CODE, $client_id, $redirect_uri, $display, $scope, $state);

		Router::redirect($browser_url);
	}

	public function yandex_login()
	{
		unset($_SESSION['username']);
		unset($_SESSION['user_id']);
		unset($_SESSION['usergroup']);
		unset($_SESSION['userstatus']);

		$config = Storage::json('get', CONF, 'oauth_yandex');

		if (isset($_GET['code'])) 
		{
			$params = [
				'grant_type'    => 'authorization_code',
				'code'          => $_GET['code'],
				'client_id'     => $config['client_id'],
				'client_secret' => $config['client_secret'],
			];

			$ch = curl_init('https://oauth.yandex.ru/token');
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HEADER, false);
			$data = curl_exec($ch);
			curl_close($ch);

			$data = json_decode($data, true);

			if (!empty($data['access_token'])) 
			{
				$ch = curl_init('https://login.yandex.ru/info');
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, array('format' => 'json'));
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $data['access_token']));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_HEADER, false);
				$info = curl_exec($ch);
				curl_close($ch);

				$info = json_decode($info, true);

				Storage::json('get', CONF, 'db');

				if (!$login = Db::connect('users')->select('username')->where(['username' => base64_encode($info['login'])])->get()) {
					$login = base64_encode('user_' . date('Y_m_d_H_i_s'));
				}

				$avatar = json_encode([
					'fullsize' => $fullsize = '/img/' . $GLOBALS['router']->getController() . '/fullsize/' . str_replace(' ', '_', date('ymdHis')) . '.jpg',
					'thumb' => $thumb = '/img/' . $GLOBALS['router']->getController() . '/thumb/' . str_replace(' ', '_', date('ymdHis')) . '.jpg'
				]);

				if ($user = Db::connect('users')->select('id, name, surname, email, phone, username, usergroup, status')->in('email', $info['emails'])->orWhere('phone', $info['default_phone']['number'])->get()) {
					if (!empty($user->email)) {
						$_SESSION = [
							'username' => base64_decode($user->username),
							'user_id' => $user->id,
							'usergroup' => $user->usergroup,
							'userstatus' => $user->status
						];

						Db::connect('users')->where('id', $user->id)->update([
							'surname' => $info['last_name'],
							'name' => $info['first_name'],
							'birthday' => $info['birthday'],
							'email' => !empty($user->email) ? $user->email : strip_tags($info['default_email']),
							'phone' => $user->phone ?: str_replace([' ', '(', ')', '-'], '', $info['default_phone']['number']),
							'username' => $login,
							'sex' => $info['sex'] == 'male' ? 1 : 2,
							'code' => bin2hex(random_bytes(16)),
							// 'img' => $avatar,
							'yandex_token' => $data['access_token']

						]);

						// Images::save_image_from_url('https://avatars.yandex.net/get-yapic/' . $info['default_avatar_id'] . '/islands-200', ROOT . $fullsize, 'jpg', 200);
						// Images::save_image_from_url('https://avatars.yandex.net/get-yapic/' . $info['default_avatar_id'] . '/islands-retina-50', ROOT . $thumb, 'jpg', 100);

						Log::set($user->id, 'User logged in by Yandex', $GLOBALS['router']->getController(), $GLOBALS['router']->getAction());

						Router::redirect(base64_decode($_POST['redirect']) ?: SITE_URL . '/cabinet');
					}
				} else {
					// Notify::createError('Пользователь с почтой "' . $info['default_email'] . '" не существует');
					// Router::redirect('/');

					$new_user_id = Db::connect('users')->insert([
						'surname' => $info['last_name'],
						'name' => $name = $info['first_name'],
						'birthday' => $info['birthday'],
						'sex' => $info['sex'] == 'male' ? 1 : 2,
						'phone' => $phone = str_replace([' ', '(', ')', '-'], '', $info['default_phone']['number']),
						'email' => $email = $info['default_email'],
						'username' => $login,
						'pass_hash' => password_hash(strip_tags($password = App::generate_password(8)), PASSWORD_DEFAULT),
						'usergroup' => $usergroup = 4,
						'status' => $status = 0,
						'code' => bin2hex(random_bytes(16)),
						'img' => $avatar,
						'registration_date' => date('Y-m-d H:i:s'),
						'yandex_token' => $data['access_token']
					]);

					$_SESSION = [
						'username'    => $login,
						'user_id'     => $new_user_id,
						'usergroup'   => $usergroup,
						'userstatus'  => $status
					];

					Images::save_image_from_url('https://avatars.yandex.net/get-yapic/' . $info['default_avatar_id'] . '/islands-200', ROOT . $fullsize, 'jpg', 200);
					Images::save_image_from_url('https://avatars.yandex.net/get-yapic/' . $info['default_avatar_id'] . '/islands-retina-50', ROOT . $thumb, 'jpg', 100);

					Log::set($new_user_id, 'User registred and logged in by Yandex', $GLOBALS['router']->getController(), $GLOBALS['router']->getAction());

					if ($email) {
						/** SENDPULSE **/
						if (Storage::json('check', CONF, 'sendpulse'))
							require_once(EXTENSIONS . '/sendpulse/snippets/registration.php');
						else
							Notify::createWarning('Senpulse не настроен');
					}

					if ($phone) {
						/** SMS Aero **/
						if (Storage::json('check', CONF, 'smsaero'))
						{
							$sms = [
								'phone' => $phone,
								'text' => 'Ваш пароль: ' . $password,
							];

							require_once(EXTENSIONS . '/smsaero/snippets/send_sms.php');
						} else {
							Notify::createWarning('SMS Aero не настроен');
						}
					}
				}

				Router::redirect(base64_decode($_POST['redirect']) ?: SITE_URL . '/cabinet/');
			}
		}

		$params = array(
			'client_id'     => $config['client_id'],
			'redirect_uri'  => $config['redirect_uri'],
			'response_type' => 'code',
			'state'         =>  bin2hex(random_bytes(16))
		);

		$url = 'https://oauth.yandex.ru/authorize?' . urldecode(http_build_query($params));

		Router::redirect($url);
	}

	public function registration()
	{
		unset($_SESSION['username']);
		unset($_SESSION['user_id']);
		unset($_SESSION['usergroup']);
		unset($_SESSION['userstatus']);

		$results = [
			'form' => [
				'action' => '/user/registration',
				'btn' => 'registration'
			]
		];

		if (isset($_POST['registration'])) 
		{
			$is_ok = true;

			// if ($is_ok && is_null($_POST['data']['agreement'])) 
			// {
			//   Notify::createError('Вы не приняли политику сайта');
			//   $is_ok = false;
			// }

			if ($is_ok && Db::connect('users')->where(['phone' =>  str_replace([' ', '(', ')', '-'], '', $_POST['user']['phone'])])->get() != null) 
			{
				Notify::createError('Данный телефон уже используется');
				$is_ok = false;
				Router::redirect('/user/restore_password');
			}

			if ($is_ok) 
			{
				$app = Db::connect('information')->where(['lang_code' => $_COOKIE['lang'] ? : DEFAULT_LANG])->get();

				$formData = [
					'name' => $_POST['user']['name'],
					'phone' => $phone = str_replace([' ', '(', ')', '-'], '', $_POST['user']['phone']),
					'email' => $email = strip_tags($_POST['user']['email']),
					'username' => $login = base64_encode($_POST['user']['email']),
					'pass_hash' => password_hash(strip_tags($password = App::generate_password(random_int(6, 12))), PASSWORD_DEFAULT),
					'usergroup' => 4,
					'status' => 0,
					'active' => 0
				];

				$new_user_id = Db::connect('users')->insert($formData);

				Log::set($new_user_id, 'User registred', $GLOBALS['router']->getController(), $GLOBALS['router']->getAction());

				if ($_POST['user']['email'])
				{
					/** SENDPULSE **/
					if (Storage::json('check', CONF, 'sendpulse'))
					{
						// require_once(EXTENSIONS . '/sendpulse/snippets/registration.php');

						$html_message = '
						<p>Здравствуйте, ' . $_POST['user']['name'] . '!</p>
						<p>Вы успешно зарегистрировались на сайте <a href="'. SITE_URL . '" target="_blank">'. SITE_URL .'</a></p>
						<p>Используйте следующие данные для входа:</p>
						<p>
							<b>Email</b>: ' .  $email . '<br />
							<b>Телефон</b>: ' .  $phone . '<br />
							<b>Пароль</b>: ' . $password . '
						</p>
						
						<p>С уважением, &laquo;' . ($app->seo_title ? : $app->title) . '&raquo;</p>
						<a href="'. SITE_URL .'" target="_blank"><img src="'. SITE_URL . ($app->img ?: '/img/splash.svg') . '" height="34"></a><br>
						<small>' . date('d.m.Y H:i:s') . '</small>
					';

					$sp = new ApiClient(Storage::json('get', CONF, 'sendpulse')['id'], Storage::json('get', CONF, 'sendpulse')['secret']);
					$sp->smtpSendMail([
						'html' => $html_message,
						'text' => strip_tags($html_message),
						'subject' => 'Восстановление пароля на сайте «' . $app->title . '»',
						'from' => [
							'name' => $app->title,
							'email' => $app->email,
						],
						'to' => [
							[
								'name' => $_POST['user']['name'],
								'email' => $email,
							]
						]
					]);
					}
					else
						Notify::createWarning('Senpulse не настроен');
				}

				if ($_POST['user']['phone'])
				{
					/** SMS Aero **/
					if (Storage::json('check', CONF, 'smsaero')) 
					{
						$sms = [
							'phone' => $phone,
							'text' => 'Ваш пароль: ' . $password,
						];

						require_once(EXTENSIONS . '/smsaero/snippets/send_sms.php');
					} else
						Notify::createWarning('SMS Aero не настроен');
				}

				Notify::createSuccess('Вам направлены данные для входа');
			}

			Router::redirect($_POST['redirect'] ?: '/user/login');
		}

		return $results;
	}

	public function logout()
	{
		Log::set($_SESSION['user_id'], 'User logout', $GLOBALS['router']->getController(), $GLOBALS['router']->getAction());

		if (isset($_SERVER['HTTP_COOKIE'])) {
			$cookies = explode(';', $_SERVER['HTTP_COOKIE']);

			foreach($cookies as $cookie) {
				$parts = explode('=', $cookie);
				$name = trim($parts[0]);

				if(!in_array($name, ['policy', 'lang'])) Storage::cookie('unset', ['title' => $name]);
			}
		}

		unset($_SESSION['username']);
		unset($_SESSION['user_id']);
		unset($_SESSION['usergroup']);
		unset($_SESSION['userstatus']);
		session_destroy();
		Router::redirect(SITE_URL);
	}

	public function restore_password()
	{
		$redirect = base64_decode($_POST['redirect']);
		
		$results = [
			'form' => [
				'action' => '/user/restore_password',
				'btn' => 'restore'
			]
		];

		if (isset($_POST['restore'])) 
		{
			$new_password = App::generate_password(random_int(6, 12));
			$app = Db::connect('information')->where(['lang_code' => $_COOKIE['lang'] ? : DEFAULT_LANG])->get();
			$is_ok = true;

			if($_POST['user']['phone']) 
			{
				if (!$user = Db::connect('users')->select('id, name, phone, email')->where(['phone' => str_replace([' ', '(', ')', '-'], '', $_POST['user']['phone'])])->get()) 
				{
					if(empty($user->phone))
					{
						Notify::createError(Lang::get('messages', 'Not found'));
						$is_ok = false;
					}
				}
			}

			if($_POST['user']['email']) 
			{
				if (!$user = Db::connect('users')->select('id, name, phone, email')->where(['email' => strip_tags($_POST['user']['email'])])->get()) 
				{
					if(empty($user->email))
					{
						Notify::createError(Lang::get('messages', 'Not found'));
						$is_ok = false;
					}
				}
			}

			if($is_ok == true)
			{
				if (Storage::json('check', CONF, 'smsaero')) 
				{
					$sms = [
						'phone' => $user->phone,
						'text' => 'Ваш новый пароль: ' . $new_password,
					];

					require_once(EXTENSIONS . '/smsaero/snippets/send_sms.php');
				} else {
					Notify::createWarning('SMS Aero не настроен');
				}


				Db::connect('users')->where('id', (int) $user->id)->update(['pass_hash' => password_hash(strip_tags($new_password), PASSWORD_DEFAULT)]);
				Log::set($user->id, 'User restore password', $GLOBALS['router']->getController(), $GLOBALS['router']->getAction());
				Notify::createSuccess('Вам направлены данные для входа');
			}

			Router::redirect($redirect ?: '/cabinet');
		}

		return $results;
	}
	public function admin_index()
	{
		$response = App::check_auth($redirect = CI);
		$response = App::check_permission($GLOBALS['router']->getController());

		$limit = (int) $_COOKIE['admin_items_per_page'] ?: ADMIN_ITEMS_PER_PAGE;
		$status = (int) $_COOKIE['display_status'] ? [0, 1] : [0, 1];
		$sorting = $_COOKIE['items_sorting'] ?: 'id ASC, surname ASC';
		$offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;

		$where = [];
		$_GET['category'] ? $where['usergroup'] = Db::connect('users_groups')->select('id, code')->where(['code' => strip_tags($_GET['category'])])->get()->id : '';

		return [
			'users' => Db::connect('users')->in('status', $status)->where($where)->limit($limit)->offset($offset)->orderBy($sorting)->getAll(),
			'usergroups' => Db::connect('users_groups')->getAll(),
			'search_placeholder' => 'Enter first name, last name or phone number',

			'total' => count(Db::connect('users')->select('id')->where($where)->in('status', $status)->getAll())
		];
	}

	public function admin_profile()
	{
		$response = App::check_auth(SITE_URL);

		if ($_GET['id']) {
			$user = Db::connect('users')->select('username')->where(['id' => $_GET['id']])->get();
			Router::redirect(CI . DS . $GLOBALS['router']->getAction() . DS . base64_decode($user->username));
		}

		$limit = (int) $_COOKIE['admin_items_per_page'] ?: ADMIN_ITEMS_PER_PAGE;
		$sorting = $_COOKIE['items_sorting'] ?: 'date_create DESC';
		$offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;

		$user = Db::connect('users')->where(['username' => base64_encode(strip_tags($GLOBALS['router']->getSef()))])->get();
		$where = [];
		$where['user_id'] = $user->id;

		return [
			'user' => $user,
			'types' => [['id' => 1,'title' => 'Заявка'],['id' => 2,'title' => 'Вопрос']],
			'requests' => Db::connect('requests')->where($where)->limit($limit)->offset($offset)->orderBy($sorting)->getAll(),
			'total' => count(Db::connect('requests')->select('id')->where($where)->getAll())
		];
	}

	public function admin_add()
	{
		$response = App::check_auth($redirect = CI);

		$results = [
			'users_groups' => Db::connect('users_groups')->where('id', '>=', $_SESSION['usergroup'])->getAll(),
		];

		return $results;
	}

	public function admin_edit()
	{
		$response = App::check_auth($redirect = CI);

		if (isset($_GET['id'])) {

			if (!$user = Db::connect('users')->where('id', (int) $_GET['id'])->get()) {
				Notify::createError('User not found');
				Log::set($_SESSION['user_id'], 'User not found', $GLOBALS['router']->getController(), $GLOBALS['router']->getAction(), (int) $_GET['id']);

				Router::redirect(DS . substr($GLOBALS['router']->getMethodPrefix(), 0, -1) . DS . $GLOBALS['router']->getController());
				return;
			}

			$results = [
				'user' => $user,
				'users_groups' => Db::connect('users_groups')->where('id', '>=', $_SESSION['usergroup'])->getAll(),
			];

			return $results;
		}
	}

	public function edit_profile()
	{
		$response = App::check_auth($redirect = base64_decode($_POST['redirect']) ?: '/cabinet');

		if (!$user = Db::connect('users')->where(['id' => (int) $_GET['id']])->get()) {
			Notify::createError(Lang::get('messages', 'User not found'));
			Log::set($_SESSION['user_id'], 'User not found', $GLOBALS['router']->getController(), $GLOBALS['router']->getAction(), (int) $_GET['id']);

			Router::redirect($redirect);
		}

		$results = [
			'user' => $user,
			'users_groups' => Db::connect('users_groups')->getAll(),
		];

		return $results;
	}

	public function get_list()
	{
		$response = App::check_auth($redirect = CI);

		if (isset($_POST['get_list']))
			echo Formatting::json(Db::connect('users')->select('id, name, surname, patronymic, img, usergroup')->getAll(), 200);

		die();
	}

	public function add()
	{
		$response = App::check_auth($redirect = SITE_URL . DS . substr($GLOBALS['router']->getMethodPrefix(), 0, -1) . DS . $GLOBALS['router']->getController());
		$response = App::check_permission($GLOBALS['router']->getController());

		if (isset($_POST['add'])) 
		{
			if ((UPLOAD_ERR_OK === $_FILES['image']['error'])) 
			{
				$filename = explode('.', $_FILES['image']['name']);

				$_POST['user']['img'] = json_encode([
					'fullsize' => $fullsize = '/img/' . $GLOBALS['router']->getController() . '/fullsize/' . str_replace(' ', '_', date('ymdHis')) . '.' . $filename[1],
					'thumb' => $thumb = '/img/' . $GLOBALS['router']->getController() . '/thumb/' . str_replace(' ', '_', date('ymdHis')) . '.' . $filename[1]
				]);

				Images::upload($_FILES['image']['tmp_name'], ROOT . $fullsize, $filename[1], null, 500);
				Images::upload($_FILES['image']['tmp_name'], ROOT . $thumb, $filename[1], null, 75);
			}

			//Проверка на логин
			$username = base64_encode(strtolower(strip_tags($_POST['user']['username'])));
			if (Db::connect('users')->where('username', $username)->get() != null) {
				Notify::createError('Логин занят');
				Router::redirect_back();
			}

			Db::connect('users')->insert([
				'name'              => strip_tags($_POST['user']['name']),
				'surname'           => strip_tags($_POST['user']['surname']),
				'patronymic'        => strip_tags($_POST['user']['patronymic']),
				'birthday'          => strip_tags($_POST['user']['birthday']),
				'sex'               => (int) $_POST['user']['sex'] ?: 0,
				'phone'             => str_replace([' ', '(', ')', '-'], '', $_POST['user']['phone']),
				'email'             => strip_tags($_POST['user']['email']),
				'username'          => $username,
				'pass_hash'         => password_hash(strip_tags($_POST['user']['password']), PASSWORD_DEFAULT),
				'usergroup'         => $_POST['user']['usergroup'] ?: 4,
				'status'            => (int) $_POST['user']['status'],
				'code'              => $_POST['user']['code'] ?: uniqid(),
				'img'               => $_POST['user']['img'] ?: null,
				'img_upd'           => time(),
				'registration_date' => $_POST['user']['registration_date'] ?: date('Y-m-dTH:s')
			]);

			Notify::createSuccess('User add successfully');
		}

		Router::redirect(base64_decode($_POST['redirect']) ?: $redirect);
	}

	public function edit()
	{
		$response = App::check_auth($redirect = base64_decode($_POST['redirect']));
		// $response = App::check_permission($GLOBALS['router']->getController());

		if (isset($_POST['edit'])) {
			if (!$user = Db::connect('users')->where(['id' => (int) $_POST['user']['id']])->get()) {
				Notify::createError(Lang::get('messages', 'User not found'));
				Router::redirect($redirect);
			}

			if ((UPLOAD_ERR_OK === $_FILES['image']['error'])) {
				if (!empty($user->img)) foreach (json_decode($user->img) as $img) Images::remove($img);

				$filename = explode('.', $_FILES['image']['name']);

				$_POST['user']['img'] = json_encode([
					'fullsize' => $fullsize = '/img/' . $GLOBALS['router']->getController() . '/fullsize/' . str_replace(' ', '_', date('ymdHis')) . '.' . $filename[1],
					'thumb' => $thumb = '/img/' . $GLOBALS['router']->getController() . '/thumb/' . str_replace(' ', '_', date('ymdHis')) . '.' . $filename[1]
				]);

				Images::upload($_FILES['image']['tmp_name'], ROOT . $fullsize, $filename[1], null, 500);
				Images::upload($_FILES['image']['tmp_name'], ROOT . $thumb, $filename[1], null, 75);
			}

			if (isset($_POST['deleteImage']) && $_POST['deleteImage'] == "on") {
				if (!empty($user->img)) foreach (json_decode($user->img) as $img) Images::remove($img);

				$_POST['user']['img_path'] = '';
				$_POST['user']['img_prop'] = '';
			}

			//Проверка на логин
			$username = $_POST['user']['username'] ? base64_encode(strip_tags($_POST['user']['username'])) : $user->username;
			if (Db::connect('users')->where('username', $username)->notWhere('id', $user->id)->get() != null) {
				Notify::createError('Логин занят');
				Router::redirect_back();
			}

			$user_params = [
				'name'              => $_POST['user']['name']             ? strip_tags($_POST['user']['name']) : $user->name,
				'surname'           => $_POST['user']['surname']          ? strip_tags($_POST['user']['surname']) : $user->surname,
				'patronymic'        => $_POST['user']['patronymic']       ? strip_tags($_POST['user']['patronymic']) : $user->patronymic,
				'birthday'          => $_POST['user']['birthday'] ? Formatting::date($_POST['user']['birthday'], 'Y-m-d') : $user->birthday,
				'sex'               => (int) $_POST['user']['sex']        ?: $user->sex,
				'phone'             => $_POST['user']['phone']            ? str_replace([' ', '(', ')', '-'], '', $_POST['user']['phone']) : $user->phone,
				'email'             => $_POST['user']['email']            ? strip_tags($_POST['user']['email']) : $user->email,
				'username'          => $username,
				'pass_hash'         => $_POST['user']['password']         ? password_hash(strip_tags($_POST['user']['password']), PASSWORD_DEFAULT) : $user->pass_hash,
				'usergroup'         => (int) $_POST['user']['usergroup']  ?: $user->usergroup,
				'status'            => (int) $_POST['user']['status'],
				'img'               => $_POST['user']['img']              ?: $user->img,
				'img_upd'           => time(),
			];

			Db::connect('users')->where('id', (int) $user->id)->update($user_params);

			Log::set($_SESSION['user_id'], 'User edit successfully', $GLOBALS['router']->getController(), $GLOBALS['router']->getAction(), (int) $user->id);

			Notify::createSuccess(Lang::get('messages', 'User edit successfully'));
		}

		Router::redirect($redirect);
	}

	public function delete()
	{
		$response = App::check_auth($redirect = CI);
		$response = App::check_permission($GLOBALS['router']->getController());

		if (isset($_POST['delete'])) {
			if (!$user = Db::connect('users')->select('id, img')->where('id', (int) $_POST['id'])->get()) {
				$callback = [
					'type' => 'Danger',
					'message' => Lang::get('messages', 'Not found')
				];

				return;
			}

			if (!empty($user->img)) foreach (json_decode($user->img) as $img) Images::remove($img);

			Db::connect('companies_employees')->where('user_id', $user->id)->delete();
			Db::connect('users')->where('id', $user->id)->delete();

			$callback = [
				'type' => 'Success',
				'message' => Lang::get('messages', 'Successful removal'),
			];
		}

		echo json_encode($callback);
		die();
	}

	public function activate()
	{
		$response = App::check_auth($redirect = CI);

		if (isset($_GET['code']) && ($_GET['activate'] == true)) {
			if (!$user = Db::connect('users')->select('id, status, code')->where('code', (int) $_GET['code'])->get()) {
				Notify::createError(Lang::get('messages', 'Not found'));
				return;
			} else {
				Db::connect('users')->where('id', (int) $user->id)->update(['status' => 1]);
				Notify::createSuccess('User activate successfully');
			}
		}

		Router::redirect($_POST['redirect'] ?: SITE_URL);
	}

	public function api_edit()
	{
		App::check_auth($redirect = $_POST['redirect'] ? base64_decode($_POST['redirect']) : CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_POST['edit'])) {
			if ($_POST['data'] == null)
				echo Formatting::json(['message' => Lang::get('messages', 'Data not sending')], 400);

				Db::connect('users')->where('id', (int) $_POST['data']['id'])->update($_POST['data']);
			// $controller = Reviews::edit($_POST['data']);
			// echo Formatting::json(['message' => Lang::get('messages', $controller['message'])], $controller['code']);
		}

		die();
	}
}