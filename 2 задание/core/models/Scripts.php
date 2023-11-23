<?php
class Scripts 
{
	private $data = [];

	public function __construct (array $data = [])
	{
		foreach ($data as $key => $value)
			$this->data[$key] = $value;
	}

	private function __clone () { /* ... @return ClassName */ }  // We protect from creation through "cloning"
	private function __wakeup () { /* ... @return ClassName */ }  // We protect from creation through "unserialize"

  public static function add ($data)
  {
		if (!empty($data['id'])) 
			return Router::get_code(400);

    $id = Db::connect('scripts')->insert($data);
		Log::set($_SESSION['user_id'], 'Added script', strtolower(__CLASS__), 'add', $id);

		return Router::get_code(201);
  }

	public static function edit ($data)
	{
		if (empty($data['id'])) 
			return Router::get_code(400);

		$item = Db::connect('scripts')->select('id')->where(['id' => (int) $data['id']])->get();

		if ($item == null)
			return Router::get_code(400);

		Db::connect('scripts')->where(['id' => (int) $item->id])->update($data);
		Log::set($_SESSION['user_id'], 'Edit script', strtolower(__CLASS__), 'edit', $item->id);

		return Router::get_code(200);
	}

	public static function delete ($data)
	{
		if ($data['id'] == null)
      return Router::get_code(400);

		$item = Db::connect('scripts')->select('id')->where(['id' => (int) $data['id']])->get();
		if ($item == null)
			return Router::get_code(404);

		Db::connect('scripts')->where(['id' => (int) $item->id])->delete();
		Log::set($_SESSION['user_id'], 'Removed script', strtolower(__CLASS__), 'delete', $item->id);
		return Router::get_code(200);
	}

	public static function clone ($data)
	{
		if (empty($data['id']))
			return Router::get_code(400);

		if (!$old_item = Db::connect('scripts')->where(['id' => (int) $data['id']])->get())
			return Router::get_code(404);

		unset($old_item->id);
		$item = $old_item;

		$id = Db::connect('scripts')->insert((array) $item);
		Log::set($_SESSION['user_id'], 'Clone script', strtolower(__CLASS__), 'clone', $id);

		return Router::get_code(201);
	}
}