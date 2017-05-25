<?php
namespace SeanKndy\SMVC;

abstract class Middleware implements \Interop\Http\ServerMiddleware\MiddlewareInterface
{
    protected $app;
    
    public function __construct(Application $app) {
        $this->app = $app;
    }
}
