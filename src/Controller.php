<<<<<<< HEAD
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

    public function __construct(Application $app) {
        $this->app = $app;
		$this->session = $this->app->getSession();
        $this->response = new Response();
        $this->view = new View($this->app, $this->response);
    }

    public function getRequest() {
        return $this->app->getRequest();
    }

    public function redirectToRoute($name, array $args = []) {
        $this->response = new Response(302);
        $this->response = $this->response->withHeader('Location', $this->app->url($name, $args));
        return $this->response;
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
=======
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
    protected $models;

    public function __construct(Application $app) {
        $this->app = $app;
		$this->session = $this->app->getSession();
        $this->response = new Response();
        $this->view = new View($this->app, $this->response);
    }

    public function getRequest() {
        return $this->app->getRequest();
    }

    public function reqVar($var, $default = null) {
        $reqMethod = strtoupper($this->getRequest()->getMethod());
        if ($reqMethod == 'POST' || $reqMethod == 'PUT')
            $vars = $this->getRequest()->getParsedBody();
        else
            $vars = $this->getRequest()->getQueryParams();
        if (isset($vars[$var])) {
            return $vars[$var];
        }
        return $default;
    }

    public function redirectToRoute($name, array $args = []) {
        $this->response = new Response(302);
        $this->response = $this->response->withHeader('Location', $this->app->url($name, $args));
        return $this->response;
    }

    // used to instantiate/get models
    public function __get($var) {
        if (isset($this->models[$var]))
            return $this->models[$var];
        $modelClass = ucfirst(strtolower($var)) . 'Model';
        if (class_exists($modelClass))
            return ($this->models[$var] = new $modelClass());
        return null;
    }
}
>>>>>>> b3073afc0b2ef2f249c071a7a00c7b1677579668
