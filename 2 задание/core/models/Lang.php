<?php
class Lang
{
	private $data = [];

	public function __construct (array $data = [])
	{
		foreach ($data as $key => $value)
			$this->data[$key] = $value;
	}

  private function __clone() { /* ... @return ClassName */ }  // We protect from creation through "cloning"
  private function __wakeup() { /* ... @return ClassName */ }  // We protect from creation through "unserialize"

  public static function get ($array, $str)
  {
    $path = CONF . '/lang';
    $filename = $_COOKIE['lang'] ? : DEFAULT_LANG;

    if(!$variable = Storage::json('get', $path, $filename)[$array][str_replace(' ', '_', strtolower($str))])
    {
      self::put($path, $filename, [
        'array_name' => $array,
        'param_name' => str_replace(' ', '_', strtolower($str)),
        'param_value' => $str
      ]); 
    }

    return $variable ?: $str;
  }

  public static function put ($path, $filename, $params)
  {
    if (!file_exists(ROOT . $path)) mkdir(ROOT . $path, 0755, true);

    $items_list = Storage::json('get', $path, $filename);
    $items_list[$params['array_name']][$params['param_name']] = '_' . $params['param_value'];

    // $items_list = Storage::json('put', $path, $filename, json_encode($items_list, JSON_UNESCAPED_UNICODE));

    file_put_contents(ROOT . $path . DS . $filename . '.json', json_encode($items_list, JSON_UNESCAPED_UNICODE));
    unset($items_list);
  }

  public static function add ($data)
  {
		if (!empty($data['id'])) 
			return Router::get_code(400);

    $id = Db::connect('langs')->insert($data);
		Log::set($_SESSION['user_id'], 'Added lang', strtolower(__CLASS__), 'add', $id);

		return Router::get_code(201);
  }

	public static function edit ($data)
	{
		if (empty($data['id'])) 
			return Router::get_code(400);

		$item = Db::connect('langs')->select('id')->where(['id' => (int) $data['id']])->get();

		if ($item == null)
			return Router::get_code(400);

		Db::connect('langs')->where(['id' => (int) $item->id])->update($data);
		Log::set($_SESSION['user_id'], 'Edit lang', strtolower(__CLASS__), 'edit', $item->id);
		return Router::get_code(200);
	}

	public static function delete ($data)
	{
		if ($data['id'] == null)
      return Router::get_code(400);

		$item = Db::connect('langs')->select('id')->where(['id' => (int) $data['id']])->get();
		if ($item == null)
			return Router::get_code(404);

		Db::connect('langs')->where(['id' => (int) $item->id])->delete();
		Log::set($_SESSION['user_id'], 'Removed lang', strtolower(__CLASS__), 'delete', $item->id);
		return Router::get_code(200);
	}

	public static function clone ($data)
	{
		if (empty($data['id']))
			return Router::get_code(400);

		if (!$old_item = Db::connect('langs')->where(['id' => (int) $data['id']])->get())
			return Router::get_code(404);

		unset($old_item->id);
		$item = $old_item;

		$id = Db::connect('langs')->insert((array) $item);
		Log::set($_SESSION['user_id'], 'Clone lang', strtolower(__CLASS__), 'clone', $id);

		return Router::get_code(201);
	}
}
