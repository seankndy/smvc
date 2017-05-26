<?php
namespace SeanKndy\SMVC\Routing;

class RouteGroup
{
    use RouterTrait;
    
    protected $attributes;

    public function __construct(array $attributes) {
        $this->setAttributes($attributes);
        $this->routes = [];
    }

    public function setAttributes(array $attributes) {
        /*
         * only 2 attributes supported at this time
         */
        if (isset($attributes['prefix'])) {
            $this->routePrefix = $attributes['prefix'];
        }
        if (isset($attributes['middleware'])) {
            if (!is_array($attributes['middleware'])) {
                $attributes['middleware'] = [$attributes['middleware']];
            }
            $this->routeMiddleware = $attributes['middleware'];
        }
        if (isset($attributes['hostname'])) {
            $this->routeHost = $attributes['hostname'];
        }
        $this->attributes = $attributes;
    }
}
