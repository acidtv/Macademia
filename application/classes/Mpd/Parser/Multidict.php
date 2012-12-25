<?

class Mpd_Parser_Multidict extends Mpd_Parser_Dict
{
	/**
	 * Indicates at which key a new item is started
	 */
	private $sep = 'file';

	/**
	 * Set new separator
	 */
	public function sep($sep)
	{
		$this->sep = $sep;
	}

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
			$count = count($this->cache);

			if ($matches['key'] == $this->sep)
			{
				$count++;
			}

			$this->cache[$count][$matches['key']] = $matches['value'];
		}

		// not finished parsing this command's results yet
		return false;
	}
}