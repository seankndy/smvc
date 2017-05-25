<?php
namespace SeanKndy\SMVC;

trait RouterTrait
{
    protected $routes = [];
    protected $routePrefix = '';
    protected $routeMiddleware = [];
    
    public function route($method, $routeString = null, $target = null) {
        $route = new Route($method, $routeString, $target);
        $route->setPrefix($this->routePrefix);
        $route->setMiddleware($this->routeMiddleware);
        
        $this->routes[] = $route;
        return $route;
    }
	
	public function get($routeString, $target) {
		return $this->route('GET', $routeString, $target);
	}

	public function post($routeString, $target) {
		return $this->route('POST', $routeString, $target);
	}

	public function delete($routeString, $target) {
		return $this->route('DELETE', $routeString, $target);
	}
	
	public function put($routeString, $target) {
		return $this->route('PUT', $routeString, $target);
	}

	public function any($routeString, $target) {
		return $this->route('ANY', $routeString, $target);
	}
	
	public function getRoutes() {
        return $this->routes;
    }
}

