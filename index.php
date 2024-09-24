<?php
include 'connexion.php';

// Requête SQL pour récupérer les données des différentes tables
$sqlAlbums= "SELECT id, name, artist_id, cover FROM albums";
$resultAlbums = $conn->query($sqlAlbums);

$sqlArtist= "SELECT id_artist, name, photo FROM artist";
$resultArtist = $conn->query($sqlArtist);
?>

<html>
<head>
    <title>Home</title>
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
        <div class="content-wrap">
            <section id="favourites" class="section">
                <h2 class="section-heading">FAVORITE ALBUMS</h2>
                <ul class="poster-list-horizontal">
                    <?php if ($resultAlbums->num_rows > 0): ?>
                        <?php while ($rowAlbums = $resultAlbums->fetch_assoc()): ?>
                            <li class="image-poster-list-horizontal">
                                <a href="album.php?id=<?= htmlspecialchars($rowAlbums['id']) ?>" data-original-title="<?= htmlspecialchars($rowAlbums['name']) ?>">
                                    <img src="<?= htmlspecialchars($rowAlbums['cover'], ENT_QUOTES, 'UTF-8') ?>" alt="Cover of <?= htmlspecialchars($rowAlbums['name'], ENT_QUOTES, 'UTF-8') ?>">
                                </a>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li>Aucun album disponible.</li>
                    <?php endif; ?>
                </ul>
            </section>
            <section id="favourites" class="section">
                <h2 class="section-heading">FAVORITE ARTIST</h2>
                <ul class="poster-list-horizontal">
                    <?php if ($resultArtist->num_rows > 0): ?>
                        <?php while ($rowArtist = $resultArtist->fetch_assoc()): ?>
                            <li class="image-poster-list-horizontal">
                                <a href="artist.php?id=<?= htmlspecialchars($rowArtist['id_artist']) ?>" data-original-title="<?= htmlspecialchars($rowArtist['name']) ?>">
                                    <img src="<?= htmlspecialchars($rowArtist['photo'], ENT_QUOTES, 'UTF-8') ?>" alt="Cover of <?= htmlspecialchars($rowArtist['name'], ENT_QUOTES, 'UTF-8') ?>">
                                </a>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li>Aucun album disponible.</li>
                    <?php endif; ?>
                </ul>
            </section>
        </div>
    </div>
</body>
</html>