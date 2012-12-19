<?

class Mpd_Command_List implements Mpd_Command {

	protected $type = 'command_list_begin';

	protected $commands = array();

	protected $result = array();

	/**
	 * Return aggregates command strings
	 */
	public function get_command()
	{
		$this->result = array();
		$commandstring = $this->type;

		foreach ($this->commands as $commandobj)
		{
			$commandstring .= "\n" . $commandobj->get_command();
		}

		$commandstring .= "\ncommand_list_end";

		return $commandstring;
	}

	public function parse($data)
	{
		throw new Exception('Not implemented yet');
	}

	public function add(Mpd_Command_Single $command)
	{
		$this->commands[] = $command;
		return $this;
	}
}