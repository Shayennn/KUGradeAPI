<?php
require_once('func.php');
require_once('curlHelper.php');

class KUGrade
{

    private $username, $password, $request;
    private $url = 'https://grade.ku.ac.th/GSTU';
    private $path = array(
        'login' => 'KUG0010104_Login.aspx',
        'selectcourse' => 'KUG4150101_SelectCourse.aspx'
    );

    private function fullURL($key)
    {
        return $this->url . '/' . $this->path[$key];
    }

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
        $this->request = new curlHelper();
    }

    public function getGrade($semester = NULL)
    {
        $addParam = array(
            'UserIDTextBox' => $this->username,
            'PasswordTextBox' => $this->password,
            'CampusDropDownList' => '0'
        );
        if (!isValidUsername($this->username)) {
            return array(
                'status' => FALSE,
                'error_msg' => "Internal ERROR"
            );
        }
        $result = $this->request->HttpGET($this->fullURL('login'));
        if (!$result['status'] || $result['code'] != 200) {
            return array(
                'status' => FALSE,
                'error_msg' => "Cannot send GET to login page",
                'http_response' => $result
            );
        }
        $data = getAllInput($result['text']) + $addParam;
        $result = $this->request->HttpPOST($this->fullURL('login'), array(), $data);
        if (!$result['status'] || $result['code'] != 200) {
            return array(
                'status' => FALSE,
                'error_msg' => "Cannot send POST to login page",
                'http_response' => $result
            );
        }
        if (count($alerts = getAlert($result['text'])) != 0) {
            return array(
                'status' => FALSE,
                'error_msg' => $alerts
            );
        }
        $semavailable = getAvailableSemesters($result['text']);

        if ($semester === NULL || $semavailable[count($semavailable) - 1] == strval($semester)) {
            $grades = fetchGrade($result['text']);
            return array(
                'status' => TRUE,
                'semester' => $semavailable[count($semavailable) - 1],
                'semavailable' => $semavailable,
                'data' => $grades
            );
        } elseif (in_array(strval($semester), $semavailable)) {
            $params = array(
                'ctl00$ScriptManager1' => 'ctl00$ScriptManager1|ctl00$ContentPlaceHolder1$CmdBt_Search_057',
                '__EVENTTARGET' => '',
                '__EVENTARGUMENT' => '',
                'ctl00$ContentPlaceHolder1$EduSemDropDownList' => $semester,
                '__ASYNCPOST' => 'true',
                'ctl00$ContentPlaceHolder1$CmdBt_Search_057' => 'ค้นหา'
            );
            $data = getAllInput($result['text']) + $params;
            $result = $this->request->HttpPOST($this->fullURL('selectcourse'), array(), $data);
            if (!$result['status'] || $result['code'] != 200) {
                return array(
                    'status' => FALSE,
                    'error_msg' => "Cannot send POST to selectcourse page",
                    'http_response' => $result
                );
            }
            if (count($alerts = getAlert($result['text'])) != 0) {
                return array(
                    'status' => FALSE,
                    'error_msg' => $alerts
                );
            }
            $grades = fetchGrade($result['text']);
            return array(
                'status' => TRUE,
                'semester' => intval($semester),
                'semavailable' => $semavailable,
                'data' => $grades
            );
        } else {
            return array(
                'status' => FALSE,
                'error_msg' => 'That semester is not available.'
            );
        }
    }
}
