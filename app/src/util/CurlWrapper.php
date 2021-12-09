<?php

namespace util;

class CurlWrapper {

    public function post($url, $message, $timeoutInSeconds = 10, $port = 443)
    {
        $curlSession = curl_init();

        curl_setopt($curlSession, CURLOPT_POSTFIELDS, $message);

        curl_setopt($curlSession, CURLOPT_URL, $url);
        curl_setopt($curlSession, CURLOPT_PORT, $port);
        
        curl_setopt($curlSession, CURLOPT_HEADER, 0);
        curl_setopt($curlSession, CURLOPT_POST, 1); 
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, 1);

        curl_setopt($curlSession, CURLOPT_CONNECTTIMEOUT, $timeoutInSeconds);
        curl_setopt($curlSession, CURLOPT_TIMEOUT, $timeoutInSeconds); 

        $response = curl_exec($curlSession);
        $httpcode = curl_getinfo($curlSession, CURLINFO_HTTP_CODE);

        if (curl_errno($curlSession))
        { 
            $response = curl_error($curlSession);
        }

        curl_close($curlSession);

        $result = new \stdClass();
        $result->response = $response;
        $result->httpcode = $httpcode;
        return $result;
    }
}