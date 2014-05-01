<?php
Application::import('Desmond::Controller::IController.php');
	class Controller implements IController {
		protected $variables = array();
		protected $viewname = "";
		protected $restful = true;

		
		public $request = null;
		public $response = null;

		/* setup the controller */
		public function Init() {

			/* request and response */
			$this->request = HTTPRequest::instance();

			$this->response = HTTPResponse::instance();

		}

		/* allows you to set variables for the controller */
		public function Set($data = array()) {
			$this->variables = $this->variables + $data;
		}

		/* returns content to the screen */
		public function Render() {

			/* render headers */
			if(!headers_sent()) {
				foreach($this->response->GetHeaders() as $key => $value) {

					header("{$key}: {$value}");
				}
			}

			/* render twig template if we want one */

			if($this->response->GetContent() == "") {

				/* so no content set yet must be a view render */
				
				/* templates work by using either automatically rendering the view or using your override */
				if($this->viewname == '') {

					/* load lang files */
					Language::LoadFile(strtolower($this->request->Controller()) . '::' . 
						$this->request->Action());

					/* auto render */
					Template::Render(strtolower($this->request->Controller()) . '::' . 
						$this->request->Action() . '.html',$this->variables);
				}

				else {
						Template::Render(strtolower($this->viewname) . '.html',$this->variables);
				}
			}

			else {
				/* let's render content to browser */
				echo $this->response->GetContent();
			}
		}

		/* sets if the controller is restful or not, controllers are restful by default */
		public function Restful($value) {	
			$this->restful = $value;
		}

		/* override default template */
		public function SetView($name) {
			$this->viewname = $name;
		}

	public function ProcessActions() {
			$action = null;
			if($this->request->Action() != '') {

			if(!$this->restful) {
				$action = $this->request->Action() . '_action';
				if(!method_exists($this, $action)) {
					$action = 'index_action';
				}
				
			}

			else {
				$action = strtolower($this->request->HttpMethod()) . '_'.$this->request->Action();
				if(!method_exists($this, $action)) {
					/* check if 'any' method */
					$action = 'any_'.$this->request->Action();
					if(!method_exists($this, $action)) {
							$action = 'any_index';
					}
				}
			}
		}

		if($action == null) {

			if($this->restful) {
				$action = 'any_index';
			}

			else {
				$action = 'index_action';
			}
			
			$this->request->Action('index');
		}

		/* determine if action takes parameters */
		$classMethod = new ReflectionMethod($this,$action);
		$numargs = count($classMethod->getParameters());

		$args = array();
					
					
		if($numargs > 0) {
		/* break up pathinfo */
		$path_args = explode('/', $_SERVER['PATH_INFO']);
		/* remove whitespace from pathinfo */
		foreach($path_args as $key => $value) {
			if($path_args[$key] == '') {
				unset($path_args[$key]);
			}
		}

		$path_args = array_values($path_args);

		/* remove controller and action */

		if($action != 'any_index') {
			unset($path_args[1]);
		}

		unset($path_args[0]);

		$path_args = array_values($path_args);
						
		for($i = 0; $i <= $numargs; $i++) {
							
			if(isset($path_args[$i])) {
				$args[] = $path_args[$i];
			}
								
			else {
				$args[] = null;
				}
			}

			try {
				$result = call_user_func_array(array( $this, $action ), $args);
			}

			catch( ErrorException $e) {
				$result = call_user_func_array(array( $this, 'missing' ), $args); 
			}
		}
		
		else {

			$result = $this->$action();


		}

	/* determine content type and store in response object */
	$this->setActionContent($result);
 }	

 public function setActionContent($result) {
 	if($this->response->GetContent() == '') {
		if(is_array($result)) {
			$this->Set($result);
		}

		else if($result instanceof IModel) {
			$this->response->SetContentType('application/json');
			$this->response->SetContent($result->GetJSON());
		}

		else if(is_object($result)) {
			$this->response->SetContentType('application/json');
			$this->response->SetContent(json_encode($result));
		}

		else if(json_decode($result) != null) {
				$this->response->SetContentType('application/json');
				$this->response->SetContent($result);
		}

		else {
			$this->response->SetContentType('text/html');
			$this->response->SetContent($result);
		}
	}
 }

}

?>