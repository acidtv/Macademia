<?

class Model_Mpd {

	/**
	 * MPD Connection object
	 */
	private $mpd = null;

	/**
	 * Queued commands
	 */
	private $command_list = null;

	/**
	 * Available scope specifiers
	 */
	private $scopes = array('filename', 'any', 'artist', 'album', 'title', 'track', 'name', 'genre', 'date', 'composer', 'performer', 'comment', 'disc');

	public function __construct(Mpd $mpd)
	{
		$this->mpd = $mpd;
	}

	/**
	 * Sends commands to mpd.
	 * If a command list was started commands are queued and
	 * send to the server upon receiving a second mpd_command_list object.
	 */
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

	/**
	 * Dump all scheduled commands
	 */
	public function _reset()
	{
		$this->command_list = null;
		return $this;
	}

	/**
	 * Check if $scope is a valid scope specifier
	 */
	private function _check_scope($scope)
	{
		$scope = trim($scope);

		if ( ! $scope)
			return true;

		if ( ! in_array($scope, $this->scopes))
			throw new Mpd_Exception_Scope('Invalid scope specified: ' . $scope);

		return true;
	}

	/***********/

	/**
	 * Start a new command list. 
	 * Output will not be seperated by command or parsed in any other way.
	 */
	public function command_list_begin()
	{
		return $this->_execute(new Mpd_Command_List());
	}

	/**
	 * Start a new command list.
	 * Output will be grouped by command and parsed with the
	 * appropriate parser.
	 */
	public function command_list_ok_begin()
	{
		return $this->_execute(new Mpd_Command_List_Ok());
	}

