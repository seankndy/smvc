<?php
namespace SeanKndy\SMVC;

use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Interop\Http\ServerMiddleware;

class Application
{
    use Routing\RouterTrait;

    protected static $instance = null;
    protected $request;
    protected $config = [];
    protected $dataValidators = [];
    protected $session = null;
    protected $csrfProtectionManager = null;

    private function __construct() {
    }

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new Application();
        }
        return self::$instance;
    }

    public function config($key, $val = null) {
        if ($val === null && isset($this->config[$key])) {
            return $this->config[$key];
        }
        $this->config[$key] = $val;
    }

    protected function routeRequest(ServerRequestInterface $request) {
        $this->request = $request;

        // find matching route
        $route = null;
        $params = [];
        foreach ($this->routes as $r) {
            if ($r->matchRequest($request, $params)) {
                $route = $r;
                break;
            }
        }
        if (!$route) {
            $this->handleNotFound($request);
            return;
        }

        $response = (new Routing\RouteDispatcher($this, $route, $params))->dispatch($request);
        if ($response instanceof ResponseInterface) {
            $this->outputResponse($response);
        } else {
            $this->handleNoContent($request);
        }
    }

    protected function outputResponse(ResponseInterface $response) {
        foreach ($response->getHeaders() as $key => $vals) {
            if (!is_array($vals)) $vals = [$vals];
			foreach ($vals as $val) {
	            header("$key: $val", false);
            }
        }
        if ($response->hasHeader('Location')) {
            exit; // if Location header present, don't allow body to be display and exit now for security reasons.
        }
        echo (string) $response->getBody();
    }

    protected function handleNotFound(ServerRequestInterface $request) {
        $response = new Response(404, [], "404 resource not found.");
        $this->outputResponse($response);
    }

    protected function handleNoContent(ServerRequestInterface $request) {
        $response = new Response(204, [], "No content.");
        $this->outputResponse($response);
    }

    public function group(array $attributes, \Closure $callback) {
        $grp = new Routing\RouteGroup($attributes);
        $callback($grp);
        $this->routes = array_merge($this->routes, $grp->getRoutes());
    }

    // return url for route based on name and argument replacements
    public function url($namedRoute, array $args = []) {
        foreach ($this->routes as $route) {
            if ($route->getName() == $namedRoute) {
                return $route->generateUrl($args);
            }
        }
        return false;
    }

    public function setDataValidators(array $validators) {
        $this->dataValidators = $validators;
        return $this;
    }

    public function getDataValidator($name) {
        foreach ($this->dataValidators as $validatorClass) {
            if ($name == call_user_func($validatorClass . '::name')) {
                return new $validatorClass();
            }
        }
        return null;
    }

    public function setSession(\SessionHandlerInterface $session) {
        $this->session = $session;
    }

    public function getSession() {
        if (is_null($this->session) && class_exists(Session\BasicHandler::class)) {
            $this->session = new Session\BasicHandler();
        }
        return $this->session;
    }

    public function getCsrfProtectionManager() {
        return $this->csrfProtectionManager;
    }

    public function start() {
        if ($sess = $this->getSession()) {
            session_set_save_handler($sess, true);
            session_start();

            $this->csrfProtectionManager = new Session\CsrfProtectionManager($sess,
                $this->config('request.csrf_token_name') ? $this->config('request.csrf_token_name') : '_csrf_token');
        }
        $this->routeRequest(ServerRequest::fromGlobals());
    }

    public function getRequest() {
        return $this->request;
    }
}
