<?php

namespace QR2REG;

class QR2REGClient
{
    const URL = 'https://stage.qr2reg-api.vivifyideas.com/api/oauth/exchange?authorization_code=';

    public function __construct()
    {
        $this->httpClient = new \Guzzle\Http\Client();
    }

    public function exchangeAuthorizationCode($authorizationCode)
    {
        $request = $this->httpClient->get(self::URL . $authorizationCode);
        $response = $this->httpClient->send($request);

        return json_decode($response->getBody(), true);
    }
}

