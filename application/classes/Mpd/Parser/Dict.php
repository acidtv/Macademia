<?

/**
 * Parse returned data into an associative array
 */
class Mpd_Parser_Dict extends Mpd_Parser {
	/**
	 * Holds parsed data until we're funished parsing
	 */
	private $dict = array();

	/**
	 * Parse returned data
	 */
	public function parse($data)
	{
		if ($this->is_eof($data))
		{
			// finished parsing
			return array('result' => $data, 'data' => $this->dict);
		}

		$matches = array();
		// match key and value pair
		preg_match('/([a-z\-]+)\: (.*)$/Ui', $data, $matches);

		if ($matches)
		{
			$this->dict[$matches[1]] = $matches[2];
		}

		// not finished parsing this command's results yet
		return false;
	}
}