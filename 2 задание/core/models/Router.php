<?php
class Router{
	protected $url;
  protected $method_prefix;
  protected $controller;
	protected $action;
	protected $sef;
	protected $params;
	protected $route;

	public function getUrl(){
		return $this->url;
  }

  public function getMethodPrefix(){
		return $this->method_prefix;
  }

	public function getController(){
		return $this->controller;
  }

	public function getAction(){
		return $this->action;
	}

	public function getSef(){
		return $this->sef;
  }

	public function getParams(){
		return $this->params;
	}

	public function getRoute(){
		return $this->route;
  }

	public function __construct($url)
	{
    $routes = [
      'default' => '',
			'api' => 'api_',
      'cabinet' => 'cabinet_',
			'admin' => 'admin_',
    ];
    
		$this->url = parse_url(urldecode(trim($url, '/')));
		$this->route = TEMPLATE;
		$this->method_prefix = isset($routes[$this->route]) ? $routes[$this->route] : '';
		$this->controller = DEFAULT_CONTROLLER;
		$this->action = DEFAULT_ACTION;
		$this->sef = '';

		$path_parts = explode('/', $this->url['path']);

		if (count($path_parts)) {
			if (in_array(strtolower(current($path_parts)), array_keys($routes))) {
				$this->route = strtolower(current($path_parts));
				$this->method_prefix = isset($routes[$this->route]) ? $routes[$this->route] : '';
				array_shift($path_parts);
			}

			if (current($path_parts)) {
				$this->controller = strtolower(current($path_parts));
				array_shift($path_parts);
			}

			if (current($path_parts)) {
				$this->action = strtolower(current($path_parts));
				array_shift($path_parts);
			}

			if (current($path_parts)) {
				$this->sef = strtolower(current($path_parts));
				array_shift($path_parts);
			}

			parse_str($this->url['query'], $this->params);
		}
	}

	public static function redirect($location)
	{
		$code = 301;
		$status = self::get_code($code);

		header("Access-Control-Allow-Origin: *");
		header($status['protocol'] . ' ' . $status['code'] . ' ' . $status['message']);
		header('Status: ' . ' ' . $status['code'] . ' ' . $status['message']);

		header("Location: $location", true, $code);
		die();
	}

	public static function redirect_back()
	{
		self::redirect($_SERVER['HTTP_REFERER'] ? : '//' . $_SERVER['SERVER_NAME']);
	}

	public static function get_code ($code = 200, $redirect = false)
	{
		switch ($code) {
			case 100: $message = Lang::get('codes', 'Continue'); break;
			case 101: $message = Lang::get('codes', 'Switching Protocols'); break;

			case 200: $message = Lang::get('codes', 'OK'); break;
			case 201: $message = Lang::get('codes', 'Created'); break;
			case 202: $message = Lang::get('codes', 'Accepted'); break;
			case 203: $message = Lang::get('codes', 'Non-Authoritative Information'); break;
			case 204: $message = Lang::get('codes', 'No Content'); break;
			case 205: $message = Lang::get('codes', 'Reset Content'); break;
			case 206: $message = Lang::get('codes', 'Partial Content'); break;

			case 300: $message = Lang::get('codes', 'Multiple Choices'); break;
			case 301: $message = Lang::get('codes', 'Moved Permanently'); break;
			case 302: $message = Lang::get('codes', 'Moved Temporarily'); break;
			case 303: $message = Lang::get('codes', 'See Other'); break;
			case 304: $message = Lang::get('codes', 'Not Modified'); break;
			case 305: $message = Lang::get('codes', 'Use Proxy'); break;

			case 400: $message = Lang::get('codes', 'Bad Request'); break;
			case 401: $message = Lang::get('codes', 'Unauthorized'); break;
			case 402: $message = Lang::get('codes', 'Payment Required'); break;
			case 403: $message = Lang::get('codes', 'Forbidden'); break;
			case 404: $message = Lang::get('codes', 'Page Not Found'); break;
			case 405: $message = Lang::get('codes', 'Method Not Allowed'); break;
			case 406: $message = Lang::get('codes', 'Not Acceptable'); break;
			case 407: $message = Lang::get('codes', 'Proxy Authentication Required'); break;
			case 408: $message = Lang::get('codes', 'Request Time-out'); break;
			case 409: $message = Lang::get('codes', 'Conflict'); break;
			case 410: $message = Lang::get('codes', 'Gone'); break;
			case 411: $message = Lang::get('codes', 'Length Required'); break;
			case 412: $message = Lang::get('codes', 'Precondition Failed'); break;
			case 413: $message = Lang::get('codes', 'Request Entity Too Large'); break;
			case 414: $message = Lang::get('codes', 'Request-URI Too Large'); break;
			case 415: $message = Lang::get('codes', 'Unsupported Media Type'); break;

			case 500: $message = Lang::get('codes', 'Internal Server Error'); break;
			case 501: $message = Lang::get('codes', 'Not Implemented'); break;
			case 502: $message = Lang::get('codes', 'Bad Gateway'); break;
			case 503: $message = Lang::get('codes', 'Service Unavailable'); break;
			case 504: $message = Lang::get('codes', 'Gateway Time-out'); break;
			case 505: $message = Lang::get('codes', 'HTTP Version not supported'); break;

			default:
				exit('Unknown http status code "' . htmlentities($code) . '"');
		}

		$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
		header($protocol . ' ' . $code . ' ' . $message);
		header('Status: ' . $code . ' ' . $message);

		if($redirect == true)
			self::redirect('/errors/?code=' . $code);

		return ['protocol' => $protocol, 'code' => $code, 'message' => $message];
	}
}
