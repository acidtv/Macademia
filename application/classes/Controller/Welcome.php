<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller {

	public function action_index()
	{
		$this->response->body('<h1>Macademia</h1> <a href="/api/mpd/getstatus">status</a>');
	}

} // End Welcome
