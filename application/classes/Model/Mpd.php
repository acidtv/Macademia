<?

class Model_Mpd {

	private $mpd = null;

	private $command_list = null;

	public function __construct(Mpd $mpd)
	{
		$this->mpd = $mpd;
	}

	private function _execute(Mpd_Command $command)
	{
		if ( ! $command instanceof Mpd_Command_List && ! $this->command_list)
		{
			// not starting a list, and no commands scheduled 
			return $this->_execute_send($command);
		}

		if ($command instanceof Mpd_Command_List)
		{
			if ($this->command_list)
			{
				// end of command list
				return $this->_execute_send($this->command_list);
			}
			else
				// start new list
				$this->command_list = $command;
		}
		else
		{
			// add to list of commands
			$this->command_list->add($command);
		}

		return $this;
	}

	/**
	 * Send command and reset model
	 */
	private function _execute_send(Mpd_Command $command)
	{
		$result = $this->mpd->send_command($command);
		$this->_reset();
		return $result;
	}

	/**
	 * Shortcut to create new command object
	 */
	private function _command($command, $parser)
	{
		return new Mpd_Command_Single($command, $parser);
	}

	public function _reset()
	{
		$this->command_list = null;
		return $this;
	}

	/***********/

	public function command_list_begin()
	{
		return $this->_execute(new Mpd_Command_List());
	}

	public function command_list_ok_begin()
	{
		return $this->_execute(new Mpd_Command_List_Ok());
	}

	public function command_list_end()
	{
		return $this->_execute($this->command_list);
	}

	/**
	 * Turn off an output
	 */
	public function disableoutput($outputid = 0)
	{
		$outputid = intval($outputid);
		return $this->_execute($this->_command('disableoutput', new Mpd_Parser_None)->set_params($outputid));
	}

	/**
	 * Enable an output
	 */
	public function enableoutput($outputid = 0)
	{
		$outputid = intval($outputid);
		return $this->_execute($this->_command('enableoutput', new Mpd_Parser_None)->set_params($outputid));
	}

	public function kill()
	{
	}

	public function update($path)
	{
	}

	public function currentsong()
	{
		return $this->_execute($this->_command('currentsong', Mpd_Parser::factory('dict')));
	}

	public function status()
	{
		return $this->_execute($this->_command('status', Mpd_Parser::factory('dict')));
	}

}
