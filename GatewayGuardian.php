<?php

namespace App;

/**
 * @package GatewayGuardian
 * @author Garrick Crouch <services@localpathcomputing.com>
 * @version 0.1.0
 * @access public
 * @license MIT
 * @see https://github.com/localpathcomp/gateway-guardian
 */

class GatewayGuardian
{
    /**
     * verifys existence of CSRF Token & exits script execution with error message on invalid HTTP requests
     * 
     * @param string $headerToken name of your HTTP header token your're expecting
     * @param string $sessionToken name of your SESSION token for valid users
     * @param array $requestMethod HTTP verb for acceptable request methods
     * @param boolean $checkRequest whether we should verify request methods
     */

    private $headerToken;
    private $sessionToken;
    private $requestMethod;
    private $checkRequest;

    /**
     * Create a new GatewayGuardian instance.
     *
     * @return void
     */
    public function __construct($headerToken, $sessionToken, $requestMethod = array('GET', 'POST'), $checkRequest = true)
    {

        $this->headerToken = 'HTTP_' . $headerToken;
        $this->sessionToken = $sessionToken;
        $this->requestMethod = $requestMethod;
        $this->checkRequest = $checkRequest;

        if ($this->checkRequest === true) $this->verifyRequestMethod();
        $this->verifyOrigin();
        $this->verifyCSRFToken();
    }

    /**
     * Create a new GatewayGuardian instance.
     *
     * @return json
     */
    protected function verifyRequestMethod()
    {
        if (!in_array($_SERVER['REQUEST_METHOD'], $this->requestMethod, true)) $this->errorResponse(400, 'application/json', 'Invalid request type');
    }

    protected function verifyOrigin()
    {
        if (!isset($_SERVER['HTTP_ORIGIN'])) $this->errorResponse(400, 'application/json', 'No Origin header');
        if (strpos('https://' . $_SERVER['SERVER_NAME'], $_SERVER['HTTP_ORIGIN']) !== 0) $this->errorResponse(403, 'application/json', 'Invalid Origin header');
    }

    protected function verifyCSRFToken()
    {      
        if (!isset($_SERVER[$this->headerToken])) $this->errorResponse(400, 'application/json', 'No CSRF token.');
        if (!hash_equals($_SESSION[$this->sessionToken], $_SERVER[$this->headerToken])) $this->errorResponse(403, 'application/json', 'Invalid CSRF token.');
    }

    protected function errorResponse($statusCode, $contentType, $errorMessage)
    {
        http_response_code($statusCode);
        header('Content-Type: ' . $contentType);
        exit(json_encode(['error' => $errorMessage]));
    }
}
