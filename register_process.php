<?php
// Activer les erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'connexion.php';

header('Content-Type: application/json'); // Définir le type de contenu


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Validation des champs
    if (empty($username) || empty($password) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Tous les champs sont obligatoires.']);
        exit; // Arrêter l'exécution après l'envoi du JSON
    }

    // Hashage du mot de passe
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insérer dans la base de données
    $stmt = $conn->prepare("INSERT INTO users (username, password_hash, email) VALUES (?, ?, ?)");
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Erreur SQL: ' . $conn->error]);
        exit; // Arrêter l'exécution après l'envoi du JSON
    }

    $stmt->bind_param("sss", $username, $password_hash, $email);

    try {
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Inscription réussie']);
    } catch (mysqli_sql_exception $e) {
        // Gestion des erreurs : doublon d'email
        if ($e->getCode() === 1062) { // Code d'erreur pour duplicata
            echo json_encode(['success' => false, 'message' => 'Nom d\'utilisateur ou email déjà utilisé.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'inscription: ' . $e->getMessage()]);
        }
    }

    $stmt->close();
    $conn->close();
}