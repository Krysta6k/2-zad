<?php
class App
{
	private function __construct()
	{ /* ... @return ClassName */
	}  // We protect from creation through "new ClassName"
	private function __clone()
	{ /* ... @return ClassName */
	}  // We protect from creation through "cloning"
	private function __wakeup()
	{ /* ... @return ClassName */
	}  // We protect from creation through "unserialize"

	// Permissions
	public static function check_auth(?string $referer = null)
	{

		$referer = str_replace('/homepage', '', $referer);

		if ($_SESSION['username'] == null && $GLOBALS['router']->getAction() != 'login')
			return $referer ? Router::redirect("/user/login?redirect=" . base64_encode($referer)) : Router::get_code(401);

		if ($_SESSION['userstatus'] != 1) {
			unset($_SESSION['username']);
			unset($_SESSION['user_id']);
			unset($_SESSION['usergroup']);
			unset($_SESSION['userstatus']);
			session_destroy();

			return Router::get_code(403);
		}

		return Router::get_code(200);
	}

	public static function check_permission(?string $controller = null)
	{
		// if ($_SESSION['usergroup'] == 1)
		// 	return Router::get_code(200);

		if ($controller == null)
			return Router::get_code(500, true);

		if (!empty($controller_params = Db::connect('controllers')->select('name, access, lang_code')->where(['name' => strip_tags($controller), 'lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->get()))
			if ($_SESSION['usergroup'] == null || !in_array($_SESSION['usergroup'], json_decode($controller_params->access, true)))
				return Router::get_code(403, true);

		return Router::get_code(200);
	}

	public static function check_permission_manual(array $role = ['user'])
	{
		$access = array_column(Db::connect('users_groups')->select('id')->in('role', $role)->getAll(), 'id');

		if (!in_array($_SESSION['usergroup'], $access))
			Router::get_code(403, true);

		return true;
	}

	public static function get_nav($method_prefix = null)
	{
		$nav = [];

		switch (substr($method_prefix, 0, -1)) {
			case 'admin':
				$controller_types = Db::connect('controller_types')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->orderBy('rating asc')->getAll();
				$controllers = Db::connect('controllers')->where(['status' => 1, 'lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->orderBy('rating asc')->getAll();

				foreach ($controller_types as $t_key => $type) {
					array_push($nav, [
						'id' => (int) $type->id,
						'title' => $type->title,
						'description' => $type->description,
						'rating' => (int) $type->rating,
						'items' => []
					]);

					foreach ($controllers as $key => $controller) {
						if (($controller->type == $type->id) && (in_array($_SESSION['usergroup'], json_decode($controller->access)))) {
							array_push($nav[$t_key]['items'], [
								'id' => (int) $controller->id,
								'title' => $controller->title,
								'name' => $controller->name,
								'description' => $controller->description,
								'rating' => (int) $controller->rating,
								'access' => $controller->access,
								'display' => $controller->display,
							]);
						}
					}
				}
				break;

			default:
				foreach (Db::connect('navbar')->where(['method_prefix' => $method_prefix, 'lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG, 'display' => 1, 'parent_id' => 0])->orderBy('rating asc')->getAll() as $p_key => $parent) {
					array_push($nav, [
						'id' => (int) $parent->id,
						'title' => $parent->title,
						'link' => $parent->link,
						'rating' => (int) $parent->rating,
						'items' => []
					]);

					foreach (Db::connect('navbar')->where(['method_prefix' => $method_prefix, 'lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG, 'display' => 1, 'parent_id' => $parent->id])->orderBy('rating asc')->getAll() as $key => $child) {
						if ($child->parent_id == $parent->id) {
							array_push($nav[$p_key]['items'], [
								'id' => (int) $child->id,
								'title' => $child->title,
								'link' => $child->link,
								'rating' => (int) $child->rating,
								'access' => $child->access,
								'display' => $child->display,
							]);
						}
					}
				}
				break;
		}

		return $nav;
	}

	public static function get_scripts($position)
	{
		return Db::connect('scripts')->where(['status' => 1, 'position' => $position])->getAll();
	}

	// Password
	public static function generate_password($length = 12)
	{
		$chars = '1234567890AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz~!@$%._-+=&?';
		$numChars = strlen($chars);
		$string = '';

		for ($i = 0; $i < $length; $i++) $string .= substr($chars, rand(1, $numChars) - 1, 1);

		return $string;
	}

	public static function is_mobile()
	{
		return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
	}
}
