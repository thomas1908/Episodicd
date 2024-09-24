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

// Récupérer la tracklist de l'album
$stmtTracks = $conn->prepare("SELECT id, name, duration, track_number FROM track WHERE album_id = ? ORDER BY id");
$stmtTracks->bind_param("i", $albumId);
$stmtTracks->execute();
$resultTracks = $stmtTracks->get_result();

$date = new DateTime($album['release_date']);
$formattedDate = $date->format('d F Y');
?>

<html>
<head>
    <title>Détails de l'album</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="icon" type="image/webp" href="logo Episodicd.webp" />
</head>
<body>
    <header>
        <section>
            <h1 class="site-logo">
                <img class='logo' src="logo Episodicd.webp" alt="Episodicd">
                <a href="/Episodicd" class="logo replace">Episodicd</a>
            </h1>
            <div class="search-bar-container">
                <form action="search.php" method="GET">
                    <input type="text" name="query" class="search-bar" placeholder="Rechercher un album ou un artiste..." autocomplete="off">
                    <button type="submit" class="search-button">Rechercher</button>
                </form>
            </div>
        </section>
    </header>
    <div id="content" class="site-body">
        <div class="album-content-wrap">
            <h2 class="section-heading"><?= htmlspecialchars($album['name']) ?></h2>
            <section id="album-details" class="section">
                <img class="cover-image" src="<?= htmlspecialchars($album['cover'], ENT_QUOTES, 'UTF-8') ?>" alt="Cover of <?= htmlspecialchars($album['name'], ENT_QUOTES, 'UTF-8') ?>">
                <div class="album-content">
                    <h2><?= htmlspecialchars($album['name']) ?></h2>
                    <a href="artist.php?id=<?= htmlspecialchars($artist['id_artist']) ?>" class="album-artist">
                        <div class="artist-container">
                            <img src="<?= htmlspecialchars($artist['photo']) ?>" alt="<?= htmlspecialchars($artist['artist_name']) ?>" class="artist-cover" />
                            <span class="artist-name"><?= htmlspecialchars($artist['artist_name']) ?></span>
                        </div>
                    </a>
                    <ul class="css-1s16397">
                        <li class="dMJfv"><?= htmlspecialchars($album['track_number']) ?> titres</li>
                        <li class="dMJfv"><?= htmlspecialchars($album['lenght']) ?> minutes</li>
                        <li class="dMJfv"><?= htmlspecialchars($formattedDate) ?></li>
                    </ul>
                </div>
            </section>
            <section id="album-tracks" class="section">
                <div class="section-heading">
                    <h2 class="tracklist-album">Titre</h2>
                    <svg viewBox="0 0 24 24" focusable="false" class="chakra-icon css-c1x3e4" data-testid="ClockOutlinedIcon">
                        <path d="M11.335 8v4.275l3.195 3.195.94-.94-2.805-2.805V8h-1.33Z"></path>
                        <path fill-rule="evenodd" d="M4 12c0-5.138 2.862-8 8-8 5.137 0 8 2.862 8 8 0 5.137-2.863 8-8 8-5.138 0-8-2.863-8-8Zm1.333 0c0 4.424 2.243 6.667 6.667 6.667 4.424 0 6.667-2.243 6.667-6.667 0-4.424-2.243-6.667-6.667-6.667-4.424 0-6.667 2.243-6.667 6.667Z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <ul class="track-list">
                    <?php if ($resultTracks->num_rows > 0): ?>
                        <?php while ($track = $resultTracks->fetch_assoc()): ?>
                            <li class="track-list">
                                <div class="track-regroup"><img src="<?=htmlspecialchars($album['cover']) ?>" alt="Cover of <?=htmlspecialchars($track['name']) ?>" class="track-cover" />
                                    <p class="track-number"><?=htmlspecialchars($track['track_number']) ?>.&nbsp&nbsp<?= htmlspecialchars($track['name']) ?></p>
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
</body>
</html>
