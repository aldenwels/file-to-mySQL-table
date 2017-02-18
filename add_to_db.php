<?php
function addToDb($fdb,$sites,$fields){
  include("/global/mysqli.php");
  $conn->query("DELETE FROM ipdvs_".$fdb);
  print_r($fields);
  $sqlFields = array();
  print($fdb);
  checkIfTableExists($fdb,$fields);
  foreach($fields as $f){
    //check if field exist
    $hold = translateField($f);
    checkIfFieldExists($fdb,$hold);
    //translate fields
    array_push($sqlFields,translateField($f));
  }
  foreach($sites as $site){
    $sqlString = "";
    $valueString = "";
    foreach($fields as $field){
      // INSERT INTO table ($sqlString) VALUES ($valueString)
      if($field != '.')
        $valueString = $valueString."'".$site[$field]."',";
    }
    foreach($sqlFields as $s){
      if($s != '.')
        $sqlString = $sqlString.$s.',';
    }
    //Remove excess comma
    $sqlString = rtrim($sqlString,',');
    $valueString = rtrim($valueString,',');
    //add row to table
    addRow($fdb,$sqlString,$valueString);
  }
}

function translateField($field){
  if(strpos($field,' ') != false){
    $fieldarr = explode(" ",$field);
    $field = " ";
    foreach($fieldarr as $f){
      //print_r($f);
      $field = $field.$f."_";
    }
    $field = rtrim($field,"_");
    return $field;
  }
  else {
    return $field;
  }
}

function addRow($fdb,$sql,$values){
  include("/global/mysqli.php");
  $query = "INSERT INTO ipdvs_".$fdb." (".$sql.") VALUES (".$values.")";
  $conn->query($query);
}

function checkIfFieldExists($fdb,$field){
  include("/global/mysqli.php");
  $result = $conn->query("SHOW COLUMNS FROM ipdvs_".$fdb." LIKE ".$field);
  if($result == false){
    $addQuery = "ALTER TABLE ipdvs_".$fdb." ADD ".$field." varchar(255)";
    $conn->query($addQuery);
  }
}

function checkIfTableExists($fdb,$fields){
  print_r($fdb);
  include("/global/mysqli.php");
  $result = $conn->query("SELECT * FROM ipdvs_".$fdb);
  //if table doesnt exist make
  if($result == false){
    $fieldString = "";
    foreach($fields as $field){
      $fieldString = $fieldString.$field." varchar(255),";
    }
    $fieldString = rtrim($fieldString,",");
    $query = "CREATE TABLE ipdvs_".$fdb." (".$fieldString.")";
    print_r($query);
    $conn->query($query);
  }
}

?>
