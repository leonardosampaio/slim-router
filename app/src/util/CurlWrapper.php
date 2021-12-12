<?php

namespace util;

class CurlWrapper {

    /**
     * Makes a POST request using libcurl
     * 
     * @param string $url The URL to make the request to
     * @param string $message raw data to send
     * @param string $timeoutInSeconds connect and receive timeout
     * @param int $port The port to connect to
     * 
     * @return array httpcode and raw json response from server
     */
    public function post($url, $message, $headers = [], $timeoutInSeconds = 10, $port = 443)
    {
        $curlSession = curl_init();

        if (!empty($headers))
        {
            curl_setopt($curlSession, CURLOPT_HTTPHEADER, $headers);
        }

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