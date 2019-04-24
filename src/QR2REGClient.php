<?php

namespace QR2REG;

use Exception;
use GuzzleHttp\Client;

use QR2REG\QR2REGUnauthorizedException;

class QR2REGClient
{
    const CIPHER = 'AES-256-CBC';

    const BASE_URL = 'https://stage.qr2reg-api.vivifyideas.com/api/';
    const EXCHANGE_ENDPOINT_URL = 'oauth/exchange?authorization_code=';

    public function __construct($applicationSecret)
    {
        $this->applicationSecret = $applicationSecret;
        $this->httpClient = new Client([ 'base_uri' => self::BASE_URL ]);
    }

    public function exchangeAuthorizationCode($authorizationCode)
    {
        try {
            $response = $this->httpClient->get(self::EXCHANGE_ENDPOINT_URL . $authorizationCode);
        } catch (Exception $exception) {
            throw new QR2REGUnauthorizedException;
        }

        $responseJson = json_decode($response->getBody());

        if (!$stringifiedUserData = $this->decrypt($responseJson->data)) {
            throw new QR2REGUnauthorizedException;
        }

        return (object) [
            'data' => json_decode($stringifiedUserData),
        ];
    }

    private function decrypt($data)
    {
        $c = base64_decode($data);
        $ivLength = openssl_cipher_iv_length($cipher=self::CIPHER);
        $iv = substr($c, 0, $ivLength);
        $hmac = substr($c, $ivLength, $sha2len=32);
        $cipherText = substr($c, $ivLength + $sha2len);
        $plainText = openssl_decrypt(
            $cipherText,
            $cipher,
            $this->applicationSecret,
            $options=OPENSSL_RAW_DATA,
            $iv
        );

        $calcmac = hash_hmac('sha256', $cipherText, $this->applicationSecret, $as_binary=true);

        if (!hash_equals($hmac, $calcmac)) {
            return false;
        }

        return $plainText;
    }
}
