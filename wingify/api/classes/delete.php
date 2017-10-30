<?php

class delete{
	function deleteInfo($pid)
	{
    $con = mysqli_connect("localhost","root","","wingify");
    if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
    $p = mysqli_query($con,"DELETE FROM `data` WHERE `Id` = ".$pid."");		
 if($p){
 $arr=array(
          "status"=>"1",
          "message"=>'Data Deleted Successfully',
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