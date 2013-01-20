<?

class Mpd {

	private $sock = null;

	private $conn_errno = 0;

	private $conn_errstr = '';

	public function __construct()
	{
		$this->connect();
	}

	public function connect()
	{
		// $this->sock = fsockopen('127.0.0.1', '6600', $this->conn_errno, $this->conn_errstr, 5);
		$this->sock = fsockopen('192.168.178.40', '6600', $this->conn_errno, $this->conn_errstr, 5);

		if ($this->conn_errstr)
		{
			throw new Exception('Failed to connect: ' . $this->conn_errstr);
		}
	}

	public function send_command(Mpd_Command $command)
	{
		$commandstring = $command->get_command() . "\n";

	    if ( ! fwrite($this->sock, $commandstring))
	    {
	    	throw new Exception('Failed to send command');
	    }

	    // read info string
		$buf = trim(fgets($this->sock));

	    while (!feof($this->sock)) 
	    {
			$buf = trim(fgets($this->sock));

			if ($result = $command->parse($buf))
			{
				fwrite($this->sock, 'close');
			    fclose($this->sock);
				return $result;
			}
	    }

	    throw new Exception('End of stream reached without finished command');
	}

}