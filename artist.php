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

$stmtAlbums = $conn->prepare("SELECT id, name, cover FROM albums WHERE artist_id = ?");
$stmtAlbums->bind_param("i", $artistId);
$stmtAlbums->execute();
$resultAlbums = $stmtAlbums->get_result();

$date = new DateTime($artist['birth_date']);
$formattedDate = $date->format('d F Y');
?>

<html>
<head>
    <title>Détails de l'artiste - <?= htmlspecialchars($artist['name']) ?></title>
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
        <div class="artist-content-wrap">
            <h2 class="section-heading"><?= htmlspecialchars($artist['name']) ?></h2>
            <section id="artist-details" class="section">
                <img  class="cover-image" src="<?= htmlspecialchars($artist['photo'], ENT_QUOTES, 'UTF-8') ?>" alt="Photo de <?= htmlspecialchars($artist['name'], ENT_QUOTES, 'UTF-8') ?>">
                <div class="artist-content">
                    <h2><?= htmlspecialchars($artist['name']) ?></h2>
                    <ul class="css-1s16398">
                        <li class="dMJfv"><?= htmlspecialchars($artist['genre']) ?></li>
                        <li class="dMJfv"><?= htmlspecialchars($formattedDate) ?></li>
                    </ul>
                </div>
            </section>
            <section class="artist-discography">
                <h2 class="section-heading">Discographie</h2>
                <ul class="discography-list-horizontal">
                    <?php if ($resultAlbums->num_rows > 0): ?>
                        <?php while ($rowAlbums = $resultAlbums->fetch_assoc()): ?>
                            <li class="discography-list-horizontal">
                                <a href="album.php?id=<?= htmlspecialchars($rowAlbums['id'])?>" data-original-title="<?= htmlspecialchars($rowAlbums['name'])?>">
                                <img src="<?= htmlspecialchars($rowAlbums['cover'], ENT_QUOTES, 'UTF-8')?>" alt="Cover of <?= htmlspecialchars($rowAlbums['name'], ENT_QUOTES, 'UTF-8')?>">
                                </a>
                                <a href="album.php?id=<?= htmlspecialchars($rowAlbums['id'])?>"><h2 class="artist-album-title"><?= htmlspecialchars($rowAlbums['name'])?></h2></a>
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