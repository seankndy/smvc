<?php
namespace SeanKndy\SMVC;

abstract class Middleware implements \Interop\Http\ServerMiddleware\MiddlewareInterface
{   
    public function __construct() {
        ; // currently no special implementation, just a wrapper
    }
}
