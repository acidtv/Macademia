<?

/**
 * Parser for commands that don't expect anything to return
 * apart from an OK or ACK.
 */
class Mpd_Parser_None extends Mpd_Parser {
	public function parse($data) 
	{
		if ($this->is_eof($data))
		{
			// finished parsing
			return array('result' => $data, 'data' => array());
		}
	}
}