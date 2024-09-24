<?php
$host = 'localhost';
$port = 3308;
$dbname = 'episodicd';
$username = 'root';
$password = 'lulupopo17';

// Connexion a la db
$conn = new mysqli($host, $username, $password, $dbname, port: $port);

// verif de la connexion
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}