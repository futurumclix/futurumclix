<?php

class Cubits_Rpc
{
    private $_requestor;
    private $authentication;

    public function __construct($requestor, $authentication)
    {
        $this->_requestor = $requestor;
        $this->_authentication = $authentication;
    }

    public function request($method, $url, $params)
    {
        // Create query string
        if ($params)
            $queryString = json_encode($params);
        else
            $queryString = '';
        $path = "/api/v1/$url";
        $url = CUBITS_API_BASE . $url;

        // Initialize CURL
        $curl = curl_init();
        $curlOpts = array();


        $method = strtolower($method);

        // Check wether CURL should verify SSL (host and peer). 
        if ( CUBITS_SSL_VERIFY == false) {
          $curlOpts[CURLOPT_SSL_VERIFYPEER] = 0;
          $curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
        }
        // HTTP method

        if ($method == 'get') {
            $curlOpts[CURLOPT_HTTPGET] = 1;
            if ($queryString) {
                $url .= "?" . $queryString;
            }
        } else if ($method == 'post') {
            $curlOpts[CURLOPT_POST] = 1;
            $curlOpts[CURLOPT_POSTFIELDS] = $queryString;
        } else if ($method == 'delete') {
            $curlOpts[CURLOPT_CUSTOMREQUEST] = "DELETE";
            if ($queryString) {
                $url .= "?" . $queryString;
            }
        } else if ($method == 'put') {
            $curlOpts[CURLOPT_CUSTOMREQUEST] = "PUT";
            $curlOpts[CURLOPT_POSTFIELDS] = $queryString;
        }

        // Headers
        $headers = array('User-Agent: Cubits/PHP v0.0.1');

        $auth = $this->_authentication->getData();

        // Get the authentication class and parse its payload into the HTTP header.
        $authenticationClass = get_class($this->_authentication);
        switch ($authenticationClass) {


            case 'Cubits_ApiKeyAuthentication':
                // Use HMAC API key

                $dataToHash = '';
                if (array_key_exists(CURLOPT_POSTFIELDS, $curlOpts)) {
                    $dataToHash .= $curlOpts[CURLOPT_POSTFIELDS];
                }
                // First i create the message
                // string hash ( string $algo , string $data [, bool $raw_output = false ] )
                $post_data = $this->sha256hash($dataToHash);
                $microseconds = sprintf('%0.0f',round(microtime(true) * 1000000));

                $message = utf8_encode($path) . $microseconds . $post_data ;

                // string hash_hmac ( string $algo , string $data , string $key [, bool $raw_output = false ] )
                $hmac_key = $auth->apiKeySecret;
                $signature = $this->calc_signature($message, $hmac_key);

                $headers[] = "X-Cubits-Key: {$auth->apiKey}";
                $headers[] = "X-Cubits-Signature: $signature";
                $headers[] = "X-Cubits-Nonce: $microseconds";
                $headers[] = "Accept: application/vnd.api+json";
                $headers[] = "Content-Type: application/vnd.api+json";
                break;


            default:
                throw new Cubits_ApiException("Invalid authentication mechanism");
                break;
        }

        // CURL options
        $curlOpts[CURLOPT_URL] = $url;
        $curlOpts[CURLOPT_HTTPHEADER] = $headers;
        $curlOpts[CURLOPT_RETURNTRANSFER] = true;

        // Do request
        curl_setopt_array($curl, $curlOpts);
        $response = $this->_requestor->doCurlRequest($curl);

        // Decode response
        try {
            $json = $response['body'];
        } catch (Exception $e) {
            throw new Cubits_ConnectionException("Invalid response body", $response['statusCode'], $response['body']);
        }
        if($json === null) {
            throw new Cubits_ApiException("Invalid response body", $response['statusCode'], $response['body']);
        }
        if(isset($json->error)) {
            throw new Cubits_ApiException($json->error, $response['statusCode'], $response['body']);
        } else if(isset($json->errors)) {
            throw new Cubits_ApiException(implode($json->errors, ', '), $response['statusCode'], $response['body']);
        }

        return $json;
    }

    public function sha256hash($data){
        return hash('sha256',  utf8_encode( $data ), false );
    }

    public function calc_signature($message, $hmac_key){
        return hash_hmac("sha512", $message , $hmac_key);
    }
}
?>
