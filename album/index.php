<?php
include('../functions.php');
include('../connexion.php');
include('../header.php');

$url = $_SERVER['REQUEST_URI'];

$pathParts = explode('/album/', $url);
if (isset($pathParts[1])) {
    $GetalbumName = trim($pathParts[1]);
    $AlbumName = urldecode($GetalbumName);
} else {
    echo "Nom de l'album non spécifié.";
    exit;
}

// Récupérer les informations de l'album
$stmt = $conn->prepare("SELECT * FROM albums WHERE slug = ?");
$stmt->bind_param("s", $AlbumName); // Utiliser slug au lieu de name
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $album = $result->fetch_assoc();
} else {
    echo "Album non trouvé.";
    exit;
}
$stmt->close();

$artistId = $album['artist_id'];

// Récupérer les informations de l'artiste
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

$albumId = $album['id'];

// Récupérer les pistes de l'album
$stmtTracks = $conn->prepare("SELECT id, name, duration, track_number FROM track WHERE album_id = ? ORDER BY track_number");
$stmtTracks->bind_param("i", $albumId);
$stmtTracks->execute();
$resultTracks = $stmtTracks->get_result();

// Formater la date de sortie
$date = new DateTime($album['release_date']);
$formattedDate = $date->format('d F Y');

$slugArtist = slugify($artist['artist_name']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'album - <?= htmlspecialchars($album['name']) ?></title>
    <link rel="stylesheet" type="text/css" href="../style.css">
    <link rel="icon" type="image/webp" href="../../logo_Episodicd.webp" />
</head>
<body>
    <div id="content" class="site-body">
        <div class="album-content-wrap">
            <h2 class="section-heading"><?= htmlspecialchars($album['name']) ?></h2>
            <section id="album-details" class="section">
                <img class="cover-image" src="<?= htmlspecialchars($album['cover'], ENT_QUOTES, 'UTF-8') ?>" alt="Cover of <?= htmlspecialchars($album['name'], ENT_QUOTES, 'UTF-8') ?>">
                <div class="album-content">
                    <h2><?= htmlspecialchars($album['name']) ?></h2>
                    <a href="../artist/<?= $slugArtist ?>" class="album-artist">
                        <div class="artist-container">
                            <?php $imageData = base64_encode($artist['photo']); ?>
                            <img src="data:image/jpeg;base64,<?= $imageData ?>" alt="<?= htmlspecialchars($artist['artist_name']) ?>" class="artist-cover" />
                            <span class="artist-name"><?= htmlspecialchars($artist['artist_name']) ?></span>
                        </div>
                    </a>
                    <ul class="css-1s16397">
                        <li class="dMJfv"><?= htmlspecialchars($album['track_number']) ?> titres</li>
                        <li class="dMJfv"><?= htmlspecialchars($album['length']) ?> minutes</li>
                        <li class="dMJfv"><?= htmlspecialchars($formattedDate) ?></li>
                    </ul>
                </div>
            </section>
            <section id="album-tracks" class="section">
                <div class="section-heading">
                    <h2 class="tracklist-album">Liste des titres</h2>
                </div>
                <ul class="track-list">
                    <?php if ($resultTracks->num_rows > 0): ?>
                        <?php while ($track = $resultTracks->fetch_assoc()): ?>
                            <li class="track-list">
                                <div class="track-regroup">
                                    <img src="<?= htmlspecialchars($album['cover']) ?>" alt="Cover of <?= htmlspecialchars($track['name']) ?>" class="track-cover" />
                                    <p class="track-number"><?= htmlspecialchars($track['track_number']) ?>. <?= htmlspecialchars($track['name']) ?></p>
                                </div>
                                <?php
                                $duration = intval($track['duration']);
                                $minutes = floor($duration / 60);
                                $seconds = $duration % 60;
                                ?>
                                <p class="track-name"><?= sprintf('%02d:%02d', $minutes, $seconds) ?></p>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li class="zero-result">Aucun résultat trouvé.</li>
                    <?php endif; ?>
                </ul>
            </section>
        </div>
    </div>
    <?php include('../footer.php'); ?>
</body>
</html>
