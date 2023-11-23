<?php
class HomepageController
{
  public function index()
  {
    return [
      'goods' => Db::connect('goods')->getAll(),
      'categories' => Db::connect('categories')->getAll(),
      'reviews' => Db::connect('reviews')->getAll(),
    ];
  }

  public function cabinet_index()
  {
    App::check_auth($redirect = CI);

    $limit = $_COOKIE['items_per_page'] ?: DEFAULT_ITEMS_PER_PAGE;
    $offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;
    $where = [];

    return [];
  }

  public function admin_index()
  {
    App::check_auth($redirect = CI);
    App::check_permission_manual(['admin']);

    $limit = (int) $_COOKIE['admin_items_per_page'] ?: ADMIN_ITEMS_PER_PAGE;
    $offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;
    $where = [];

    return [];
  }

  function api_index()
  {
    App::check_auth();

    $data = Storage::get_default([]);
    unset($data['controller_info']);

    echo Formatting::json($data);
  }
}
