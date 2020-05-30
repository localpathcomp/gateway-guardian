<?php
namespace App;

/**
 * @package App
 * @author Garrick Crouch <services@localpathcomputing.com>
 * @version 0.1.0
 * @access public
 * @see https://github.com/localpathcomp/gateway-guardian
 */

class GatewayGuardian
{
    /**
     * exits script execution with error message on invalid HTTP requests
     * 
     * @param string $headerToken name of your HTTP header token your're expecting
     * @param string $sessionToken name of your SESSION token for valid users
     * @param mixed $requestMethod HTTP verb for acceptable request methods
     * @param boolean $checkRequest whether we should verify request methods
     */

    private $headerToken;
    private $sessionToken;
    private $requestMethod;
    private $checkRequest;

    public function __construct($headerToken, $sessionToken, $requestMethod = array('GET', 'POST'), $checkRequest = true)
    {

        $this->headerToken = $headerToken;
        $this->sessionToken = $sessionToken;
        $this->requestMethod = $requestMethod;
        if ($this->checkRequest === true) $this->verifyRequestMethod();
        $this->verifyOrigin();
        $this->verifyCSRFToken();
    }

    protected function verifyRequestMethod()
    {
        if (!in_array($_SERVER['REQUEST_METHOD'], $this->requestMethod, true)) {

            http_response_code(400);
            header('Content-Type: application/json');
            exit(json_encode(['error' => 'Invalid request type']));

        }
    }

    protected function verifyOrigin()
    {
        if (!isset($_SERVER['HTTP_ORIGIN'])) {

            http_response_code(400);
            header('Content-Type: application/json');
            exit(json_encode(['error' => 'No Origin header']));

        } else {

            if (strpos('https://' . $_SERVER['SERVER_NAME'], $_SERVER['HTTP_ORIGIN']) !== 0) {

                http_response_code(403);
                header('Content-Type: application/json');
                exit(json_encode(['error' => 'Invalid Origin header']));

            }
        }
    }

    protected function verifyCSRFToken()
    {      
        if (!isset($_SERVER[$this->headerToken])) {

            http_response_code(400);
            header('Content-Type: application/json');
            exit(json_encode(['error' => 'No CSRF token.']));

        } else {

            if (!hash_equals($_SESSION[$this->sessionToken], $_SERVER[$this->headerToken])) {

                http_response_code(403);
                header('Content-Type: application/json');
                exit(json_encode(['error' => 'Invalid CSRF token.']));

            }
        }
    }
}
