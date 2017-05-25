<?php
namespace SeanKndy\SMVC;

trait RouterTrait
{
    protected $routes = [];
    
    /*
     * these are "global" attributes to apply to every route added
     * they will always be blank within Application, but will be populated
     * within a RouteGroup
     */
    protected $routePrefix = '';
    protected $routeMiddleware = [];
    protected $routeHost = '';
    
    public function route($method, $routeString = null, $target = null) {
        $route = new Route($method, $routeString, $target);
        $route->setPrefix($this->routePrefix);
        $route->setMiddleware($this->routeMiddleware);
        $route->setHostString($this->routeHost);
        
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

