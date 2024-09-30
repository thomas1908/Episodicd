<?php
require 'connexion.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email, password, profile_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$stmt->close();

$artist_stmt = $conn->prepare("SELECT name FROM artist WHERE user_id = ?");
$artist_stmt->bind_param("i", $user_id);
$artist_stmt->execute();
$artist_result = $artist_stmt->get_result();

$artist_stmt->close();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    echo "Erreur : utilisateur non trouvé.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (password_verify($old_password, $user['password'])) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update_stmt->bind_param("si", $hashed_password, $user_id);

            if ($update_stmt->execute()) {
                echo "Mot de passe mis à jour avec succès.";
            } else {
                echo "Erreur lors de la mise à jour du mot de passe : " . $update_stmt->error;
            }
            $update_stmt->close();
        } else {
            echo "Les nouveaux mots de passe ne correspondent pas.";
        }
    } else {
        echo "L'ancien mot de passe est incorrect.";
    }
}

$form_display = isset($_POST['show_change_password_form']) ? true : false;

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil</title>
    <link rel="stylesheet" type="text/css" href="style.css"> <!-- Lien vers votre fichier CSS -->
</head>
<body>
    <div id="content" class="site-body">
        <div class="content-profil">
            <h1>Bienvenue, <?php echo htmlspecialchars($user['username']); ?>!</h1>
            <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Photo de profil">
            <h2 class="section-heading">Informations du compte :</h2>
            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>

            <form action="" method="POST">
                <button class="login-button" type="submit" name="show_change_password_form">Modifier mon mot de passe</button>
            </form>

            <?php if ($form_display): ?>
                <h2 class="section-heading">Changer le mot de passe :</h2>
                <form action="" method="POST">
                    <input class="search-bar" type="password" name="old_password" required placeholder="Ancien mot de passe">
                    <input class="search-bar" type="password" name="new_password" required placeholder="Nouveau mot de passe">
                    <input class="search-bar" type="password" name="confirm_password" required placeholder="Confirmer le nouveau mot de passe">
                    <button class="login-button" type="submit" name="change_password">Changer le mot de passe</button>
                </form>
            <?php endif; ?>

            <form action="logout.php" method="POST">
                <button class="login-button" type="submit">Déconnexion</button>
            </form>
        </div>
        <div class="content-wrap">
            <h2 class="section-heading">Artistes ajoutés :</h2>
            <ul class="artist-user-list-horizontal">
                <?php
                if ($artist_result->num_rows > 0): 
                    while ($artist = $artist_result->fetch_assoc()):
                        ?>
                        <li><?=htmlspecialchars($artist['name'])?></li>
                    <?php endwhile; 
                else: ?>
                    <li class="zero-result">Vous n'avez ajouté aucun artiste.</li>
                <?php endif; ?>
            </ul>
            </ul>
        </div>
    </div>
<?php 
include 'footer.php'; // Inclusion du footer
?>
</body>
</html>
