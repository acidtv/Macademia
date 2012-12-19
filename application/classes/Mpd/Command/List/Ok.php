<?

class Mpd_Command_List_Ok extends Mpd_Command_List {

	protected $type = 'command_list_ok_begin';

	/**
	 * Currently parsing command
	 */
	private $current = 0;

	public function parse($data)
	{
		if ($data == 'OK')
		{
			// finished parsing subcommand results, return data
			return $this->result;
		}

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

			if ( ! array_key_exists($this->current, $this->commands))
				throw new Exception('Ran out of commands before end of stream');
		}

		// continue
		return false;
	}
}