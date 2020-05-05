<?php
require_once('func.php');
require_once('curlHelper.php');

class KUGrade{

    private $username, $password, $request;
    private $url = 'https://grade.ku.ac.th/GSTU';
    private $path = array(
        'login' => 'KUG0010104_Login.aspx'
    );

    private function fullURL($key){
        return $this->url.'/'.$this->path[$key];
    }

    public function __construct($username, $password){
        $this->username = $username;
        $this->password = $password;
        $this->request = new curlHelper();
    }

    public function getGrade(){
        $addParam = array(
            'UserIDTextBox' => $this->username,
            'PasswordTextBox' => $this->password,
            'CampusDropDownList' => '0'
        );
        if(!isValidUsername($this->username)){
            return array(
                'status' => FALSE,
                'error_msg' => "Internal ERROR"
            );
        }
        $result = $this->request->HttpGET($this->fullURL('login'));
        if(!$result['status'] || $result['code'] != 200){
            return array(
                'status' => FALSE,
                'error_msg' => "Cannot send GET to login page",
                'http_response' => $result
            );
        }
        $data = getAllInput($result['text'])+$addParam;
        $result = $this->request->HttpPOST($this->fullURL('login'), array(), $data);
        if(!$result['status'] || $result['code'] != 200){
            return array(
                'status' => FALSE,
                'error_msg' => "Cannot send POST to login page",
                'http_response' => $result
            );
        }
        if(count($alerts=getAlert($result['text'])) != 0){
            return array(
                'status' => FALSE,
                'error_msg' => $alerts
            );
        }
        $grades = fetchGrade($result['text']);
        return array(
            'status' => TRUE,
            'data' => $grades
        );
    }
}
