<?php
include 'connexion.php';

// Vérification si un terme de recherche a été soumis
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$filters = isset($_GET['filter']) ? $_GET['filter'] : ['all']; // Par défaut, tous les filtres sont activés

if (!empty($query)) {
    // Construction de la requête SQL dynamique
    $sqlSearchParts = [];
    
    if (in_array('all', $filters)) {
        $sqlSearchParts[] = "(SELECT id, name, cover, 'album' AS type, release_date AS date_info FROM albums WHERE SOUNDEX(name) = SOUNDEX(?) OR name LIKE ?)";
        $sqlSearchParts[] = "(SELECT id_artist AS id, name, photo AS cover, 'artist' AS type, birth_date AS date_info FROM artist WHERE SOUNDEX(name) = SOUNDEX(?) OR name LIKE ?)";
    } else {
        if (in_array('album', $filters)) {
            $sqlSearchParts[] = "(SELECT id, name, cover, 'album' AS type, release_date AS date_info FROM albums WHERE SOUNDEX(name) = SOUNDEX(?) OR name LIKE ?)";
        }
        
        if (in_array('artist', $filters)) {
            $sqlSearchParts[] = "(SELECT id_artist AS id, name, photo AS cover, 'artist' AS type, birth_date AS date_info FROM artist WHERE SOUNDEX(name) = SOUNDEX(?) OR name LIKE ?)";
        }
    }
    

    // Si aucun filtre n'est sélectionné, on affiche tous les résultats
    if (empty($sqlSearchParts)) {
        // Ajout d'un placeholder pour éviter une requête vide
        $sqlSearchParts[] = "(SELECT id, name, cover, 'album' AS type FROM albums WHERE 1=0)"; // Aucune correspondance
    }

    $sqlSearch = implode(' UNION ', $sqlSearchParts);
    $stmtSearch = $conn->prepare($sqlSearch);
    $searchTerm = "%$query%";
    
    $params = [];
    $types = '';
    
    // Ajout des paramètres pour lier les valeurs
    if (in_array('all', $filters)) {
        // Si "All" est sélectionné, nous devons ajouter les paramètres pour les deux types
        $params[] = $query; // Pour SOUNDEX des albums
        $params[] = $searchTerm; // Pour LIKE des albums
        $params[] = $query; // Pour SOUNDEX des artistes
        $params[] = $searchTerm; // Pour LIKE des artistes
        $types .= 'ssss'; // Ajout des types pour les deux requêtes
    } else {
        foreach ($filters as $filter) {
            if ($filter === 'album' || $filter === 'artist') {
                $params[] = $query; // Pour SOUNDEX
                $params[] = $searchTerm; // Pour LIKE
                $types .= 'ss';
            }
        }
    }

    // Vérifiez si des types ont été ajoutés avant d'appeler bind_param
    if (!empty($types)) {
        $stmtSearch->bind_param($types, ...$params);
        $stmtSearch->execute();
        $resultSearch = $stmtSearch->get_result();
    } else {
        // Aucun type n'a été ajouté, redirigez ou gérez l'erreur
        header("Location: index.php");
        exit;
    }
} else {
    // Si aucun terme de recherche n'est soumis, rediriger vers la page d'accueil ou une autre action
    header("Location: index.php");
    exit;
}
?>

<html>
<head>
    <title>Résultats de recherche</title>
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
        <div class="content-wrap-search">
            <section id="search-results" class="section">
                <h2 class="section-heading">Résultats de recherche pour "<?= htmlspecialchars($query) ?>"</h2>
                <ul class="poster-list-horizontal" id="result-list">
                    <?php if ($resultSearch->num_rows > 0): ?>
                        <?php while ($rowSearch = $resultSearch->fetch_assoc()): ?>
                            <li class="image-poster-list-horizontal-search">
                                <a href="<?= $rowSearch['type'] == 'album' ? 'album.php?id=' . htmlspecialchars($rowSearch['id']) : 'artist.php?id=' . htmlspecialchars($rowSearch['id']) ?>" data-original-title="<?= htmlspecialchars($rowSearch['name']) ?>">
                                    <img src="<?= htmlspecialchars($rowSearch['cover'], ENT_QUOTES, 'UTF-8') ?>" alt="Cover of <?= htmlspecialchars($rowSearch['name'], ENT_QUOTES, 'UTF-8') ?>">
                                </a>
                                <div class="film-detail-content">
                                    <h2 class="headline-2">
                                        <span class="film-title-wrapper">
                                            <a href="<?= $rowSearch['type'] == 'album' ? 'album.php?id=' . htmlspecialchars($rowSearch['id']) : 'artist.php?id=' . htmlspecialchars($rowSearch['id']) ?>" data-original-title="<?= htmlspecialchars($rowSearch['name']) ?>">
                                                <?= htmlspecialchars($rowSearch['name']) ?>
                                            </a>
                                            <small class="metadata">
                                                <a>
                                                    <?php
                                                    if ($rowSearch['type'] == 'album') {
                                                        echo htmlspecialchars(date('Y', strtotime($rowSearch['date_info'])));
                                                    }
                                                    ?>
                                                </a>
                                            </small>
                                        </span>
                                    </h2>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li class="zero-result">Aucun résultat trouvé.</li>
                    <?php endif; ?>
                </ul>
            </section>
            <aside class="sidebar">
                <section id="search-filter" class="section">
                    <h2 class="section-heading">Afficher les résultats pour</h2>
                    <ul>
                        <li class="<?= in_array('all', $filters) ? 'selected' : '' ?>">
                            <a href="search.php?query=<?= htmlspecialchars($query) ?>&filter[]=all">All</a>
                        </li>
                        <li class="<?= in_array('album', $filters) ? 'selected' : '' ?>">
                            <a href="search.php?query=<?= htmlspecialchars($query) ?>&filter[]=album">Albums</a>
                        </li>
                        <li class="<?= in_array('artist', $filters) ? 'selected' : '' ?>">
                            <a href="search.php?query=<?= htmlspecialchars($query) ?>&filter[]=artist">Artists</a>
                        </li>
                    </ul>
                </section>
            </aside>
        </div>
    </div>
</body>
</html>
