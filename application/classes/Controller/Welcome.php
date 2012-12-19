<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller {

	public function action_index()
	{
		$mpd = new Model_Mpd();

		$currentsong = $mpd->currentsong();
		
		$this->response->body('hello, world!');
	}

} // End Welcome
