<?php
namespace SeanKndy\SMVC\Middleware;

abstract class Middleware implements \Interop\Http\ServerMiddleware\MiddlewareInterface
{
    protected $app;

    public function __construct(\SeanKndy\SMVC\Application $app) {
        $this->app = $app;
    }
}
