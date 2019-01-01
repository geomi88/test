<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
$con = mysqli_connect("localhost", "root", "zoondia","yaaydb");



if($_POST['submit'])
{
$name=$_FILES["excel"]["name"] . "<br>";
    	
$type=$_FILES["excel"]["type"] . "<br>";
$size=($_FILES["excel"]["size"] / 1) . " kB";
$tmp_name=$_FILES["excel"]["tmp_name"] . "<br>";
move_uploaded_file($_FILES["excel"]["tmp_name"],$_FILES["excel"]["name"]);
    

/** PHPExcel_IOFactory */
include 'Classes/PHPExcel/IOFactory.php';
$inputFileName =$_FILES["excel"]["name"];
//echo 'Loading file ',pathinfo($inputFileName,PATHINFO_BASENAME),' using IOFactory to identify the format<br />';
$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
$sheetCount = $objPHPExcel->getSheetCount();



echo '<hr />';
for($sheetindex=0;$sheetindex<1;$sheetindex++)
{

$sheetData = $objPHPExcel->getSheet($sheetindex)->toArray(null,true,true,true);
$h=count($sheetData);
for($j=1;$j<=$h;$j++)
{
	$code=$sheetData[$j]['A'];
	$name=$sheetData[$j]['B'];
	$result = $con->query("SELECT * FROM `cities` WHERE `code`='$code'");
		while ($row = mysqli_fetch_assoc($result)) {
			$city_id = $row['id'];
		    }
	$insert_result=mysqli_query($con,"insert into areas(cityId,areaName) values ($city_id,'$name')");

	 
}
}}
?>
<form action="" enctype="multipart/form-data" method="post">
<input type="file" name="excel">
<input type="submit" name="submit" value="submit">
</form>
