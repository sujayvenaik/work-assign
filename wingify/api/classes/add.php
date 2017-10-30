<?php

class add{
	function addInfo($pid,$pname,$pcost,$pvendor)
	{
    $message = "hello";
    $con = mysqli_connect("localhost","root","","wingify");
    if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
    $p = mysqli_query($con,"INSERT INTO data(Id,Name, Price, Vendor) VALUES (".$pid.",'".$pname."',".$pcost.",'".$pvendor."')");		
 if($p){
 $arr=array(
          "status"=>"1",
          "message"=>'Data Added Successfully',
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