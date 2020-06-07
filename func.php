<?php

function isValidUsername($username){
    if(!preg_match('/^b([0-9]{10})$/', $username, $match))return false;
    return VerifyNisitID($match[1]);
}

function getInput($html ,$name){
    preg_match('/<input.*? name="'.$name.'" .*?value="(.*?)".*?\/>/', $html, $match);
    if(count($match) == 2)
        return $match[1];
    return null;
}

function getAllInput($html){
    preg_match_all('/<input.*? name="(.*?)" .*?value="(.*?)".*?\/>/', $html, $matches);
    return array_combine($matches[1], $matches[2]);
}

function getAvailableSemesters($html){
    preg_match_all('/<option.*? value="([0-9]{3})">/', $html, $matches);
    return $matches[1];
}

function getAlert($html){
    preg_match_all('/alert\(\'(.*?)\'\)/', $html, $matches);
    for($i=0; $i<count($matches[0]); $i++){
        if(mb_strpos($matches[1][$i], 'ขณะนี้เวลา') !== FALSE){
            unset($matches[1][$i]);
        }
    }
    return $matches[1];
}

function fetchGrade($html){
    $pattern = '/<td>([0-9]{8})<\/td><td align="left">(.*?)<\/td><td>(.*?)<\/td><td>(.*?)<\/td><td>(.*?)<\/td><td>.*?<\/td>/';
    preg_match_all($pattern, $html, $matches);
    $grades = array();
    for($i=0; $i<count($matches[0]); $i++){
        $code           = trim(html_entity_decode($matches[1][$i]));
        $name           = trim(html_entity_decode($matches[2][$i]));
        $section        = trim(html_entity_decode($matches[3][$i]));
        $grade          = trim(html_entity_decode($matches[4][$i]));
        $gradeStatus    = trim(html_entity_decode($matches[5][$i]));
        if($name == " ")$name = "";
        if($section == " ")$section = 0;
        else $section = intval($section);
        if($grade == " ")$grade = null;
        if($gradeStatus == " ")$gradeStatus = -1;
        if($gradeStatus == "ทางการ")$gradeStatus = 1;
        if($gradeStatus == "ไม่เป็นทางการ")$gradeStatus = 0;
        $grades[] = array(
            'code' => $code,
            'name' => $name,
            'section' => $section,
            'grade' => $grade,
            'status' => $gradeStatus
        );
    }
    return $grades;
}

function GetNisitIDChecksum($nisitid){
    $nisitid = (int) $nisitid;
    $id_len = ceil(log10($nisitid));
    $sumcheck = 0;
    $digit = 0;
    if($id_len > 10 || $id_len < 9){
        return false;
    }
    if($id_len==10){
        $nisitid = floor($nisitid/10);
    }
    for($i=9;$i>=1;$i--){
        $digit = $nisitid%10;
        $nisitid = floor($nisitid/10);
        $sumcheck+=$digit*$i;
    }
    $sumcheck%=11;
    $sumcheck = ($sumcheck==0)?1:($sumcheck%10);
    return $sumcheck;
}

function VerifyNisitID($nisitid){
    $nisitid = (int) $nisitid;
    $id_len = ceil(log10($nisitid));
    if($id_len != 10){
        return false;
    }
    return (($nisitid%10)==GetNisitIDChecksum($nisitid));
}