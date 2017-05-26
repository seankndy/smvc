<?php
namespace SeanKndy\SMVC\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use SeanKndy\SMVC\Application;

class ValidateCsrfMiddleware extends Middleware
{
    public function process(ServerRequestInterface $request, DelegateInterface $delegate) {
        try {
            $this->app->getCsrfProtectionManager()->validateFromRequest($request);
            return $delegate->process($request, $delegate);
        } catch (\Exception $e) {
            $response = new Response(403);
            $response->getBody()->write('403 Forbidden - ' . $e->getMessage());
            return $response;
        }        
    }
}
