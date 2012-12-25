<?

/**
 * Parse returned data into an associative array
 */
class Mpd_Parser_Dict extends Mpd_Parser {
	/**
	 * Holds parsed data until we're funished parsing
	 */
	protected $cache = array();

	/**
	 * Parse returned data
	 */
	public function parse($data)
	{
		if ($this->is_eof($data))
		{
			// finished parsing
			return array('result' => $data, 'data' => $this->cache);
		}

		$matches = $this->get_key_value($data);

		if ($matches)
		{
			$this->cache[$matches['key']] = $matches['value'];
		}

		// not finished parsing this command's results yet
		return false;
	}

	/**
	 * Get key/value pair from data
	 */
	protected function get_key_value($data)
	{
		$matches = array();
		// match key and value pair
		preg_match('/([a-z\-]+)\: (.*)$/Ui', $data, $matches);

		return array('key' => $matches[1], 'value' => $matches[2]);
	}
}