<?php

$host = "oblivigadmin.mysql.db";
$name = "oblivigadmin";
$user = "oblivigadmin";
$pass = "ccEGk9HFfPXdX4rScHHS";
$port = "3306";
$db = null;

try {
    $db = mysqli_init();
    $link = mysqli_real_connect($db, $host, $user, $pass, $name, $port, NULL, MYSQLI_CLIENT_SSL);
    $db->query("CREATE TABLE IF NOT EXISTS TWC_INSCRIPTION (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom_equipe VARCHAR(255),
        tag_equipe VARCHAR(255),
        top_name VARCHAR(255),
        top_discord VARCHAR(255),
        top_email VARCHAR(255),
        jungle_name VARCHAR(255),
        jungle_discord VARCHAR(255),
        jungle_email VARCHAR(255),
        middle_name VARCHAR(255),
        middle_discord VARCHAR(255),
        middle_email VARCHAR(255),
        bot_name VARCHAR(255),
        bot_discord VARCHAR(255),
        bot_email VARCHAR(255),
        support_name VARCHAR(255),
        support_discord VARCHAR(255),
        support_email VARCHAR(255),
        logo_url VARCHAR(255),
        motivation_text TEXT,
        have_logo BOOLEAN
    );");
    $db->close();
    echo "Table created successfully";
} catch (Throwable $e) {
    echo "Captured Throwable for connection : " . $e->getMessage() . PHP_EOL;
}





