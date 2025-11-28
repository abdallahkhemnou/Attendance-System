<?php
require_once 'db_connect.php';

$conn = getDBConnection();
if ($conn) {
    echo "Connection successful!";
} else {
    echo "Connection failed!";
}
?>