	/**
	 * Marks the end of the command list and sends commands to mpd.
	 */
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
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none'))
			->set_params($outputid));
	}

	/**
	 * Enable an output
	 */
	public function enableoutput($outputid = 0)
	{
		$outputid = intval($outputid);
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none'))
			->set_params($outputid));
	}

	/**
	 * Stop MPD
	 */
	public function kill()
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none')));
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
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('dict'))
			->set_params($path));
	}

	/**
	 * Report the current status of MPD, and volume level
	 */
	public function status()
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('dict')));
	}

	/**
	 * Displays statistics.
	 */
	public function stats()
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('dict')));
	}

	/**
	 * Show information about all outputs
	 */
	public function outputs()
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('multidict')
			->sep('outputid')));
	}

	/**
	 * Show which commands the current user has access to.
	 */
	public function commands()
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('list')));
	}

	/**
	 * Show which commands the current user does not have access to.
	 */
	public function notcommands()
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('list')));
	}

	/**
	 * Get a list of available URL handlers.
	 */
	public function tagtypes()
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('list')));
	}

	/**
	 * Get a list of available URL handlers.
	 */
	public function urlhandlers()
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('list')));
	}

	/**
	 * Finds songs in the database with a case sensitive, exact match to <string what>.
	 */
	public function find($scope, $what)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('dict'))
			->set_params($scope, $what));
	}

	/**
	 * List all metadata of <metadata arg1>.
	 * FIXME: php doesn't support a list() method in a class
	 */
	public function getlist($scope1, $scope2 = '', $search = '')
	{
		return $this->_execute($this->_command('list', Mpd_Parser::factory('list'))
			->set_params($scope1, $scope1, $search));
	}

	/**
	 * Lists all directories and filenames in <string path> recursively.
	 */
	public function listall($path = '')
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('list'))
			->set_params($path));
	}

	/**
	 * Lists all information in database about all music files in <string path> recursively.
	 */
	public function listallinfo($path)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('multidict'))
			->set_params($path));
	}

	/**
	 * List contents of <string directory>, from the database.
	 */
	public function lsinfo($directory)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('list'))
			->set_params($directory));
	}

	/**
	 * Finds songs in the database with a case insensitive match to <string what>.
	 */
	public function search($scope, $what)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('multidict'))
			->set_params($scope, $what));
	}

	/**
	 * Retrieve the number of songs and their total playtime in the database matching <query>.
	 */
	public function count($scope, $what)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('dict'))
			->set_params($scope, $what));
	}

	/**
	 * Add a single file from the database to the end of the playlist. 
	 * This command increments the playlist version by 1 for 
	 * each song added to the playlist.
	 */
	public function add($file)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none'))
			->set_params($file));
	}

	/**
	 * Like add but returns a playlistid.
	 */
	public function addid($file, $position = '')
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('dict'))
			->set_params($file, $position));
	}

	/**
	 * Clears the current playlist, increment the playlist version by 1.
	 */
	public function clear()
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none')));
	}

	/**
	 * Displays the metadata of the current song.
	 */
	public function currentsong()
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('dict')));
	}

	/**
	 * Delete <int song> from the playlist, increment the playlist version by 1.
	 */
	public function delete($song)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none'))
			->set_params($song));
	}

	/**
	 * Delete song with <int songid> from playlist, increment the playlist version by 1.
	 */
	public function deleteid($song)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('list'))
			->set_params($song));
	}

	/**
	 * Load the playlist <string_name> from the playlist directory, 
	 * increment the playlist version by the number of songs added.
	 */
	public function load($playlist)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none'))
			->set_params($playlist));
	}

	/**
	 * Rename the playlist name to new_name.
	 */
	public function rename($name, $new_name)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none'))
			->set_params($name, $new_name));
	}

	/**
	 * Move song at <int from> to <int to> in the playlist, 
	 * increment the playlist version by 1.
	 */
	public function move($from, $to)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none'))
			->set_params($from, $to));
	}

	/**
	 * Move song <int songid from> to <int to> in the playlist, 
	 * increment the playlist version by 1.
	 */
	public function moveid($from, $to)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none'))
			->set_params($from, $to));
	}

	/**
	 * Display metadata for songs in the playlist.
	 */
	public function playlistinfo()
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('dict'))
			->set_params($song));
	}

	/**
	 * Display metadata for songs in the playlist.
	 */
	public function playlistid($song = '')
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('dict'))
			->set_params($song));
	}

	/**
	 * Displays changed songs currently in the playlist since <playlist version>.
	 */
	public function plchanges($version)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('list'))
			->set_params($version));
	}

	/**
	 * Displays changed songs currently in the 
	 * playlist since <playlist version>, but 
	 * only return the position and the id.
	 */
	public function plchangesposid($version)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('list'))
			->set_params($version));
	}

	/**
	 * Removes the <string playlist name> from the playlist directory.
	 */
	public function rm($name)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none'))
			->set_params($name));
	}

	/**
	 * Saves the current playlist to <string playlist name> in the playlist directory.
	 */
	public function save($name)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none'))
			->set_params($name));
	}

	/**
	 * Shuffles the current playlist, increments playlist version by 1.
	 */
	public function shuffle()
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none')));
	}

	/**
	 * Swap positions of <int song1> and <int song2>, increments playlist version by 1.
	 */
	public function swap($song1, $song2)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none'))
			->set_params($song1, $song2));
	}

	/**
	 * Swap positions of songs by song id's of <songid1> and <songid2>, 
	 * increments playlist version by 1.
	 */
	public function swapid($song1, $song2)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('dict')));
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
	 * Clear <playlist name>
	 */
	public function playlistclear($name)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none'))
			->set_params($name));
	}

	/**
	 * Delete <song id> from <playlist name>
	 */
	public function playlistdelete($name, $song)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none'))
			->set_params($name, $song));
	}

	/**
	 * Move <song id> in <playlist name> to <position>
	 */
	public function playlistmove($playlist, $from, $to)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none'))
			->set_params($playlist, $from, $to));
	}

	/**
	 * Search for songs in the current playlist with strict matching
	 */
	public function playlistfind($scope, $query)
	{
		$this->_check_scope($scope);

		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('FIXME'))
			->set_params($scope, $query));
	}

	/**
	 * Search case-insensitively with partial matches for songs in the current playlist
	 */
	public function playlistsearch($scope, $query)
	{
		$this->_check_scope($scope);

		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('FIXME'))
			->set_params($scope, $query));
	}

	/**
	 * Sets crossfading (mixing) between songs.
	 */
	public function crossfade($seconds)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none'))
			->set_params($seconds));
	}

	/**
	 * Plays next song in playlist.
	 */
	public function next()
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none')));
	}

	/**
	 * Toggle pause / resume playing.
	 */
	public function pause($pause)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none'))
			->set_params($pause));
	}

	/**
	 * Begin playing the playlist.
	 */
	public function play($song = '')
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none'))
			->set_params($song));
	}

	/**
	 * Begin playing playlist.
	 */
	public function playid($song)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none'))
			->set_params($song));
	}

	/**
	 * Plays previous song in playlist
	 */
	public function previous()
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none')));
	}

	/**
	 * Sets 'random' mode, which plays the playlist in a random order
	 */
	public function random($state)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none'))
			->set_params($state));
	}

	/**
	 * Repeat the playlist after all songs have been used.
	 */
	public function repeat($state)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none'))
			->set_params($state));
	}

	/**
	 * Skip to a specified point in a song on the playlist
	 */
	public function seek($song, $time = 0)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none'))
			->set_params($song, $time));
	}

	/**
	 * Skip to a specified point in a song on the playlist
	 */
	public function seekid($song, $time = 0)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none'))
			->set_params($song, $time));
	}

	/**
	 * Set the volume
	 */
	public function setvol($volume)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none'))
			->set_params($volume));
	}

	/**
	 * To halt playing
	 */
	public function stop()
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none')));
	}

	/**
	 * Clear the current error message in status 
	 * (this is also accomplished by any command that starts playback).
	 */
	public function clearerror()
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none'))
			->set_params($name));
	}

	/**
	 * Close the connection with the MPD host
	 */
	public function close()
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none')));
	}

	/**
	 * This is used for authentication with the server, 
	 * this is enabled or disabled by the administrator 
	 * in the MPD configuration file.
	 */
	public function password($password)
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none'))
			->set_params($password));
	}

	/**
	 * To return OK (basically to show some life)
	 */
	public function ping()
	{
		return $this->_execute($this->_command(__FUNCTION__, Mpd_Parser::factory('none')));
	}
}
