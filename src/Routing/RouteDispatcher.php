<?php
namespace SeanKndy\SMVC\Routing;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use GuzzleHttp\Psr7\Response;

class RouteDispatcher implements RequestHandlerInterface
{
    protected $app;
    protected $route;
    protected $params;

    public function __construct(\SeanKndy\SMVC\Application $app, Route $route, array $params = []) {
        $this->app = $app;
        $this->route = $route;
        $this->params = $params;
        return $this;
    }

    public function dispatch(ServerRequestInterface $request) {
        try {
            $response = $this->stringToResponse($this->handle($request));
            return $response;
        } catch (\Exception $e) {
            $response = new Response(500);
            $response->getBody()->write('500 Internal Server Error - ' . $e->getMessage());
            return $response;
        }
    }

    /*
     * implementation for RequestHandlerInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface {
        static $middlewareArray = null;

        if (is_null($middlewareArray)) {
            $middlewareArray = $this->route->getMiddleware();
        }

        $middlewarecls = array_shift($middlewareArray);

        if (is_null($middlewarecls)) {
            // end of middleware? launch app.
            $middlewareArray = null;
            return $this->stringToResponse($this->dispatchRouteTarget($request));
        } else if (class_exists($middlewarecls)) {
           $middleware = new $middlewarecls();
           if ($middleware instanceof MiddlewareInterface) {
               // fire up the middleware
               return $this->stringToResponse($middleware->process($request, $this));
           } else {
               // not a middleware class, throw
               throw new \RuntimeException("Middleware class '$middleware' must implement MiddlwareInterface.");
           }
        } else {
           // class not found, throw
           throw new \RuntimeException("Middleware class '$middlewarecls' not found.");
        }
    }

    protected function dispatchRouteTarget(ServerRequestInterface $request) {
        $targetNamespacePrefix = $this->route->getTargetNamespacePrefix();
        $target = $this->route->getTarget();
        if (is_string($target) && strstr($target, '::') !== false) { // assume Controller instance
            $target = $targetNamespacePrefix . $target; // prepend namespace
            list($class, $method) = explode('::', $target);
            if (class_exists($class)) {
                return call_user_func_array([new $class($this->app), $method], [$this->params]);
            } else
                throw new \RuntimeException("Route could not be dispatched, controller $class non-existant!");
        } else if (is_callable($target)) {
            $func = $target;
            return $func($request, $this->params);
        } else
            throw new \RuntimeException("Route could not be dispatched, uncallable!");
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
