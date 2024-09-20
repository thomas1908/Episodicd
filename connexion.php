<?php
$host = 'localhost';
$dbname = 'episodicd';
$username = 'root';
$password = '';

// Créer la connexion
$conn = new mysqli($host, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}
?>