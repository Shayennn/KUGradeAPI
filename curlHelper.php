<?php

class curlHelper{

    private $ch;

    public function __destruct(){
        curl_close($this->ch);
        unlink('.htgradecc');
    }

    public function __construct(){
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 5); 
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, '.htgradecc');
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36');
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
    }

    public function HttpGET($url, $param = array()){
        curl_setopt($this->ch, CURLOPT_URL, $url.'?'.http_build_query($param));
        curl_setopt($this->ch, CURLOPT_POST, 0);
        $result = curl_exec($this->ch);
        if (!curl_errno($this->ch)) {
            $http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
            $redirected_url = curl_getinfo($this->ch, CURLINFO_REDIRECT_URL);
            if($redirected_url === FALSE)
                $redirected_url = $url;
            return array(
                'status' => TRUE,
                'code' => $http_code,
                'text' => $result,
                'url' => $redirected_url
            );
        }
    }

    public function HttpPOST($url, $param = array(), $data = array()){
        curl_setopt($this->ch, CURLOPT_URL, $url.'?'.http_build_query($param));
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $result = curl_exec($this->ch);
        if (!curl_errno($this->ch)) {
            $http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
            $redirected_url = curl_getinfo($this->ch, CURLINFO_REDIRECT_URL);
            if($redirected_url === FALSE)
                $redirected_url = $url;
            return array(
                'status' => TRUE,
                'code' => $http_code,
                'text' => $result,
                'url' => $redirected_url
            );
        }
    }

}