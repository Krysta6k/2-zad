<?php
class Formatting extends App
{
	private function __construct() { /* ... @return ClassName */ }  // We protect from creation through "new ClassName"
	private function __clone() { /* ... @return ClassName */ }  // We protect from creation through "cloning"
	private function __wakeup() { /* ... @return ClassName */ }  // We protect from creation through "unserialize"
  public static function phone (string $phone = '+79999999999', $type = 'clear')
	{
		switch ($type) 
		{
			case 'analog':
				if (preg_match('/^\+\d(\d{4})(\d{2})(\d{2})(\d{2})$/', $phone, $matches)) 
				{
					$code = substr($phone, 0, 2);
					return $code . ' (' . $matches[1] . ') ' . $matches[2] . '-' . $matches[3]. '-' . $matches[4];
				}
				break;

			case 'omni':
				if (preg_match('/^\+\d(\d{3})(\d{3})(\d{4})$/', $phone, $matches)) 
					return '8 (' . $matches[1] . ') ' . $matches[2] . ' ' . $matches[3];
				break;
			
			case 'mobile':
				if (preg_match('/^\+\d(\d{3})(\d{3})(\d{4})$/', $phone, $matches)) 
				{
					$code = substr($phone, 0, 2);
					return $code . ' (' . $matches[1] . ') ' . $matches[2] . ' ' . $matches[3];
				}
				break;

			case 'clear':
				return str_replace([' ', '(', ')', '-'], '', $phone);
		}
	}

	public static function date (string $date, string $format = 'd.m.Y H:i:s')
	{
		return date_format(date_create($date), $format);
	}

	public static function money (float $amount, string $currency = 'RUR', string $formatter = 'ru_RU')
	{
		$fmt = numfmt_create($formatter, NumberFormatter::CURRENCY);
		return str_replace('р.', '₽', numfmt_format_currency($fmt, $amount, $currency));
	}

	public static function json (array $data, int $code = 200, $flags = null)
	{
		$status = Router::get_code($code);

		header("Access-Control-Allow-Origin: *");
		header($status['protocol'] . ' ' . $status['code'] . ' ' . $status['message']);
		header('Status: ' . ' ' . $status['code'] . ' ' . $status['message']);
		header("Content-Type: application/json; charset=UTF-8");

		return json_encode(array_merge($status, ['data' => $data]), $flags);
	}
}
