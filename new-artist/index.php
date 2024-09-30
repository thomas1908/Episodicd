<?php include '../header.php';
include '../functions.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Artiste</title>
    <link rel="stylesheet" type="text/css" href="../style.css">
    <link rel="icon" type="image/webp" href="../../logo_Episodicd.webp" />
</head>
<body>
    <?php
    if (!isset($_SESSION['user_id'])) {
        echo "Erreur: Vous devez être connecté pour ajouter un artiste.";
        exit;
    }
    ?>
    <h1 class="add_artist">Ajouter un Artiste</h1>

    <!-- Formulaire pour ajouter un artiste -->
    <form class="add_artist" action="" method="post" enctype="multipart/form-data">
        <!-- Champ pour le nom de l'artiste -->
        <label for="name">Nom de l'artiste:</label><br>
        <input type="text" id="name" name="name" required><br><br>
        
        <!-- Champ pour télécharger une photo -->
        <label for="photo">Photo:</label><br>
        <input type="file" id="photo" name="photo" accept="image/*" required><br><br>
        
        <!-- Champ pour la biographie de l'artiste -->
        <label for="biography">Biographie:</label><br>
        <textarea id="biography" name="biography" required></textarea><br><br>
        
        <!-- Champ pour la nationalité de l'artiste -->
        <label for="nationality">Nationalité:</label><br>
        <input type="text" id="nationality" name="nationality" required><br><br>
        
        <!-- Champ pour la date de naissance de l'artiste -->
        <label for="birth_date">Date de naissance / Date de création :</label><br>
        <input type="date" id="birth_date" name="birth_date" required><br><br>
        
        <!-- Champ pour le genre de l'artiste (musique, peinture, etc.) -->
        <label for="genre">Genre musical:</label><br>
        <input type="text" id="genre" name="genre" required><br><br>
        
        <!-- Bouton pour soumettre le formulaire -->
        <input type="submit" value="Ajouter">
    </form>

    <?php
    include '../connexion.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        } else {
            echo "Erreur: Utilisateur non connecté.";
            exit;
        }
        $name = $_POST['name'];
        $slug = slugify($name);

        $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM artist WHERE name = ?");
        $stmtCheck->bind_param("s", $name);
        $stmtCheck->execute();
        $stmtCheck->bind_result($count);
        $stmtCheck->fetch();
        $stmtCheck->close();

        if ($count > 0) {
            echo "Erreur: L'artiste '$name' existe déjà dans la base de données.";
        } else {
            $stmt = $conn->prepare("INSERT INTO artist (name, slug, photo, biography, nationality, birth_date, genre, user_id) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssi", $name, $slug, $photo, $biography, $nationality, $birth_date, $genre, $user_id); // Ajout du user_id

            $biography = $_POST['biography'];
            $nationality = $_POST['nationality'];
            $birth_date = $_POST['birth_date'];
            $genre = $_POST['genre'];

            if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
                $fileType = $_FILES['photo']['type'];
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

                if (in_array($fileType, $allowedTypes)) {
                    $photo = file_get_contents($_FILES['photo']['tmp_name']);
                } else {
                    echo "Erreur: type de fichier non autorisé.";
                    exit;
                }
            } else {
                echo "Erreur lors du téléchargement de l'image.";
                exit;
            }

            if ($stmt->execute()) {
                echo "Nouvel artiste ajouté avec succès";
            } else {
                echo "Erreur lors de l'ajout: " . $stmt->error;
            }

            $stmt->close();
        }
    }
    ?>
</body>
</html>
