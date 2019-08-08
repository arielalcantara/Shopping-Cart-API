<?php
namespace Auth\Listener;

use Firebase\JWT\JWT;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;

class AuthListener
{
    private $publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC8kGa1pSjbSYZVebtTRBLxBz5H
4i2p/llLCrEeQhta5kaQu/RnvuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t
0tyazyZ8JXw+KgXTxldMPEL95+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4
ehde/zUxo6UvS7UrBQIDAQAB
-----END PUBLIC KEY-----
EOD;

    public function __invoke(MvcEvent $mvcEvent)
    {
        $mvcEvent->getRequest();
        // check if request is authenticated
        $isValid = false;

        $authHeader = $mvcEvent->getRequest()->getHeader('Authorization');

        if (!empty($authHeader)) {
            $arrAuthHeader = explode(" ", $authHeader->getFieldValue());

            try {
                $decoded = JWT::decode($arrAuthHeader[1], $this->publicKey, array('RS256'));
                $isValid = true;
            } catch (\Exception $e) {
                $isValid = false;
            }
        }

        // issue error when not authenticated
        if (!$isValid) {
            // set error response
            $response = new JsonModel(['error' => 'Not Authorized']);
            $mvcEvent->getResponse()->setStatusCode(401);
            // set result
            $mvcEvent->setResult($response);

            // return response
            return $mvcEvent->getResponse();

        }
    }
}
