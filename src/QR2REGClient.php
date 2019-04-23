<?php

namespace QR2REG;

use GuzzleHttp\Client;

class QR2REGClient
{
    const BASE_URL = 'https://stage.qr2reg-api.vivifyideas.com/api/';
    const EXCHANGE_ENDPOINT_URL = 'oauth/exchange?authorization_code=';

    public function __construct()
    {
        $this->httpClient = new Client([ 'base_uri' => self::BASE_URL ]);
    }

    public function exchangeAuthorizationCode($authorizationCode)
    {
        $response = $this->httpClient->get(self::EXCHANGE_ENDPOINT_URL . $authorizationCode);

        return json_decode($response->getBody());
    }
}
