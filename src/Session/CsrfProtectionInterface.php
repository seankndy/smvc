<?php
namespace SeanKndy\SMVC\Session;

use Psr\Http\Message\ServerRequestInterface;

interface CsrfProtectionInterface
{
    /*
     * validate csrf in $request and return true if valid
     * throw Exception if not valid
     */
    public function validateFromRequest(ServerRequestInterface $request);

    /*
     * get the token value
     */
    public function getToken();

    /*
     * get the token field name
     */
    public function getTokenName();

}
