<?php
class ErrorsController
{
  public function index ()
  {
    $code = (int) strip_tags($_GET['code']);
		$status = Router::get_code($code);

		header("Access-Control-Allow-Origin: *");
		header($status['protocol'] . ' ' . $status['code'] . ' ' . $status['message']);
		header('Status: ' . ' ' . $status['code'] . ' ' . $status['message']);
    http_response_code($code);

    return [
      'status' => $status,
      'seo' => [
        'title' => $status['message'],
        'robots' => [
          'i' => 'noindex',
          'f' => 'nofollow',
        ]
      ],
    ];
  }
}
