<?php
include('../connexion.php');
include('../header.php');

$UserName = isset($_GET['username']) ? urldecode($_GET['username']) : null;

if (!$UserName) {
    echo "Nom de l'utilisateur non spécifié.";
    exit;
}

$stmt = $conn->prepare("SELECT id, username, profile_picture FROM users WHERE slug = ?");
$stmt->bind_param("s", $UserName);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $users = $result->fetch_assoc();
} else {
    echo "Utilisateur non trouvé.";
    exit;
}
$stmt->close();
?>

<html>
<head>
    <title><?= htmlspecialchars($users['username']) ?>'s profile</title>
    <link rel="stylesheet" type="text/css" href="../style.css">
    <link rel="icon" type="image/webp" href="logo Episodicd.webp" />
</head>
<body>
    <div id="content" class="site-body">
        <div class="user-content-wrap">
            <h2 class="section-heading"><?= htmlspecialchars($users['username']) ?></h2>
            <section id="user-details" class="section">
                <img  class="profil-picture" src="../<?= htmlspecialchars($users['profile_picture']) ?>" alt="Profil picture of <?= htmlspecialchars($users['username'], ENT_QUOTES, 'UTF-8') ?>">
                <div class="user-content">
                    <h2><?= htmlspecialchars($users['username']) ?></h2>
                </div>
            </section>
        </div>
    </div>
    <?php 
include ('../footer.php');
?>
</body>
</html>