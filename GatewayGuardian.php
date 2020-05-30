<?php
namespace App;

class GatewayGuardian
{

    private $headerToken;
    private $sessionToken;
    private $requestMethod;
    private $checkRequest;

    public function __construct($headerToken, $sessionToken, $requestMethod = array('GET', 'POST'), $checkRequest = true)
    {

        $this->headerToken = $headerToken;
        $this->sessionToken = $sessionToken;
        $this->requestMethod = $requestMethod;
        if ($this->checkRequest === true) {
            $this->verifyRequestMethod();
        }
        $this->verifyCSRFToken();
    }

    protected function verifyRequestMethod()
    {
        if (in_array($_SERVER['REQUEST_METHOD'], $this->requestMethod, true)) {

        } else { 

            http_response_code(400);
            header('Content-Type: application/json');
            exit(json_encode(['error' => 'Invalid request type']));

        }
    }

    protected function verifyCSRFToken()
    {

        if ($_SERVER['REQUEST_METHOD'] === $this->requestMethod) {
            
            if (isset($_SERVER['HTTP_ORIGIN'])) {

                $address = 'https://' . $_SERVER['SERVER_NAME'];

                if (strpos($address, $_SERVER['HTTP_ORIGIN']) !== 0) {

                    http_response_code(403);
                    header('Content-Type: application/json');
                    exit(json_encode(['error' => 'Invalid Origin header: ' . $_SERVER['HTTP_ORIGIN']]));

                }

            } else {

                http_response_code(400);
                header('Content-Type: application/json');
                exit(json_encode(['error' => 'No Origin header']));

            }

            if (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {

                if (!hash_equals($_SESSION[$this->sessionToken], $_SERVER[$this->headerToken])) {

                    http_response_code(403);
                    header('Content-Type: application/json');
                    exit(json_encode(['error' => 'Invalid CSRF token.']));

                }

            } else {

                http_response_code(400);
                header('Content-Type: application/json');
                exit(json_encode(['error' => 'No CSRF token.']));

            }

        } else { 

            http_response_code(400);
            header('Content-Type: application/json');
            exit(json_encode(['error' => 'Invalid request type']));

        }

    }

}
