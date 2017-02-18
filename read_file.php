<?php
include("/global/mysqli.php");
include("add_to_db.php");
$tables = $conn->query("SHOW TABLES FROM ipdvs");

$sites = $fields = array();
//set directory to read files from
$dir = 'files/db';
//scan directory
$files = scandir($dir);
//iterate files
foreach($files as $file){
  $sites = $fields = array();
  //get info about file
  $file_parts = pathinfo($file);
  //only read in if is a csv or txt file
  if($file_parts['extension'] == 'csv' || $file_parts['extension'] == 'txt'){
    //make array for sites and fields of csv or txt
    $i = 0;
    $handle = @fopen($dir.'/'.$file,"r");
    if($handle){
      while(($row = fgetcsv($handle,4096)) != false){
        //get all fields
        if(empty($fields)){
          $fields = $row;
          continue;
        }
        foreach($row as $k=>$value){
          //get each site info by field
          $sites[$i][$fields[$k]] = $value;
        }
        $i++;
      }
      fclose($handle);
    }
  }
  $fdb = basename($file,$file_parts['extension']);
  $fdb = str_replace('.','',$fdb);
    addToDb($fdb,$sites,$fields);

}
?>
