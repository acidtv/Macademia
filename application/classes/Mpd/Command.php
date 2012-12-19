<?

interface Mpd_Command {
	public function get_command();
	public function parse($data);
}