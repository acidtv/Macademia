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
		return $this->_execute($this->_command('disableoutput', Mpd_Parser::factory('none'))
			->set_params($outputid));
	}

	/**
	 * Enable an output
	 */
	public function enableoutput($outputid = 0)
	{
		$outputid = intval($outputid);
		return $this->_execute($this->_command('enableoutput', Mpd_Parser::factory('none'))
			->set_params($outputid));
	}

	/**
	 * Stop MPD
	 */
	public function kill()
	{
		return $this->_execute($this->_command('kill', Mpd_Parser::factory('none')));
	}

	/**
	 * Scans the music directory as defined in the MPD 
	 * configuration file's music_directory setting. 
	 * Adds new files and their metadata (if any) to the 
	 * MPD database and removes files and metadata from 
	 * the database that are no longer in the directory.
	 */
	public function update($path = '')
	{
		return $this->_execute($this->_command('update', Mpd_Parser::factory('dict'))
			->set_params($path));
	}

	/**
	 * Report the current status of MPD, and volume level
	 */
	public function status()
	{
		return $this->_execute($this->_command('status', Mpd_Parser::factory('dict')));
	}

	/**
	 * Displays statistics.
	 */
	public function stats()
	{
		return $this->_execute($this->_command('stats', Mpd_Parser::factory('dict')));
	}

	/**
	 * Show information about all outputs
	 */
	public function outputs()
	{
		throw new Exception('Not implemented yet');
		// return $this->_execute($this->_command('outputs', Mpd_Parser::factory('outputs')));
	}

	/**
	 * Show which commands the current user has access to.
	 */
	public function commands()
	{
		return $this->_execute($this->_command('commands', Mpd_Parser::factory('list')));
	}

	/**
	 * Show which commands the current user does not have access to.
	 */
	public function notcommands()
	{
		return $this->_execute($this->_command('notcommands', Mpd_Parser::factory('list')));
	}

	/**
	 * Get a list of available URL handlers.
	 */
	public function tagtypes()
	{
		return $this->_execute($this->_command('tagtypes', Mpd_Parser::factory('list')));
	}

	/**
	 * Get a list of available URL handlers.
	 */
	public function urlhandlers()
	{
		return $this->_execute($this->_command('urlhandlers', Mpd_Parser::factory('list')));
	}

	/**
	 * Finds songs in the database with a case sensitive, exact match to <string what>.
	 */
	public function find($type, $what)
	{
		return $this->_execute($this->_command('find', Mpd_Parser::factory('dict'))
			->set_params(array($type, $what)));
	}

	/**
	 * List all metadata of <metadata arg1>.
	 */
	public function _list($arg1, $arg2, $search = '')
	{
		throw new Exception('FIXME');
		return $this->_execute($this->_command('list', Mpd_Parser::factory('list'))
			->set_params($arg1, $arg2, $search));
	}

	/**
	 * Lists all directories and filenames in <string path> recursively.
	 */
	public function listall($path = '')
	{
		return $this->_execute($this->_command('listall', Mpd_Parser::factory('list'))
			->set_params($path));
	}

	/**
	 * Lists all information in database about all music files in <string path> recursively.
	 */
	public function listallinfo($path)
	{
		throw new Exception('Not implemented yet');
		return $this->_execute($this->_command('listallinfo', Mpd_Parser::factory('list'))
			->set_params($path));
	}

	/**
	 * List contents of <string directory>, from the database.
	 */
	public function lsinfo($directory)
	{
		return $this->_execute($this->_command('lsinfo', Mpd_Parser::factory('list'))
			->set_params($directory));
	}

	/**
	 * Finds songs in the database with a case insensitive match to <string what>.
	 */
	public function search($type, $what)
	{
		throw new Exception('Not implemented yet.');
		return $this->_execute($this->_command('commands', Mpd_Parser::factory('list'))
			->set_params($type, $what));
	}

	/**
	 * Retrieve the number of songs and their total playtime in the database matching <query>.
	 */
	public function count($scope, $query)
	{
		return $this->_execute($this->_command('commands', Mpd_Parser::factory('list'))
			->set_params($scope, $query));
	}

	/**
	 * Add a single file from the database to the end of the playlist. 
	 * This command increments the playlist version by 1 for 
	 * each song added to the playlist.
	 */
	public function add($file)
	{
		return $this->_execute($this->_command('add', Mpd_Parser::factory('none'))
			->set_params($file));
	}

	/**
	 * Like add but returns a playlistid.
	 */
	public function addid($file, $position = '')
	{
		return $this->_execute($this->_command('addid', Mpd_Parser::factory('dict'))
			->set_params($file, $position));
	}

	/**
	 * Clears the current playlist, increment the playlist version by 1.
	 */
	public function clear()
	{
		return $this->_execute($this->_command('clear', Mpd_Parser::factory('none')));
	}

	/**
	 * Displays the metadata of the current song.
	 */
	public function currentsong()
	{
		return $this->_execute($this->_command('currentsong', Mpd_Parser::factory('dict')));
	}

	/**
	 * Delete <int song> from the playlist, increment the playlist version by 1.
	 */
	public function delete($song)
	{
		return $this->_execute($this->_command('delete', Mpd_Parser::factory('none'))
			->set_params($song));
	}

	/**
	 * Delete song with <int songid> from playlist, increment the playlist version by 1.
	 */
	public function deleteid($song)
	{
		return $this->_execute($this->_command('commands', Mpd_Parser::factory('list'))
			->set_params($song));
	}

	/**
	 * Load the playlist <string_name> from the playlist directory, 
	 * increment the playlist version by the number of songs added.
	 */
	public function load($playlist)
	{
		return $this->_execute($this->_command('load', Mpd_Parser::factory('none'))
			->set_params($playlist));
	}

	/**
	 * Rename the playlist name to new_name.
	 */
	public function rename($name, $new_name)
	{
		return $this->_execute($this->_command('rename', Mpd_Parser::factory('none'))
			->set_params($name, $new_name));
	}

	/**
	 * Move song at <int from> to <int to> in the playlist, 
	 * increment the playlist version by 1.
	 */
	public function move($from, $to)
	{
		return $this->_execute($this->_command('move', Mpd_Parser::factory('none'))
			->set_params($from, $to));
	}

	/**
	 * Move song <int songid from> to <int to> in the playlist, 
	 * increment the playlist version by 1.
	 */
	public function moveid($from, $to)
	{
		return $this->_execute($this->_command('moveid', Mpd_Parser::factory('none'))
			->set_params($from, $to));
	}

	/**
	 * Display metadata for songs in the playlist.
	 */
	public function playlistinfo($song = '')
	{
		return $this->_execute($this->_command('playlistinfo', Mpd_Parser::factory('dict'))
			->set_params($song));
	}

	/**
	 * Display metadata for songs in the playlist.
	 */
	public function playlistid($song = '')
	{
		return $this->_execute($this->_command('playlistid', Mpd_Parser::factory('dict'))
			->set_params($song));
	}

	/**
	 * Displays changed songs currently in the playlist since <playlist version>.
	 */
	public function plchanges($version)
	{
		return $this->_execute($this->_command('plchanges', Mpd_Parser::factory('list'))
			->set_params($version));
	}

	/**
	 * Displays changed songs currently in the 
	 * playlist since <playlist version>, but 
	 * only return the position and the id.
	 */
	public function plchangesposid($version)
	{
		return $this->_execute($this->_command('plchangesposid', Mpd_Parser::factory('list'))
			->set_params($version));
	}

	/**
	 * Removes the <string playlist name> from the playlist directory.
	 */
	public function rm($name)
	{
		return $this->_execute($this->_command('rm', Mpd_Parser::factory('none'))
			->set_params($name));
	}

	/**
	 * Saves the current playlist to <string playlist name> in the playlist directory.
	 */
	public function save($name)
	{
		return $this->_execute($this->_command('save', Mpd_Parser::factory('none'))
			->set_params($name));
	}

	/**
	 * Shuffles the current playlist, increments playlist version by 1.
	 */
	public function shuffle()
	{
		return $this->_execute($this->_command('shuffle', Mpd_Parser::factory('none')));
	}

	/**
	 * Swap positions of <int song1> and <int song2>, increments playlist version by 1.
	 */
	public function swap($song1, $song2)
	{
		return $this->_execute($this->_command('swap', Mpd_Parser::factory('none'))
			->set_params($song1, $song2));
	}

	/**
	 * Swap positions of songs by song id's of <songid1> and <songid2>, 
	 * increments playlist version by 1.
	 */
	public function swapid($song1, $song2)
	{
		return $this->_execute($this->_command('swapid', Mpd_Parser::factory('dict')));
	}

	/**
	 * List files in <playlist name>
	 */
	public function listplaylist($name)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('dict'))
			->set_params($name));
	}

	/**
	 * List songs in <playlist name>
	 */
	public function listplaylistinfo($name)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('fixme'))
			->set_params($name));
	}

	/**
	 * Add <path> to <playlist name>
	 */
	public function playlistadd($playlist, $path)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none'))
			->set_params($playlist, $path));
	}

	/**
	 * List files in <playlist name>
	 */
	public function listplaylist($name)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('dict'))
			->set_params($name));
	}

	/**
	 * List files in <playlist name>
	 */
	public function listplaylist($name)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('dict'))
			->set_params($name));
	}

	/**
	 * List files in <playlist name>
	 */
	public function listplaylist($name)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('dict'))
			->set_params($name));
	}

	/**
	 * List files in <playlist name>
	 */
	public function listplaylist($name)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('dict'))
			->set_params($name));
	}

	/**
	 * List files in <playlist name>
	 */
	public function listplaylist($name)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('dict'))
			->set_params($name));
	}

	/**
	 * List files in <playlist name>
	 */
	public function listplaylist($name)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('dict'))
			->set_params($name));
	}

	/**
	 * List files in <playlist name>
	 */
	public function listplaylist($name)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('dict'))
			->set_params($name));
	}

	/**
	 * List files in <playlist name>
	 */
	public function listplaylist($name)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('dict'))
			->set_params($name));
	}

	/**
	 * List files in <playlist name>
	 */
	public function listplaylist($name)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('dict'))
			->set_params($name));
	}

	/**
	 * List files in <playlist name>
	 */
	public function listplaylist($name)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('dict'))
			->set_params($name));
	}

	/**
	 * List files in <playlist name>
	 */
	public function listplaylist($name)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('dict'))
			->set_params($name));
	}

}
