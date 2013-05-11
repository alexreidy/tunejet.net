<?php

$address = "127.0.0.1"; $user = "root"; $pass = "";
$conn = mysql_connect($address, $user, $pass);
$db_exists = mysql_select_db("tunejet");

if ($conn && ! $db_exists) {
    mysql_query(" CREATE DATABASE tunejet; ", $conn);
    mysql_select_db("tunejet");
    mysql_query("
        CREATE TABLE songs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(100) NOT NULL,
            artist VARCHAR(80) NOT NULL,
            link VARCHAR(300) NOT NULL UNIQUE,
            rating INT NOT NULL
        );
    ", $conn);
    mysql_query("
        CREATE TABLE playlists (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(40) NOT NULL UNIQUE,
            playlist VARCHAR(10000) NOT NULL,
            rating INT NOT NULL
        );
    ", $conn);
}

?>
