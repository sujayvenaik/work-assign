<?php

class search{
	function searchInfo($pid)
	{
    $con = mysqli_connect("localhost","root","","wingify");
    if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
    $p = mysqli_query($con,"select * from data where Id = ".$pid."");		
  $pr = mysqli_fetch_row($p);
 if($pr){
 $arr=array(
          "status"=>"1",
          "pId"=>$pr[0],
          "pName"=>$pr[1],
          "pCost"=>$pr[2],
          "pVendor"=>$pr[3],
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