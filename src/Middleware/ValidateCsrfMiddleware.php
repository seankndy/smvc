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
        $app = Application::instance();
        $csrfTokenName = $app->config('request.csrf_token_name');
		if (!$csrfTokenName)
		    $csrfTokenName = '_csrf_token';

        $session = $app->getSession();

        if (isset($session->$csrfTokenName)) {
            $reqMethod = strtoupper($request->getMethod());
            if ($reqMethod == 'POST' || $reqMethod == 'PUT') {
                $vars = $request->getParsedBody();
            } else {
                $vars = $request->getQueryParams();
            }
            if (!isset($vars[$csrfTokenName]) || $session->$csrfTokenName != $vars[$csrfTokenName]) {
                $response = new Response(403);
                $response->getBody()->write('403 Forbidden');
                return $response;
            }
        }

        return $delegate->process($request, $delegate);
    }
}
    
