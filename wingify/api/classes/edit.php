<?php

class edit{
	function editname($pid,$pname)
	{

    $con = mysqli_connect("localhost","root","","wingify");
    if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
    $p = mysqli_query($con,"update data set Name = '".$pname."' where Id = ".$pid);		
 if($p){
 $arr=array(
          "status"=>"1",
          "message"=>'Name Updated Successfully',
          );
          }
else {$arr=array(
          "status"=>"0",
          "message"=>'Error Occured. Please Try Again',
          );
 }
  $response=json_encode($arr); 
return $response;
}

function editcost($pid,$pcost)
  {

    $con = mysqli_connect("localhost","root","","wingify");
    if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
    $p = mysqli_query($con,"update data set Price = '".$pcost."' where Id = ".$pid);   
 if($p){
 $arr=array(
          "status"=>"1",
          "message"=>'Cost Updated Successfully',
          );
          }
else {$arr=array(
          "status"=>"0",
          "message"=>'Error Occured. Please Try Again',
          );
 }
  $response=json_encode($arr); 
return $response;
}

function editvendor($pid,$pvendor)
  {

    $con = mysqli_connect("localhost","root","","wingify");
    if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
    $p = mysqli_query($con,"update data set Vendor = '".$pvendor."' where Id = ".$pid);   
 if($p){
 $arr=array(
          "status"=>"1",
          "message"=>'Vendor Successfully',
          );
          }
else {$arr=array(
          "status"=>"0",
          "message"=>'Error Occured. Please Try Again',
          );
 }
  $response=json_encode($arr); 
return $response;
}
}
?>