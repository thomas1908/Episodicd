<?php
session_start();
require 'connexion.php'; // Assurez-vous que ce chemin est correct

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Préparez la requête pour récupérer l'utilisateur
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Vérifiez le mot de passe
        if (password_verify($password, $user['password'])) {
            // Mettez les informations de l'utilisateur dans la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['profile_picture'] = $user['profile_picture'];

            // Redirigez vers une page protégée
            header("Location: index.php");
            exit();
        } else {
            echo "Mot de passe incorrect.";
        }
    } else {
        echo "Aucun utilisateur trouvé avec cet email.";
    }

    $stmt->close(); // Fermez la déclaration
}

$conn->close(); // Fermez la connexion
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
</head>
<body>
    <h1>Connexion</h1>
    <form action="" method="POST">
        <input type="email" name="email" required placeholder="Email">
        <input type="password" name="password" required placeholder="Mot de passe">
        <button type="submit">Se connecter</button>
    </form>
</body>
</html>
