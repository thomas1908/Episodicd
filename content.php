<?php 
include 'connexion.php';
include 'functions.php';
$sqlAlbums = "SELECT id, name, artist_id, cover FROM albums";
$resultAlbums = $conn->query($sqlAlbums);

?>

<div class="content-wrap">
    <section class="section">
        <h2 class="section-heading">ALBUMS</h2>
        <ul class="poster-list-horizontal">
            <?php 
            if ($resultAlbums->num_rows > 0): 
                while ($rowAlbums = $resultAlbums->fetch_assoc()): 
                    $slugAlbum = slugify($rowAlbums['name']); // Générer le slug
                    ?>
                    <li class="image-poster-list-horizontal">
                        <a href="/album/<?= $slugAlbum ?>" data-original-title="<?= htmlspecialchars($rowAlbums['name']) ?>">
                            <img src="<?= htmlspecialchars($rowAlbums['cover'], ENT_QUOTES, 'UTF-8') ?>" alt="Cover of <?= htmlspecialchars($rowAlbums['name'], ENT_QUOTES, 'UTF-8') ?>">
                        </a>
                    </li>
                <?php endwhile; 
            else: ?>
                <li class="zero-result">Aucun album disponible.</li>
            <?php endif; ?>
        </ul>
    </section>
    <section class="section">
        <h2 class="section-heading">ARTISTS</h2>
        <ul class="poster-list-horizontal">
            <?php
            $sqlArtist = "SELECT id_artist, name, photo FROM artist";
            $resultArtist = $conn->query($sqlArtist);
            if ($resultArtist->num_rows > 0): 
                while ($rowArtist = $resultArtist->fetch_assoc()):
                    $imageData = base64_encode($rowArtist['photo']);
                    $src = 'data:image/jpeg;base64,' . $imageData;
                    $slugArtist = slugify($rowArtist['name']);
                    ?>
                    <li class="image-poster-list-horizontal">
                        <a href="/artist/<?= $slugArtist ?>" data-original-title="<?= htmlspecialchars($rowArtist['name']) ?>">
                            <img src="<?= $src ?>" alt="Cover of <?= htmlspecialchars($rowArtist['name'], ENT_QUOTES, 'UTF-8') ?>">
                        </a>
                    </li>
                <?php endwhile; 
            else: ?>
                <li class="zero-result">Aucun artiste disponible.</li>
            <?php endif; ?>
        </ul>
    </section>
</div>
