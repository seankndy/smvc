<?php
namespace SeanKndy\SMVC;

use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Psr7\Response;

class Controller
{
    protected $response;
    protected $view;
    protected $app;
    protected $session;
	
    public function __construct() {
        $this->app = Application::instance();
		$this->session = $this->app->getSession();
        $this->response = new Response();
        $this->view = new View($this->response);
    }
    
    public function getRequest() {
        return $this->app->getRequest();
    }
    
    // used to instantiate models.
    /*
    public function __get($var) {
        if (isset($this->models[$var]))
            return $this->models[$var];
        $modelClass = $this->app->config('namespace') . '\\' . $var . 'Model';
        if ($model = Util::instantiateModel($var, $this->db)) {
            $this->models[$var] = $model;
            return $this->models[$var];
        }
        return null;
    }
    */
}
