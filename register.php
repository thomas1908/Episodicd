<?php
session_start();
require 'connexion.php'; // Assurez-vous que ce chemin est correct

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $profile_picture = '';

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        // Vérifiez si le fichier est bien une image
        $fileType = $_FILES['profile_picture']['type'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    
        if (in_array($fileType, $allowedTypes)) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
    
            // Assurez-vous que le dossier existe
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true); // Crée le dossier si nécessaire
            }
    
            // Déplacez le fichier téléchargé
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                $profile_picture = $target_file; // Stockez le chemin du fichier
            } else {
                echo "Erreur lors du téléchargement de l'image : Impossible de déplacer le fichier.";
                exit;
            }
        } else {
            echo "Erreur: type de fichier non autorisé.";
            exit;
        }
    } else {
        echo "Erreur lors du téléchargement de l'image.";
        exit;
    }
    

    // Préparez et exécutez l'insertion des données
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, profile_picture) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $profile_picture);

    if ($stmt->execute()) {
        // Redirigez l'utilisateur vers la page de connexion
        header("Location: login.php");
        exit();
    } else {
        echo "Erreur lors de l'insertion: " . $stmt->error;
    }

    $stmt->close(); // Fermez la déclaration
}

$conn->close(); // Fermez la connexion
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
</head>
<body>
    <h1>Inscription</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="text" name="username" required placeholder="Nom d'utilisateur">
        <input type="email" name="email" required placeholder="Email">
        <input type="password" name="password" required placeholder="Mot de passe">
        <input type="file" name="profile_picture" accept="image/*">
        <button type="submit">S'inscrire</button>
    </form>
</body>
</html>
