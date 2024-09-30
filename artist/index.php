<?php
include('../connexion.php');
include('../header.php');
include('../functions.php');

$ArtistName = isset($_GET['name']) ? urldecode($_GET['name']) : null;

if (!$ArtistName) {
    echo "Nom de l'artiste non spécifié.";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM artist WHERE slug = ?");
$stmt->bind_param("s", $ArtistName);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $artist = $result->fetch_assoc();
} else {
    echo "Artiste non trouvé.";
    exit;
}
$stmt->close();

$artistId = $artist['id_artist'];

$imageData = base64_encode($artist['photo']);
$src = 'data:image/jpeg;base64,' . $imageData;

$typeFilter = isset($_GET['type']) ? $_GET['type'] : 'all';
$typeCondition = $typeFilter !== 'all' ? "AND type = ?" : "";

if ($typeFilter !== 'all') {
    $stmtAlbums = $conn->prepare("SELECT id, name, cover FROM albums WHERE artist_id = ? AND type = ?");
    $stmtAlbums->bind_param("is", $artistId, $typeFilter);
} else {
    $stmtAlbums = $conn->prepare("SELECT id, name, cover FROM albums WHERE artist_id = ?");
    $stmtAlbums->bind_param("i", $artistId);
}

$stmtAlbums->execute();
$resultAlbums = $stmtAlbums->get_result();

$date = new DateTime($artist['birth_date']);
$formattedDate = $date->format('d F Y');
?>

<html>
<head>
    <title>Détails de l'artiste - <?= htmlspecialchars($artist['name']) ?></title>
    <link rel="stylesheet" type="text/css" href="../style.css">
    <link rel="icon" type="image/webp" href="logo Episodicd.webp" />
</head>
<body>
    <div id="content" class="site-body">
        <div class="artist-content-wrap">
            <h2 class="section-heading"><?= htmlspecialchars($artist['name']) ?></h2>
            <section id="artist-details" class="section">
                <img class="cover-image" src="<?php echo $src; ?>" alt="Photo de <?= htmlspecialchars($artist['name'], ENT_QUOTES, 'UTF-8') ?>">
                <div class="artist-content">
                    <h2><?= htmlspecialchars($artist['name']) ?></h2>
                    <ul class="css-1s16398">
                        <li class="dMJfv"><?= htmlspecialchars($artist['genre']) ?></li>
                        <li class="dMJfv"><?= htmlspecialchars($formattedDate) ?></li>
                    </ul>
                </div>
            </section>
            <section class="artist-discography">
                <h2 class="section-heading">
                    <form method="GET" action="" class="filter-form">
                        <input type="hidden" name="name" value="<?= htmlspecialchars($ArtistName) ?>">
                        <div class="dropdown">
                        <span class="dropdown-label">
                                <?php 
                                $typeFilter = $typeFilter ?: 'all'; 
                                echo htmlspecialchars(ucfirst($typeFilter)); 
                                ?>
                                <i class="ir s icon"></i>
                            </span>
                            <ul class="dropdown-menu">
                                <li><a href="?name=<?= htmlspecialchars($ArtistName) ?>&type=all" <?= $typeFilter === 'all' ? 'class="selected"' : '' ?>>All</a></li>
                                <li><a href="?name=<?= htmlspecialchars($ArtistName) ?>&type=album" <?= $typeFilter === 'album' ? 'class="selected"' : '' ?>>Albums</a></li>
                                <li><a href="?name=<?= htmlspecialchars($ArtistName) ?>&type=single" <?= $typeFilter === 'single' ? 'class="selected"' : '' ?>>Singles</a></li>
                                <li><a href="?name=<?= htmlspecialchars($ArtistName) ?>&type=ep" <?= $typeFilter === 'ep' ? 'class="selected"' : '' ?>>EPs</a></li>
                            </ul>
                        </div>
                    </form>
                </h2>
                <ul class="discography-list-horizontal">
                    <?php if ($resultAlbums->num_rows > 0): ?>
                        <?php while ($rowAlbums = $resultAlbums->fetch_assoc()):
                            $slugAlbum = slugify($rowAlbums['name']);
                            ?>
                            <li class="discography-list-horizontal">
                                <a href="../album/<?= $slugAlbum ?>" data-original-title="<?= htmlspecialchars($rowAlbums['name'])?>">
                                    <img src="<?= htmlspecialchars($rowAlbums['cover'], ENT_QUOTES, 'UTF-8') ?>" alt="Cover of <?= htmlspecialchars($rowAlbums['name'], ENT_QUOTES, 'UTF-8') ?>">
                                </a>
                                <a href="../album/<?= $slugAlbum ?>"><h2 class="artist-album-title"><?= htmlspecialchars($rowAlbums['name']) ?></h2></a>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li class="zero-result">Aucun résultat trouvé.</li>
                    <?php endif; ?>
                </ul>
            </section>
        </div>
    </div>
    <?php 
include ('../footer.php');
?>
</body>
</html>
