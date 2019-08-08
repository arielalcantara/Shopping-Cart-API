<?php

namespace Auth\Service;

use \Firebase\JWT\JWT;

class TokenService
{

    private $privateKey = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQC8kGa1pSjbSYZVebtTRBLxBz5H4i2p/llLCrEeQhta5kaQu/Rn
vuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t0tyazyZ8JXw+KgXTxldMPEL9
5+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4ehde/zUxo6UvS7UrBQIDAQAB
AoGAb/MXV46XxCFRxNuB8LyAtmLDgi/xRnTAlMHjSACddwkyKem8//8eZtw9fzxz
bWZ/1/doQOuHBGYZU8aDzzj59FZ78dyzNFoF91hbvZKkg+6wGyd/LrGVEB+Xre0J
Nil0GReM2AHDNZUYRv+HYJPIOrB0CRczLQsgFJ8K6aAD6F0CQQDzbpjYdx10qgK1
cP59UHiHjPZYC0loEsk7s+hUmT3QHerAQJMZWC11Qrn2N+ybwwNblDKv+s5qgMQ5
5tNoQ9IfAkEAxkyffU6ythpg/H0Ixe1I2rd0GbF05biIzO/i77Det3n4YsJVlDck
ZkcvY3SK2iRIL4c9yY6hlIhs+K9wXTtGWwJBAO9Dskl48mO7woPR9uD22jDpNSwe
k90OMepTjzSvlhjbfuPN1IdhqvSJTDychRwn1kIJ7LQZgQ8fVz9OCFZ/6qMCQGOb
qaGwHmUK6xzpUbbacnYrIM6nLSkXgOAwv7XXCojvY614ILTK3iXiLBOxPu5Eu13k
eUz9sHyD6vkgZzjtxXECQAkp4Xerf5TGfQXGXhxIX52yH+N2LtujCdkQZjXAsGdm
B2zNzvrlgRmgBrklMTrMYgm1NPcW+bRLGcwgW2PTvNM=
-----END RSA PRIVATE KEY-----
EOD;

    private $publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC8kGa1pSjbSYZVebtTRBLxBz5H
4i2p/llLCrEeQhta5kaQu/RnvuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t
0tyazyZ8JXw+KgXTxldMPEL95+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4
ehde/zUxo6UvS7UrBQIDAQAB
-----END PUBLIC KEY-----
EOD;

    public function __construct()
    {
    }

    public function generateToken(array $tokenPayload)
    {
        $token = array(
            "iss" => "api.shopping.com",
            "aud" => "shopping-cart.com",
            "iat" => time(),
            "nbf" => time(),
            "exp" => time() + 86400,
            "data" => $tokenPayload,
        );

        $jwt = JWT::encode($token, $this->privateKey, 'RS256');
        return $jwt;
    }

    public function validateToken($jwt)
    {
        try {
            $decoded = JWT::decode($jwt, $this->publicKey, array('RS256'));
            return true;
        } catch (\Exception $e) {
            return false;
        }

    }

    public function decodeAccessToken($authHeader)
    {
        if (!empty($authHeader)) {
            $arrAuthHeader = explode(" ", $authHeader->getFieldValue());

            try {
                $decoded = JWT::decode($arrAuthHeader[1], $this->publicKey, array('RS256'));
            } catch (\Exception $e) {
                $decoded = false;
            }

            return get_object_vars($decoded);
        }
    }

    public function getCustomerIdInAccessToken($authHeader)
    {
        if (!empty($authHeader)) {
            $arrAuthHeader = explode(" ", $authHeader->getFieldValue());
            try {
                $decoded = JWT::decode($arrAuthHeader[1], $this->publicKey, array('RS256'));
            } catch (\Exception $e) {
                $decoded = false;
            }
            $customerData = $decoded ? get_object_vars($decoded) : $decoded;
            $customerData = $customerData['data'] ? get_object_vars($customerData['data']) : $customerData['data'];
            $customer_id = (int) $customerData['customer_id'];
        } else {
            $customer_id = 0;
        }
        return $customer_id;
    }
}
