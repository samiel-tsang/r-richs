<?php
namespace Utility;

class PayPal {
    const URL_LIVE = "https://api.paypal.com";      // https://api-m.paypal.com (Rest API Doc)
    const URL_SANDBOX = "https://api.sandbox.paypal.com";   // https://api-m.sandbox.paypal.com (Rest API Doc)

    const ENV_LIVE = 'LIVE';
    const ENV_SANDBOX = 'SANDBOX';

    const INTENT_CAPTURE = 'CAPTURE';
    const INTENT_AUTHORIZE = 'AUTHORIZE';

    const OP_ADD = 'add';
    const OP_REPLACE = 'replace';
    const OP_REMOVE = 'REMOVE';

    private $environment;
    private $clientID;
    private $clientSecret;

    private $accessToken;

    public function __construct(string $clientID, string $clientSecret, string $env) {
        $this->clientID = $clientID;
        $this->clientSecret = $clientSecret;
        $this->environment = self::ENV_SANDBOX;
        if (strtoupper($env) == self::ENV_LIVE)
            $this->environment = self::ENV_LIVE;
        $this->accessToken = null;
        $this->tokenType = null;
        $this->app_id = null;
    }

    public function setClientCredential(string $clientID, string $clientSecret, string $env) {
        $this->clientID = $clientID;
        $this->clientSecret = $clientSecret;  
        $this->environment = self::ENV_SANDBOX;
        if (strtoupper($env) == self::ENV_LIVE)
            $this->environment = self::ENV_LIVE;
        $this->accessToken = null;
    }

    public function grantAccess() {
        $header = [
            'accept: application/json',
            'accept-language: en_US',
            'authorization: basic '.base64_encode($this->clientID.":".$this->clientSecret),
            'content-type: application/x-www-form-urlencoded'
        ];
        $ret = $this->sendcUrl('/v1/oauth2/token', "POST", $header, "grant_type=client_credentials");

        if ($ret['info']['http_code'] != 200) {
            throw new \Exception('PayPal Error: '.$ret['response']->error.' Description:'.$ret['response']->error_description, $ret['info']['http_code']);
            return false;
        }

        // if needed to check the scope, RestAPI is return so need to be handle for the information
        $this->accessToken = $ret['response']->access_token;
        return true;
    }

    public function isGranted() { return !empty($this->accessToken); }

    public function createOrder(Array $purchaseUnits, string $intent = self::INTENT_CAPTURE,
        string $returnUrl = "", string $cancelUrl = "") {
        if (!$this->isGranted()) return false;

        $header = [
            'accept: application/json',
            'accept-language: en_US',
            'authorization: Bearer '.$this->accessToken,
            'content-type: application/json'
        ];
        $intentParam = self::INTENT_CAPTURE;
        if ($intent == self::INTENT_AUTHORIZE) $intentParam = self::INTENT_AUTHORIZE;
        $payload = [
            'intent'=>$intentParam,
            'purchase_units'=>$purchaseUnits,
            'application_context'=>['return_url'=>$returnUrl, 'cancel_url'=>$cancelUrl]
        ];
        $ret = $this->sendcUrl('/v2/checkout/orders', "POST", $header, json_encode($payload));

        if ($ret['info']['http_code'] < 200 || $ret['info']['http_code'] >= 300) {
            throw new \Exception('PayPal Error: '.$ret['response']->name.' Message:'.$ret['response']->message."\nJSON:".json_encode($ret['response']), $ret['info']['http_code']);
            return false;
        }

        return $ret['response'];
    }

    public function updateOrder(string $orderID, string $op, string $path, $value) {
        if (!$this->isGranted()) return false;
        if (empty($orderID)) return false;
        if (!in_array($op, [self::OP_ADD, self::OP_REPLACE, self::OP_REMOVE])) return false;

        $header = [
            'accept: application/json',
            'accept-language: en_US',
            'authorization: Bearer '.$this->accessToken,
            'content-type: application/json'
        ];
        $payload = [
            'op'=>$op,
            'path'=>$path,
            'value'=>$value
        ];

        echo json_encode($payload, JSON_UNESCAPED_SLASHES);
        $ret = $this->sendcUrl('/v2/checkout/orders/'.$orderID, "PATCH", $header, json_encode($payload, JSON_UNESCAPED_SLASHES));

        if ($ret['info']['http_code'] < 200 || $ret['info']['http_code'] >= 300) {
            throw new \Exception('PayPal Error: '.$ret['response']->name.' Message:'.$ret['response']->message."\nJSON:".json_encode($ret['response']), $ret['info']['http_code']);
            return false;
        }

        return $ret['response'];
    }

    public function captureOrder(string $orderID) {
        if (!$this->isGranted()) return false;
        if (empty($orderID)) return false;

        $header = [
            'authorization: Bearer '.$this->accessToken,
            'content-type: application/json'
        ];

        $ret = $this->sendcUrl('/v2/checkout/orders/'.$orderID.'/capture', "POST", $header);

        if ($ret['info']['http_code'] < 200 || $ret['info']['http_code'] >= 300) {
            throw new \Exception('PayPal Error: '.$ret['response']->name.' Message:'.$ret['response']->message."\nJSON:".json_encode($ret['response']), $ret['info']['http_code']);
            return false;
        }

        return $ret['response'];
    }

    public function showOrderDetails(string $orderID) {
        if (!$this->isGranted()) return false;
        if (empty($orderID)) return false;

        $header = [
            'authorization: Bearer '.$this->accessToken,
            'content-type: application/json'
        ];

        $ret = $this->sendcUrl('/v2/checkout/orders/'.$orderID, "GET", $header);

        if ($ret['info']['http_code'] < 200 || $ret['info']['http_code'] >= 300) {
            throw new \Exception('PayPal Error: '.$ret['response']->name.' Message:'.$ret['response']->message."\nJSON:".json_encode($ret['response']), $ret['info']['http_code']);
            return false;
        }

        return $ret['response'];
    }

    private function sendcUrl(string $endpoint, string $method, Array $header, string $postFields="") {
        $url = constant('self::URL_'.$this->environment).$endpoint;

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        ]);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        if (!empty($postFields))
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);

        $response = curl_exec($curl);
        $errNo = curl_errno($curl);
        $respInfo = curl_getinfo($curl);
        
        curl_close($curl);

        // release cURL before throw Exception
        if ($errNo) 
            throw new \Exception('cURL Error: '.curl_strerror($errNo), $errNo);

        return ['response'=>json_decode($response), 'info'=>$respInfo];
    }
}