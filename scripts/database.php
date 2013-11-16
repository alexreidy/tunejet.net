<?php

$db_name = 'tunejet';
$db = new mysqli('127.0.0.1', 'root', '');
$db_exists = $db->select_db($db_name);

if ($db && ! $db_exists) {
    $db->query(" CREATE DATABASE tunejet; ");
    $db->select_db($db_name);
    $db->query("
        CREATE TABLE songs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(100) NOT NULL,
            artist VARCHAR(80) NOT NULL,
            link VARCHAR(300) NOT NULL UNIQUE,
            rating INT NOT NULL
        );
    ");
    $db->query("
        CREATE TABLE playlists (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(40) NOT NULL UNIQUE,
            playlist VARCHAR(10000) NOT NULL,
            rating INT NOT NULL
        );
    ");
}

?>
