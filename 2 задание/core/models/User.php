<?php
class User {
	private $full_img_size = 250;
	private $thumb_img_size = 90;
	private $img_type = 'png';
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

		if ((UPLOAD_ERR_OK === $_FILES['image']['error'])) 
		{
			$filename = explode('.', $_FILES['image']['name']);

			$data['img'] = json_encode([
				'fullsize' => $fullsize = '/img/' . $GLOBALS['router']->getController() . '/fullsize/' . str_replace(' ', '_', date('ymdHis')) . '.' . $filename[1],
				'thumb' => $thumb = '/img/' . $GLOBALS['router']->getController() . '/thumb/' . str_replace(' ', '_', date('ymdHis')) . '.' . $filename[1]
			]);

			Images::upload($_FILES['image']['tmp_name'], ROOT . $fullsize, $filename[1], null, 250);
			Images::upload($_FILES['image']['tmp_name'], ROOT . $thumb, $filename[1], null, 90);
		}

    $id = Db::connect('users')->insert($data);
		Log::set($_SESSION['user_id'], 'Added user', strtolower(__CLASS__), 'add', $id);

		return Router::get_code(201);
  }

	public static function edit ($data)
	{
		if (empty($data['id'])) 
			return Router::get_code(400);

		$item = Db::connect('users')->select('id, img')->where(['id' => (int) $data['id']])->get();

		if ($item == null)
			return Router::get_code(400);

		if ((UPLOAD_ERR_OK === $_FILES['image']['error'])) {
			if (!empty($item->img)) foreach (json_decode($item->img) as $img) Images::remove($img);

			$filename = explode('.', $_FILES['image']['name']);

			$data['img'] = json_encode([
				'fullsize' => $fullsize = '/img/' . $GLOBALS['router']->getController() . '/fullsize/' . str_replace(' ', '_', date('ymdHis')) . '.' . $filename[1],
				'thumb' => $thumb = '/img/' . $GLOBALS['router']->getController() . '/thumb/' . str_replace(' ', '_', date('ymdHis')) . '.' . $filename[1]
			]);

			Images::upload($_FILES['image']['tmp_name'], ROOT . $fullsize, $filename[1], null, 250);
			Images::upload($_FILES['image']['tmp_name'], ROOT . $thumb, $filename[1], null, 90);
		}

		if (isset($_POST['deleteImage']) && $_POST['deleteImage'] == "on") {
			if (!empty($item->img)) foreach (json_decode($item->img) as $img) Images::remove($img);
		}

		Db::connect('users')->where(['id' => (int) $item->id])->update($data);
		Log::set($_SESSION['user_id'], 'Edit user', strtolower(__CLASS__), 'edit', $item->id);

		return Router::get_code(200);
	}

	public static function delete ($data)
	{
		if ($data['id'] == null)
      return Router::get_code(400);

		$item = Db::connect('users')->select('id, img')->where(['id' => (int) $data['id']])->get();
		if ($item == null)
			return Router::get_code(404);

		if (!empty($item->img)) foreach (json_decode($item->img) as $img) Images::remove($img);

		Db::connect('users')->where(['id' => (int) $item->id])->delete();
		Log::set($_SESSION['user_id'], 'Removed user', strtolower(__CLASS__), 'delete', $item->id);
		return Router::get_code(200);
	}

	public static function clone ($data)
	{
		if (empty($data['id']))
			return Router::get_code(400);

		if (!$old_item = Db::connect('users')->where(['id' => (int) $data['id']])->get())
			return Router::get_code(404);

		unset($old_item->id);
		$item = $old_item;

		if(!empty($old_item->img))
		{
			$old_fullsize = json_decode($old_item->img, true)['fullsize'];
			$old_thumb = json_decode($old_item->img, true)['thumb'];
			$filename = str_replace(' ', '_', uniqid(rand()));

			$item->img = json_encode([
				'fullsize' => $fullsize = '/img/users/fullsize/' . $filename . '.png',
				'thumb' => $thumb = '/img/users/thumb/' . $filename . '.png'
			]);

			Images::upload(ROOT . $old_fullsize, ROOT . $fullsize, 'png', null, 250);
			Images::upload(ROOT . $old_thumb, ROOT . $thumb, 'png', null, 90);
		}

		$id = Db::connect('users')->insert((array) $item);
		Log::set($_SESSION['user_id'], 'Clone user', strtolower(__CLASS__), 'clone', $id);

		return Router::get_code(201);
	}
}