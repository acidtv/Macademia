<?

/**
 * Class to implement the MPD command_list_ok_begin command.
 * Output from this command includes the results for each command. This makes
 * for easier parsing.
 */
class Mpd_Command_List_Ok extends Mpd_Command_List {
	/**
	 * The MPD list type
	 */
	protected $type = 'command_list_ok_begin';

	/**
	 * Currently parsing command
	 */
	private $current = 0;

	/**
	 * Parse data to current command for parsing
	 */
	public function parse($data)
	{
		if ($data == 'OK')
		{
			// finished parsing subcommand results, return data
			return $this->result;
		}

		if ( ! array_key_exists($this->current, $this->commands))
			throw new Exception('Ran out of commands before end of stream');

		$command = $this->commands[$this->current];

		if ($data == 'list_OK')
		{
			// convert to OK so subcommand parser knows it's finished
			$data = 'OK';
		}

		if ($result = $command->parse($data))
		{
			// current command is finished parsing data, move on to the next
			$this->result[$command->get_command_name()] = $result;
			$this->current++;
		}

		// continue
		return false;
	}
}