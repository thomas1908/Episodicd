<?php
include 'connexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Tous les champs sont obligatoires.']);
        exit;
    }

    // Vérifier les informations d'identification
    $stmt = $conn->prepare("SELECT id, password_hash FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $password_hash);
        $stmt->fetch();

        if (password_verify($password, $password_hash)) {
            $session_id = session_create_id();
            $user_id = intval($user_id);
            echo json_encode(['success' => true, 'message' => 'Connexion réussie', 'session_id' => $session_id, 'user_id' => $user_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Mot de passe incorrect.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Nom d\'utilisateur introuvable.']);
    }

    $stmt->close();
    $conn->close();
}