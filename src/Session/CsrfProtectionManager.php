<?php
namespace SeanKndy\SMVC\Session;

use Psr\Http\Message\ServerRequestInterface;

class CsrfProtectionManager
{
    protected $session;
    protected $tokenName;
    protected $token;

    public function __construct(BaseHandler $session, $csrfTokenName = '_csrf_token') {
        $this->session = $session;
        $this->tokenName = $csrfTokenName;

        if (!isset($this->session->$csrfTokenName)) {
            $this->session->set($csrfTokenName, $this->generateToken());
        }
        $this->token = $this->session->get($csrfTokenName);
    }

    public function validateFromRequest(ServerRequestInterface $request) {
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

    public function generateHiddenInput() {
        return '<input type="hidden" name="' . $this->tokenName . '" value="' . $this->token . '">';
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

    protected function generateToken() {
        if (function_exists('random_bytes')) {
            return bin2hex(random_bytes(32));
        } else if (function_exists('mcrypt_create_iv')) {
            return bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
        } else if (function_exists('openssl_random_pseudo_byte')) {
            return bin2hex(openssl_random_pseudo_bytes(32));
        } else {
            return md5(uniqid(rand(), true));
        }
    }
}
