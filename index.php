<?php
require_once('KUGrade.php');

function post_main(){
    header("Access-Control-Allow-Origin: *");
    header('Content-Type: application/json');
    $obj = new KUGrade($_POST['username'], $_POST['password']);
    echo json_encode($obj->getGrade());
}

function get_main(){
    header('Content-Type: text/plain');
    echo file_get_contents('help.txt');
}

if($_SERVER['REQUEST_METHOD'] === 'POST')
    post_main();
else
    get_main();