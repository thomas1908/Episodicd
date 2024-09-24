<?php
include 'connexion.php';

if (isset($_GET['id'])) {
    $albumId = intval($_GET['id']);

    // Fetch the album details
    $stmt = $conn->prepare("SELECT * FROM albums WHERE id = ?");
    $stmt->bind_param("i", $albumId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $album = $result->fetch_assoc();
    } else {
        echo "Album non trouvé.";
        exit;
    }

    $stmt->close();
} else {
    echo "ID d'album non spécifié.";
    exit;
}

$artistId = $album['artist_id'];

$stmtArtist = $conn->prepare("SELECT 
    a.id_artist,
    a.name AS artist_name,
    a.photo,
    a.biography,
    a.nationality,
    a.birth_date,
    a.genre
FROM 
    artist a
WHERE 
    a.id_artist = ?");
$stmtArtist->bind_param("i", $artistId);
$stmtArtist->execute();
$resultArtist = $stmtArtist->get_result();

if ($resultArtist->num_rows > 0) {
    $artist = $resultArtist->fetch_assoc();
} else {
    echo "Artiste non trouvé.";
    exit;
}

$date = new DateTime($album['release_date']);
$formattedDate = $date->format('d F Y');
?>

<html>
<head>
    <title>Détails de l'album</title>
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
            <section id="album-details" class="section">
                <div class="album-content">
                    <h2 class="section-heading"><?= htmlspecialchars($album['name']) ?></h2>
                    <img class="cover-image" src="<?= htmlspecialchars($album['cover'], ENT_QUOTES, 'UTF-8') ?>" alt="Cover of <?= htmlspecialchars($album['name'], ENT_QUOTES, 'UTF-8') ?>">
                    <a href="artist.php?id=<?= htmlspecialchars($artist['id_artist']) ?>" class="album-artist">
                        <div class="artist-container">
                            <img src="<?= htmlspecialchars($artist['photo']) ?>" alt="<?= htmlspecialchars($artist['artist_name']) ?>" class="artist-cover" />
                            <span class="artist-name"><?= htmlspecialchars($artist['artist_name']) ?></span>
                        </div>
                    </a>
                    <h3>Genre: <?= htmlspecialchars($album['genre']) ?></h3>
                    <p>Date de sortie: <?= htmlspecialchars($formattedDate) ?></p>
                </div>
                <h3>Description:</h3>
                <p class="album-description"><?= nl2br(htmlspecialchars($album['description'])) ?></p>
            </section>
        </div>
    </div>
</body>
</html>