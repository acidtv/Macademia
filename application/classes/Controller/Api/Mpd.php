<?

class Controller_Api_Mpd extends Controller {

	private $model_mpd = null;

	public function before()
	{
		$mpd = new Mpd();
		$this->model_mpd = new Model_Mpd($mpd);
	}

	public function action_getcurrentsong()
	{
		$response = $this->model_mpd->currentsong();
		$this->response->body(json_encode($response));	
	}

	public function action_getstatus()
	{
		$response = $this->model_mpd->status();
		$this->response->body(json_encode($response));	
	}

	public function action_chain()
	{
		$response = $this->model_mpd
			->command_list_ok_begin()
			->status()
			->currentsong()
			->command_list_end();
		$this->response->body(json_encode($response));	
	}
}