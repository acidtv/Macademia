<?

abstract class Mpd_Parser {
	/**
	 * Inheriting objects need to implement parse()
	 */
	abstract public function parse($response);

	/**
	 * Create new parser object of $type
	 */
	static function factory($type)
	{
		$class = 'Mpd_Parser_' . ucfirst($type);
		return new $class();
	}

	/**
	 * Test if output is finished
	 */
	protected function is_eof($data)
	{
		$data = trim($data);

		if ($data == 'OK' || substr($data, 0, 3) == 'ACK')
			return $data;

		return false;
	}
}