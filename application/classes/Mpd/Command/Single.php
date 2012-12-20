<?

class Mpd_Command_Single implements Mpd_Command {
	/**
	 * Parser object to parse output for this command
	 */
	private $parser = null;

	/**
	 * Command name
	 */
	private $command = null;

	/**
	 * Command parameters
	 */
	private $params = array();

	public function __construct($command, Mpd_Parser $parser)
	{
		$command = trim((string)$command);

		if ( ! $command)
			throw new Exception('Invalid command');

		$this->command = $command;
		$this->parser = $parser;
	}

	/**
	 * Returns command including params to send to MPD
	 */
	public function get_command()
	{
		$params = '';

		if ($this->params)
			$params = ' ' . implode(' ', $this->params);

		return $this->command . $params;
	}

	/**
	 * Returns only command name without params
	 */
	public function get_command_name()
	{
		return $this->command;
	}

	/**
	 * Set command params
	 */
	public function set_params()
	{
		$this->params = func_get_args();
		return $this;
	}

	/**
	 * Passthrough data to parser
	 */
	public function parse($data)
	{
		return $this->parser->parse($data);
	}
}