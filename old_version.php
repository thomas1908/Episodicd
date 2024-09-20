<?php
include 'connexion.php';

// Requête SQL pour récupérer les données
$sqlAlbums= "SELECT name, artist, cover FROM albums";
$resultAlbums = $conn->query($sqlAlbums);

$sqlArtist= "SELECT name, photo FROM artist";
$resultArtist = $conn->query($sqlArtist);
?>

<html>
<head>
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <header>
        <section><!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Albums Spotify</title>
  <style>
    body {
      font-family: Arial, sans-serif;
    }
    .album {
      display: inline-block;
      margin: 20px;
      text-align: center;
    }
    img {
      width: 150px;
      height: 150px;
      object-fit: cover;
    }
  </style>
</head>
<body>

<h1>Albums - Pray for Paris</h1>
<div id="albums"></div>

<script>
  // Token d'autorisation (insère ton vrai token ici)
  const accessToken = '1POdFZRZbvb...qqillRxMr2z';

  // Requête à l'API Spotify
  fetch('https://api.spotify.com/v1/search?q=Pray+for+Paris&type=album', {
    method: 'GET',
    headers: {
      'Authorization': 'Bearer ' + accessToken
    }
  })
  .then(response => response.json())
  .then(data => {
    const albums = data.albums.items;
    const albumsContainer = document.getElementById('albums');

    // Boucle à travers les albums et créer des éléments HTML pour chacun
    albums.forEach(album => {
      const albumElement = document.createElement('div');
      albumElement.classList.add('album');

      // Nom de l'album
      const albumTitle = document.createElement('h3');
      albumTitle.textContent = album.name;

      // Image de l'album
      const albumImage = document.createElement('img');
      albumImage.src = album.images[0].url;
      albumImage.alt = album.name;

      // Lien vers l'album sur Spotify
      const albumLink = document.createElement('a');
      albumLink.href = album.external_urls.spotify;
      albumLink.target = '_blank';
      albumLink.textContent = 'Écouter sur Spotify';

      // Ajouter l'image, le titre, et le lien dans l'élément de l'album
      albumElement.appendChild(albumImage);
      albumElement.appendChild(albumTitle);
      albumElement.appendChild(albumLink);

      // Ajouter l'élément de l'album dans le conteneur principal
      albumsContainer.appendChild(albumElement);
    });
  })
  .catch(error => console.error('Erreur lors de la récupération des albums :', error));
</script>

</body>
</html>

            <h1 class="site-logo">
                <img class='logo' src="logo Episodicd.webp" alt="Episodicd">
                <a href="/Episodicd" class="logo replace">Episodicd</a>
            </h1>
        </section>
    </header>
    <div id="content" class="site-body">
        <div class="content-wrap">
            <section id="favourites" class="section">
                <h2 class="section-heading">FAVORITE ALBUMS</h2>
                <ul class="poster-list-horizontal">
                    <?php if ($resultAlbums->num_rows > 0): ?>
                        <?php while ($rowAlbums = $resultAlbums->fetch_assoc()): ?>
                            <li>
                                <a href="/film/<?= htmlspecialchars($rowAlbums['name'])?>/" data-original-title="<?= htmlspecialchars($rowAlbums['name'])?>">
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
                            <li>
                                <a href="/film/<?= htmlspecialchars($rowArtist['name'])?>/" data-original-title="<?= htmlspecialchars($rowArtist['name'])?>">
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