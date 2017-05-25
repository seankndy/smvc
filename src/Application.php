<?php
namespace SeanKndy\SMVC;

use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Interop\Http\ServerMiddleware;

class Application
{
    use RouterTrait;

    protected $request;
    protected $config = [];

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
		
		$response = (new RouteDispatcher($this, $route, $params))->dispatch($request);
		if ($response instanceof ResponseInterface) {
            $this->outputResponse($response);
        } else {
            $this->handleNoContent($request);
        }
    }

    protected function outputResponse(ResponseInterface $response) {
        foreach ($response->getHeaders() as $key => $val) {
            header("$key: $val", false);
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
        $grp = new RouteGroup($attributes);
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

    public function start() {
        $this->routeRequest(ServerRequest::fromGlobals());
    }
    
    public function getRequest() {
        return $this->request;
    }
}

