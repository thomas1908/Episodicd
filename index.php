<?php
include 'connexion.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
} else {
    echo "Utilisateur non connecté.";
    exit;
}

$sqlUser = "SELECT username, profile_picture FROM users WHERE id = ?";
$stmt = $conn->prepare($sqlUser);
$stmt->bind_param("i", $userId);
$stmt->execute();
$resultUser = $stmt->get_result();

if ($resultUser->num_rows > 0) {
    $account = $resultUser->fetch_assoc();
} else {
    echo "Compte non trouvé.";
    exit;
}

$sqlAlbums = "SELECT id, name, artist_id, cover FROM albums";
$resultAlbums = $conn->query($sqlAlbums);

$sqlArtist = "SELECT id_artist, name, photo FROM artist";
$resultArtist = $conn->query($sqlArtist);
?>

<html>
<head>
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="icon" type="image/webp" href="logo Episodicd.webp" />
    <script>
        window.onload = function() {
            const sessionId = localStorage.getItem('session_id');
            if (!sessionId) {
                document.getElementById('auth-buttons').style.display = 'block';
                document.getElementById('profil-info').style.display = 'none';
            } else {
                document.getElementById('auth-buttons').style.display = 'none';
            }
        };
    </script>
</head>
<body>
    <header>
        <section class="header-button">
            <h1 class="site-logo">
                <img class='logo' src="logo Episodicd.webp" alt="Episodicd">
                <a href="/Episodicd" class="logo replace">Episodicd</a>
            </h1>
            <div class="login-container" id="auth-buttons">
                <a href="login.php" class="login-button">Connexion</a>
                <a href="register.php" class="register-button">Inscription</a>
            </div>
            <div id="profil-info">
                <div class="profile-menu">
                    <a href="#" class="has-icon toggle-menu">
                        <div class="avatar">
                            <span class="icon"></span>
                            <img src="<?= htmlspecialchars($account['profile_picture']) ?>" alt="Photo de profil" class="user-photo" width="24" height="24"/>
                            <p><?= htmlspecialchars($account['username'])?></p>
                        </div>
                    </a>
                    <ul class="subnav" style="display: none;">
                        <li class="divider"><a href="/Episodicd">Home</a></li>
                        <li><a href="/Episodicd/profil">Profile</a></li>
                        <li><a href="/thomas__pttr/films/">Films</a></li>
                        <li><a href="/thomas__pttr/films/diary/">Diary</a></li>
                        <li><a href="/thomas__pttr/films/reviews/">Reviews</a></li>
                        <li><a href="/thomas__pttr/watchlist/">Watchlist</a></li>
                        <li><a href="/thomas__pttr/lists/">Lists</a></li>
                        <li><a href="/thomas__pttr/likes/">Likes</a></li>
                        <li><a href="/thomas__pttr/tags/">Tags</a></li>
                        <li><a href="/thomas__pttr/following/">Network</a></li>
                        <li class="divider"><a href="/settings/">Settings</a></li>
                        <li><a href="/settings/subscriptions/">Subscriptions</a></li>
                        <li id="sign-out"><a href="/Episodicd" onclick="localStorage.removeItem('session_id'); localStorage.removeItem('user_id');">Sign Out</a></li>
                    </ul>
                </div>
            </div>
            <div class="search-bar-container">
                <form action="search.php" method="GET">
                    <input type="text" name="query" class="search-bar" placeholder="Rechercher un album ou un artiste..." autocomplete="off">
                    <button type="submit" class="search-button">Rechercher</button>
                </form>
            </div>
        </section>
        <script src="script.js"></script>
    </header>
    <div id="content" class="site-body">
        <div class="content-wrap">
            <section class="section">
                <h2 class="section-heading">ALBUMS</h2>
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
                        <li class="zero-result">Aucun album disponible.</li>
                    <?php endif; ?>
                </ul>
            </section>
            <section class="section">
                <h2 class="section-heading">ARTISTS</h2>
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
                        <li class="zero-result">Aucun artiste disponible.</li>
                    <?php endif; ?>
                </ul>
            </section>
        </div>
    </div>
</body>
</html>