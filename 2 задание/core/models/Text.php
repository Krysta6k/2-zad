<?php
class Text extends App
{
  private function __construct() { /* ... @return ClassName */ }  // We protect from creation through "new ClassName"
  private function __clone() { /* ... @return ClassName */ }  // We protect from creation through "cloning"
  private function __wakeup() { /* ... @return ClassName */ }  // We protect from creation through "unserialize"

  //Translit
	public static function translit($value, $type = null)
  {
		$converter = Storage::json('get', '/core/utilities/Text' , 'translit');

		switch ($type) {
			case 'sef':
				$value = mb_strtolower($value);
				$value = strtr($value, $converter);
				$value = mb_ereg_replace('[^-0-9a-z]', '-', $value);
				$value = mb_ereg_replace('[-]+', '-', $value);
				$value = trim($value, '-');	
				break;

			case 'filename':
				$value = mb_strtolower($value);
				$value = strtr($value, $converter);
				$value = mb_ereg_replace('[^-0-9a-z\.]', '_', $value);
				$value = mb_ereg_replace('[-]+', '_', $value);
				$value = trim($value, '_');	
				break;

			default:
				$value = strtr($value, $converter);
				break;
		}

    return $value;
  }

	// Crypting
	static function encrypting (string $string, string $key)
	{
		$ciphering = "BF-CBC";
		$iv_length = openssl_cipher_iv_length($ciphering);
		$encryption_iv = random_bytes($iv_length);
		$encryption_key = openssl_digest(php_uname(), 'MD5', TRUE);
		$encrypted = openssl_encrypt($string, $ciphering, $encryption_key, $options = 0, $encryption_iv);

		return base64_encode($encrypted) . '|' . base64_encode($encryption_iv);
	}

	static function decrypting (string $string, string $key)
	{
		$decrypt = explode('|', $string . '|');
		$ciphering = "BF-CBC";
		$decryption_key = openssl_digest(php_uname(), 'MD5', TRUE);

		return openssl_decrypt(base64_decode($decrypt[0]), $ciphering, $decryption_key, $options = 0, base64_decode($decrypt[1]));
	}

  public static function word_break ($string, $max_length = 55) {
    $regular = '~\S{'. $max_length .'}\S~si';
    $replace = "$1<wbr />";

    return preg_replace($regular, $replace, $string);
	}

	/**
	 * Склонение существительных после числительных.
	 * @use Text::num_word_lib(32, ['lang' => 'en', 'lib' => 'square', 'word' => 'hectare'], true);
	 * 
	 * @param array $params [lang, lib, word]
	 */
	public static function num_word_lib (float $value, array $params, bool $show = true)
	{
		$words = Storage::json('get', '/core/utilities/Text', 'num_word_'. ($params['lang'] ? : DEFAULT_LANG));

		return self::num_word($value, $words, $show);
	}

	public static function num_word (float $value, array $words, bool $show = true)
	{
		$num = $value % 100;

		if ($num > 19)
			$num = $num % 10;

		$out = ($show) ?  $value . ' ' : '';

		switch ($num) {
			case 1:
				$out .= $words[0];
				break;
			case 2:
			case 3:
			case 4:
				$out .= $words[1];
				break;
			default:
				$out .= $words[2];
				break;
		}

		return $out;
	}
}
