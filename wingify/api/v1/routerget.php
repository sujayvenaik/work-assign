<?php

include_once 'constant.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$pid=0;$pname="";$pcost=0;
//$type = $_GET ['type'];
//$cid = $_GET ['cid'];
if (isset($_GET['pid']) && !empty($_GET['pid'])) {
    $pid = $_GET ['pid'];
}
if (isset($_GET['pname']) && !empty($_GET['pname'])) {
    $pname = $_GET ['pname'];
}
if (isset($_GET['pcost']) && !empty($_GET['pcost'])) {
    $pcost = $_GET ['pcost'];
}
if (isset($_GET['pvendor']) && !empty($_GET['pvendor'])) {
    $pvendor = $_GET ['pvendor'];
}
$type = $_GET ['ptype'];
$getHeader = emu_getallheaders();
$username = $getHeader['Username'];
$password = $getHeader['Password'];

if ($password == "47672DhQtO2CQHoj" && $username=="wingify") {
    
} else {
    echo $msg = "User authentication failed.";
    return $msg;
}
if ($type == "add") {
    require_once ADD;
    $add = new add();
    $response = $add->addInfo($pid,$pname,$pcost,$pvendor);
    echo $response;
}
if ($type == "edit") {
    require_once EDIT;
    $edit = new edit();
    if(isset($_GET['pname']) && !empty($_GET['pname'])){
    $response = $edit->editname($pid,$pname);
    echo $response;}

    if(isset($_GET['pcost']) && !empty($_GET['pcost'])){
    $response = $edit->editcost($pid,$pcost);
    echo $response;}

    if(isset($_GET['pvendor']) && !empty($_GET['pvendor'])){
    $response = $edit->editvendor($pid,$vendor);
    echo $response;}
}
if ($type == "delete") {
    require_once DEL;
    $delete = new delete();
    $response = $delete->deleteInfo($pid);
    echo $response;
}
if ($type == "search") {
    require_once SEARCH;
    $search = new search();
    $response = $search->searchInfo($pid);
    echo $response;
}
// echo $response;

function emu_getallheaders() {
    foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) == 'HTTP_') {
            $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
            $headers[$name] = $value;
        } else if ($name == "CONTENT_TYPE") {
            $headers["Content-Type"] = $value;
        } else if ($name == "CONTENT_LENGTH") {
            $headers["Content-Length"] = $value;
        }
    }
    return $headers;
}

?>