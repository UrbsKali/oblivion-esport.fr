<?php
// exit if the file is called directly
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    exit("This file cannot be called directly.");
}


$host = "oblivigadmin.mysql.db";
$name = "oblivigadmin";
$user = "oblivigadmin";
$pass = "ccEGk9HFfPXdX4rScHHS";
$port = "3306";
$db = null;

try {
    $db = mysqli_init();
    $link = mysqli_real_connect($db, $host, $user, $pass, $name, $port, NULL, MYSQLI_CLIENT_SSL);
} catch (Throwable $e) {
    echo "Error during connection with DB : " . $e->getMessage() . PHP_EOL;
}

// make $db available in other files
return $db;
