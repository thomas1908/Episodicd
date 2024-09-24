<?php
include 'connexion.php';

if (isset($_GET['id'])) {
    $artistId = intval($_GET['id']);

    // Utilisation de requêtes préparées
    $stmt = $conn->prepare("SELECT * FROM artist WHERE id_artist = ?");
    $stmt->bind_param("i", $artistId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $artist = $result->fetch_assoc();
    } else {
        echo "Artiste non trouvé.";
        exit;
    }

    $stmt->close();
} else {
    echo "ID d'artiste non spécifié.";
    exit;
}

$date = new DateTime($artist['birth_date']);
$formattedDate = $date->format('d F Y');
?>

<html>
<head>
    <title>Détails de l'artiste - <?= htmlspecialchars($artist['name']) ?></title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <header>
        <section>
            <h1 class="site-logo">
                <img class='logo' src="logo Episodicd.webp" alt="Episodicd">
                <a href="/Episodicd" class="logo replace">Episodicd</a>
            </h1>
        </section>
    </header>
    <div id="content" class="site-body">
        <div class="content-wrap">
            <section id="artist-details" class="section">
                <h2 class="section-heading"><?= htmlspecialchars($artist['name']) ?></h2>
                <img  class="cover-image" src="<?= htmlspecialchars($artist['photo'], ENT_QUOTES, 'UTF-8') ?>" alt="Photo de <?= htmlspecialchars($artist['name'], ENT_QUOTES, 'UTF-8') ?>">
                <h3 class="artist-type">Genre: <?= htmlspecialchars($artist['genre']) ?></h3>
                <h3 class="artist-birth">Date de naissance: <?= htmlspecialchars($formattedDate) ?></h3>
                <h3 class="artist-nationality">Nationalité: <?= htmlspecialchars($artist['nationality']) ?></h3>
                <h3>Biographie:</h3>
                <p class="artist-biography"><?= nl2br(htmlspecialchars($artist['biography'])) ?></p>
            </section>
        </div>
    </div>
</body>
</html>