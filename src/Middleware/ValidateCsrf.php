<?php
namespace SeanKndy\SMVC\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SeanKndy\SMVC\Application;
use SeanKndy\SMVC\Session\CsrfProtectionInterface;

class ValidateCsrf extends Middleware implements CsrfProtectionInterface
{
    protected $tokenName = '_csrf_token';
    protected $token;
    protected $session;

    public function __construct() {
        $this->session = Application::instance()->getSession();
        $tokenName = $this->tokenName;
        if (!isset($this->session->$tokenName)) {
            $this->session->set($tokenName, $this->generateToken());
        }
        $this->token = $this->session->get($tokenName);
    }

    protected function generateToken() {
        if (function_exists('\random_bytes')) {
            return bin2hex(random_bytes(32));
        } else if (function_exists('\mcrypt_create_iv')) {
            return bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
        } else if (function_exists('\openssl_random_pseudo_byte')) {
            return bin2hex(openssl_random_pseudo_bytes(32));
        } else {
            return md5(uniqid(rand(), true));
        }
    }

    public function tokensMatch($str1, $str2) {
        if (function_exists('\hash_equals')) {
            return \hash_equals($str1, $str2);
        } else {
            if (strlen($str1) != strlen($str2))
                return false;
            else {
                $res = $str1 ^ $str2;
                $ret = 0;
                for($i = strlen($res) - 1; $i >= 0; $i--)
                    $ret |= ord($res[$i]);
                return !$ret;
            }
        }
    }

    public function getToken() {
        return $this->token;
    }

    public function getTokenName() {
        return $this->tokenName;
    }

    public function validateFromRequest(ServerRequestInterface $request) {
        if ($request->getMethod() != 'POST' && $request->getMethod() != 'PUT'
            && $request->getMethod() != 'DELETE') {
            return true;
        }

        $csrfTokenName = $this->tokenName;

        if (!isset($this->session->$csrfTokenName))
            throw new \Exception("No CSRF token in user session.");

        $reqMethod = strtoupper($request->getMethod());
        if ($reqMethod == 'POST' || $reqMethod == 'PUT')
            $vars = $request->getParsedBody();
        else
            $vars = $request->getQueryParams();

        if (!isset($vars[$csrfTokenName]))
            throw new \Exception("CSRF token required.");
        if (!$this->tokensMatch($this->session->get($csrfTokenName), $vars[$csrfTokenName]))
            throw new \Exception("Invalid CSRF token.");

        return true;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        try {
            if ($this->validateFromRequest($request))
                return $handler->handle($request);
        } catch (\Exception $e) {
            $response = new Response(403);
            $response->getBody()->write('403 Forbidden - ' . $e->getMessage());
            return $response;
        }
    }
}
