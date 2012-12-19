<?

class Mpd_Command_Single implements Mpd_Command {
	private $parser = null;

	private $command = null;

	private $params = array();

	public function __construct($command, Mpd_Parser $parser)
	{
		$command = trim((string)$command);

		if ( ! $command)
			throw new Exception('Invalid command');

		$this->command = $command;
		$this->parser = $parser;
	}

	public function get_command()
	{
		//FIXME include params
		return $this->command;
	}

	public function get_command_name()
	{
		//FIXME include params
		return $this->command;
	}

	public function set_params()
	{
		$this->params = func_get_args();
		return $this;
	}

	public function parse($data)
	{
		return $this->parser->parse($data);
	}
}