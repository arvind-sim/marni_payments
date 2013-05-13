<?php
  $ini_array = parse_ini_file("./database.ini", true);      
  echo "array values" . $ini_array['test']['hostName'];
?>
