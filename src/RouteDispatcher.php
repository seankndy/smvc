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
        $response = $this->process($request);
		if (is_string($response)) {
			$response = new Response(200, [], $response);
		}
		return $response;
    }
    
    /*
     * implementation for DelegateInterface
     */
    public function process(ServerRequestInterface $request) {
        static $middlewareIdx = 0;

        $middleware = $this->route->getMiddleware();

		// end of middleware? launch app.
        if (!isset($middleware[$middlewareIdx])) {
			return $this->dispatchRouteTarget($request);
        }
		
        $middlewareClass = $middleware[$middlewareIdx++];
        if (class_exists($middlewareClass)) {
            $middleware = new $middlewareClass();
            if ($middleware instanceof MiddlewareInterface) {
                return $middleware->process($request, $this);
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
}
