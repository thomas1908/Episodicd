<!DOCTYPE html>
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
  async function getAccessToken() {
    const response = await fetch('https://accounts.spotify.com/api/token', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'Authorization': 'Basic ' + btoa('0399d242d0f54a0ba4bf95ba7a310629:b6add34a9fbc45d1862730e2463739e4')
      },
      body: new URLSearchParams({
        grant_type: 'client_credentials'
      })
    });

    const data = await response.json();
    return data.access_token;
  }

  async function fetchAlbums(token) {
    const response = await fetch('https://api.spotify.com/v1/search?q=Pray+for+Paris&type=album', {
      method: 'GET',
      headers: {
        'Authorization': 'Bearer ' + token
      }
    });

    const data = await response.json();
    return data.albums.items;
  }

  async function displayAlbums() {
    try {
      const token = await getAccessToken();
      const albums = await fetchAlbums(token);
      const albumsContainer = document.getElementById('albums');

      albums.forEach(album => {
        const albumElement = document.createElement('div');
        albumElement.classList.add('album');

        const albumTitle = document.createElement('h3');
        albumTitle.textContent = album.name;

        const albumImage = document.createElement('img');
        albumImage.src = album.images[0].url;
        albumImage.alt = album.name;

        albumElement.appendChild(albumImage);
        albumElement.appendChild(albumTitle);

        albumsContainer.appendChild(albumElement);
      });
    } catch (error) {
      console.error('Erreur lors de la récupération des albums :', error);
    }
  }
  window.onload = displayAlbums;
</script>

</body>
</html>
