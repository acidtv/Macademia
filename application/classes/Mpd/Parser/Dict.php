<?

class Mpd_Parser_Dict extends Mpd_Parser {

	private $dict = array();

	public function parse($data)
	{
		if ($this->is_eof($data))
		{
			// finished parsing
			return array('result' => $data, 'data' => $this->dict);
		}

		$matches = array();
		preg_match('/([a-z\-]+)\: (.*)$/Ui', $data, $matches);

		if ($matches)
		{
			$this->dict[$matches[1]] = $matches[2];
		}

		// not finished parsing this command's results yet
		return false;
	}
}