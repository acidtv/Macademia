<?

class Controller_Api_Mpd extends Controller {

	private $model_mpd = null;

	public function before()
	{
		$mpd = new Mpd();
		$this->model_mpd = new Model_Mpd($mpd);
	}

	public function execute()
	{
		// Execute the "before action" method
		$this->before();

		// Determine the action to use
		$action = 'action_'.$this->request->action();

		// If the action doesn't exist, pass through MPD
		if ( ! method_exists($this, $action))
		{
			$this->action_passthrough();
		}
		else
		{
			// Execute the action itself
			$this->{$action}();
		}

		// Execute the "after action" method
		$this->after();

		// Return the response
		return $this->response;
	}

	/**
	 * Passes command through MPD
	 */
	private function action_passthrough()
	{
		$action = $this->request->action();
		$methods = $this->get_methods();

		// Use filtered method array to check for access
		if ( ! in_array($action, array_keys($methods)))
			throw new HTTP_Exception_404('Method does not exist');

		$args = array();
		foreach ($methods[$action]->getParameters() as $param)
		{
			$args[$param->name] = $this->request->query($param->name);
		}

		$method = new ReflectionMethod($this->model_mpd, $action);
		$result = $method->invokeArgs($this->model_mpd, $args);

		return $this->response->body(json_encode($result));
	}

	/**
	 * Display API interface
	 */
	public function action_index()
	{
		$methods = $this->get_methods();

		foreach ($methods as $key => $method)
		{
			$methods[$key] = array(
				'name' => $method->name,
				'doc_comment' => Kodoc::parse($method->getDocComment(), true),
				'params' => $method->getParameters(),
				);
		}

		$view = View::factory('api/mpd');
		$view->methods = $methods;
		$this->response->body($view);
	}

	/**
	 * Return available mpd methods
	 */
	private function get_methods()
	{
		$class = new ReflectionClass('Model_Mpd');
		$methods_unfiltered = $class->getMethods(ReflectionMethod::IS_PUBLIC);
		$methods = array();

		foreach ($methods_unfiltered as $method)
		{
			if (substr($method->name, 0, 1) == '_') 
				continue;

			$methods[$method->name] = $method;
		}

		return $methods;
	}
}