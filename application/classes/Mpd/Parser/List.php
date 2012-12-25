<?

/**
 * Parse returned data into an associative array
 */
class Mpd_Parser_List extends Mpd_Parser_Dict {
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
			$this->cache[$matches['key']][] = $matches['value'];
		}

		// not finished parsing this command's results yet
		return false;
	}
}
