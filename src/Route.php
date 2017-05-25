<?php
namespace SeanKndy\SMVC;

use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class Route
{
    protected $httpMethod;
    protected $routeString;
    protected $defaultParams;
    protected $target;
    protected $middleware;
    protected $name;
    protected $prefix = '';
    protected $hostString = '';

    public function __construct($httpMethod, $routeString, $target, $defaultParams = [], $middleware = []) {
        $this->setHttpMethod($httpMethod);
        $this->setRouteString($routeString);
        $this->setTarget($target);
        $this->setDefaultParams($defaultParams);
        $this->setMiddleware($middleware);
        return $this;
    }

    public function matchRequest(ServerRequestInterface $request, &$params) {
        // if method doesn't match, bail now
        if ($this->httpMethod != 'ANY' && $this->httpMethod != strtoupper($request->getMethod())) {
            return false;
        }
        
        // match on hostname if set, and also extract variables from hostname if provided
        if ($this->hostString) {
            $hostRegex = '/^' . preg_replace('/\{(\w+)\}/', '(.+?)', str_replace('.', '\\.', $this->hostString)) . '$/';
            if (preg_match_all($hostRegex, $request->getUri()->getHost(), $valueMatches)) {
                if (isset($valueMatches[1]) && preg_match('/\{(\w+)\}/', $this->hostString, $varMatches)) {
                    array_shift($varMatches);
                    $params = array_combine($varMatches, $valueMatches[1]);
                }
            } else
                return false;
        }
        
        $routeArray = explode('/', $this->getRouteStringWithPrefix());
        $pathString = trim($request->getUri()->getPath(), '/');
        $pathArray = explode('/', $pathString);
        
        // compare $pathArray to $routeArray, item by item
        foreach ($pathArray as $k => $pathItem) {
            if (!isset($routeArray[$k]))
                return false;
            $routeItem = $routeArray[$k];
            $defaultValue = '';
            if (preg_match('/^\{(\w+\??)\}$/', $routeItem, $m)) { // if variable (defined by word within curly brackets)
                $variable = $m[1];
                
                if (substr($variable, -1) == '?') { // optional flag (?) specified
                    $variable = substr($variable, 0, -1); // hack '?' off end
                    $params[$variable] = (!$pathItem ? (isset($this->defaultParams[$variable]) ? $defaultValue : '') : $pathItem);
                } else if (!$pathItem) {
                    return false;
                } else {
                    $params[$variable] = $pathItem;
                }
            } else if ($routeItem == '*') { // wildcard
                continue;
            } else if ($routeItem != $pathItem) {
                return false;
            }
        }
        
        return true;
    }
    
    public function getName() {
        return $this->name;
    }
     
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getHttpMethod() {
        return $this->httpMethod;
    }

    public function setHttpMethod($httpMethod) {
        $this->httpMethod = strtoupper(trim($httpMethod));
        return $this;
    }

    public function getRouteString() {
        return $this->routeString;
    }
    
    public function getRouteStringWithPrefix() {
        return $this->prefix . $this->routeString;
    }

    public function setRouteString($routeString) {
        $this->routeString = ltrim($routeString, '/');
        return $this;
    }

    public function getTarget() {
        return $this->target;
    }

    public function setTarget($target) {
        $this->target = $target;
        return $this;
    }

    public function getDefaultParams() {
        return $this->defaultParams;
    }

    public function setDefaultParams(array $defaultParams) {
        $this->defaultParams = $defaultParams;
        return $this;
    }

    public function getMiddleware() {
        return $this->middleware;
    }

    public function setMiddleware(array $middleware) {
        $this->middleware = $middleware;
        return $this;
    }
    
    public function setPrefix($prefix) {
        $this->prefix = $prefix;
    }
    
    public function setHostString($hostString) {
        $this->hostString = $hostString;
    }
    
    public function generateUrl(array $params = []) {
        $url = $this->getRouteStringWithPrefix();
        foreach ($params as $k => $v) {
            $url = preg_replace('/\{' . preg_quote($k) . '\??\}/', $v, $url);
        }
        $url = preg_replace('/\/?\{(\w+\?)\}/', '', $url);
        return $url;
    }
}
