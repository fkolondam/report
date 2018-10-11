<?php

$serverName = 'LENOVO01\BESTSERVER';
$connectionInfo = array( "Database"=>"DB_BEST12", "UID"=>"sa", "PWD"=>"bestpwd");
$conn = sqlsrv_connect( $serverName, $connectionInfo);

if( $conn ) {
     echo "Connection established.<hr /><br />";
}else{
     echo "Connection could not be established.<hr /><br />";
     die( print_r( sqlsrv_errors(), true));
}
?>
