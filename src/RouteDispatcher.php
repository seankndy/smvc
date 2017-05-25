<?php
namespace SeanKndy\SMVC;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;

class RouteDispatcher implements DelegateInterface
{
    protected $route;
    protected $app;
    protected $params;    

    public function __construct(Application $app, Route $route, array $params = []) {
        $this->route = $route;
        $this->app = $app;
        $this->params = $params;
        return $this;
    }
    
    public function dispatch(ServerRequestInterface $request) {
        return $this->stringToResponse($this->process($request));
    }
    
    /*
     * implementation for DelegateInterface
     */
    public function process(ServerRequestInterface $request) {
        static $middlewareArray = null;
        
        if (is_null($middlewareArray)) {
            $middlewareArray = $this->route->getMiddleware();
        }
        
        $middleware = array_shift($middlewareArray);
        
        if (is_null($middleware)) {
            // end of middleware? launch app.
            $middlewareArray = null;
            return $this->stringToResponse($this->dispatchRouteTarget($request));
        } else if (class_exists($middleware)) {
            // run middleware
            $middleware = new $middleware($this->app);
            if ($middleware instanceof MiddlewareInterface) {
                return $this->stringToResponse($middleware->process($request, $this));
            } else {
                return $this->process($request);
            }
        }
    }
    
    protected function dispatchRouteTarget(ServerRequestInterface $request) {
        $target = $this->route->getTarget();
        if (is_string($target) && strstr($target, '::') !== false) { // assume Controller instance
            list($class, $method) = explode('::', $target);
            return call_user_func_array([new $class($this->app), $method], [$this->params]);
        } else if (is_callable($target)) {
            $func = $target;
            return $func($request, $this->params);
        } else
            throw new \Exception("Route could not be dispatched, uncallable!");
    }
    
    protected function stringToResponse($string) {
        if (is_string($string)) {
            $response = new Response();
            $response->getBody()->write($string);
        } else
            $response = $string;
        return $response;
    }
}
