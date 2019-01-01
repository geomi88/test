<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$host="localhost"; // Host name.
$db_user="root"; //mysql user
$db_password="zoondia"; //mysql pass
$db='yaaydb'; // Database name.
//$conn=mysql_connect($host,$db_user,$db_password) or die (mysql_error());
$conn = mysqli_connect($host,$db_user,$db_password,$db);
//mysql_select_db($db) or die (mysql_error());
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

echo $filename=$_FILES["file"]["name"];
$ext=substr($filename,strrpos($filename,"."),(strlen($filename)-strrpos($filename,".")));
$filename=$_FILES["file"]["tmp_name"];
//we check,file must be have csv extention
if($ext==".csv")
{
  $file = fopen($filename, "r");

         while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE)
         {
            $sql = "INSERT into cities(code,name) values('$emapData[0]','$emapData[1]')";
            $conn->query($sql);
         }
         fclose($file);
         echo "CSV File has been successfully Imported.";
}
else {
    echo "Error: Please Upload only CSV File";
}



?>
