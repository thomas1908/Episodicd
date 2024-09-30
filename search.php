<?php
include 'connexion.php';
include 'header.php';

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$filters = isset($_GET['filter']) ? $_GET['filter'] : ['all'];

if (!empty($query)) {
    $sqlSearchParts = [];

    if (in_array('all', $filters)) {
        $sqlSearchParts[] = "(SELECT id, name, slug, cover, 'album' AS type, release_date AS date_info 
                              FROM albums 
                              WHERE (MATCH(name) AGAINST (?) OR name LIKE ?) AND type IN ('album', 'single', 'ep'))";
        $sqlSearchParts[] = "(SELECT id_artist AS id, name, slug, photo AS cover, 'artist' AS type, birth_date AS date_info 
                              FROM artist 
                              WHERE MATCH(name) AGAINST (?) OR name LIKE ?)";
    } else {
        if (in_array('album', $filters)) {
            $sqlSearchParts[] = "(SELECT id, name, slug, cover, 'album' AS type, release_date AS date_info 
                                  FROM albums 
                                  WHERE (MATCH(name) AGAINST (?) OR name LIKE ?) AND type = 'album')";
        }
        
        if (in_array('single', $filters)) {
            $sqlSearchParts[] = "(SELECT id, name, slug, cover, 'single' AS type, release_date AS date_info 
                                  FROM albums 
                                  WHERE (MATCH(name) AGAINST (?) OR name LIKE ?) AND type = 'single')";
        }

        if (in_array('ep', $filters)) {
            $sqlSearchParts[] = "(SELECT id, name, slug, cover, 'ep' AS type, release_date AS date_info 
                                  FROM albums 
                                  WHERE (MATCH(name) AGAINST (?) OR name LIKE ?) AND type = 'ep')";
        }
        
        if (in_array('artist', $filters)) {
            $sqlSearchParts[] = "(SELECT id_artist AS id, name, slug, photo AS cover, 'artist' AS type, birth_date AS date_info 
                                  FROM artist 
                                  WHERE MATCH(name) AGAINST (?) OR name LIKE ?)";
        }
    }
    
    if (empty($sqlSearchParts)) {
        $sqlSearchParts[] = "(SELECT id, name, slug, cover, 'album' AS type FROM albums WHERE 1=0)";
    }

    $sqlSearch = implode(' UNION ', $sqlSearchParts) . " LIMIT ? OFFSET ?";
    $stmtSearch = $conn->prepare($sqlSearch);
    
    $limit = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    $searchTerm = "%$query%";
    $params = [];
    $types = '';
    
    if (in_array('all', $filters)) {
        $params[] = $query;
        $params[] = $searchTerm;
        $params[] = $query;
        $params[] = $searchTerm;
        $types .= 'ssss';
    } else {
        foreach ($filters as $filter) {
            if ($filter === 'album') {
                $params[] = $query;
                $params[] = $searchTerm;
                $types .= 'ss';
            }
            if ($filter === 'single') {
                $params[] = $query;
                $params[] = $searchTerm;
                $types .= 'ss';
            }
            if ($filter === 'ep') {
                $params[] = $query;
                $params[] = $searchTerm;
                $types .= 'ss';
            }
            if ($filter === 'artist') {
                $params[] = $query;
                $params[] = $searchTerm;
                $types .= 'ss';
            }
        }
    }
    
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
    
    if (!empty($types)) {
        $stmtSearch->bind_param($types, ...$params);
        if (!$stmtSearch->execute()) {
            error_log("SQL Error: " . $stmtSearch->error, 0);
            header("Location: ../error.php");
            exit;
        }
        $resultSearch = $stmtSearch->get_result();
    } else {
        header("Location: ../index.php");
        exit;
    }    
} else {
    header("Location: ../index.php");
    exit;
}
?>

<html>
<head>
    <title>Résultats de recherche</title>
    <link rel="stylesheet" type="text/css" href="../style.css">
    <link rel="icon" type="image/webp" href="../logo_Episodicd.webp" />
</head>
<body>
    <div class="container">
        <div id="content" class="site-body">
            <div class="content-wrap-search">
                <section id="search-results" class="section">
                    <h2 class="section-heading">Résultats de recherche pour "<?= htmlspecialchars($query ?? '', ENT_QUOTES, 'UTF-8') ?>"</h2>
                    <ul class="poster-list-horizontal" id="result-list">
                        <?php if ($resultSearch->num_rows > 0): ?>
                            <?php while ($rowSearch = $resultSearch->fetch_assoc()):
                                if ($rowSearch['type'] === 'artist') {
                                    $imageData = base64_encode($rowSearch['cover']);
                                    $src = 'data:image/jpeg;base64,' . $imageData;
                                }
                                ?>
                                <li class="image-poster-list-horizontal-search">
                                    <a href="<?= $rowSearch['type'] != 'artist' ? '../album/' . htmlspecialchars($rowSearch['slug'] ?? '', ENT_QUOTES, 'UTF-8') : '../artist/' . htmlspecialchars($rowSearch['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>" data-original-title="<?= htmlspecialchars($rowSearch['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                    <img src="<?= $rowSearch['type'] != 'artist' ? htmlspecialchars($rowSearch['cover'], ENT_QUOTES, 'UTF-8') : $src ?>" alt="Cover of <?= htmlspecialchars($rowSearch['name'], ENT_QUOTES, 'UTF-8') ?>">
                                    </a>
                                    <div class="film-detail-content">
                                        <h2 class="headline-2">
                                            <span class="film-title-wrapper">
                                                <a href="<?= $rowSearch['type'] != 'artist' ? '../album/' . htmlspecialchars($rowSearch['slug']) : '../artist/' . htmlspecialchars($rowSearch['slug']) ?>" data-original-title="<?= htmlspecialchars($rowSearch['name']) ?>">
                                                    <?= htmlspecialchars($rowSearch['name']) ?>
                                                </a>
                                                <small class="metadata">
                                                    <a>
                                                        <?php
                                                        if ($rowSearch['type'] != 'artist') {
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
                                <a href="../search/?query=<?= htmlspecialchars($query) ?>&filter[]=all">All</a>
                            </li>
                            <li class="<?= in_array('album', $filters) ? 'selected' : '' ?>">
                                <a href="../search/?query=<?= htmlspecialchars($query) ?>&filter[]=album">Albums</a>
                            </li>
                            <li class="<?= in_array('single', $filters) ? 'selected' : '' ?>">
                                <a href="../search/?query=<?= htmlspecialchars($query) ?>&filter[]=single">Singles</a>
                            </li>
                            <li class="<?= in_array('ep', $filters) ? 'selected' : '' ?>">
                                <a href="../search/?query=<?= htmlspecialchars($query) ?>&filter[]=ep">EPs</a>
                            </li>
                            <li class="<?= in_array('artist', $filters) ? 'selected' : '' ?>">
                                <a href="../search/?query=<?= htmlspecialchars($query) ?>&filter[]=artist">Artists</a>
                            </li>
                        </ul>
                    </section>
                </aside>
            </div>
        </div>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="../search/?query=<?= htmlspecialchars($query) ?>&page=<?= $page - 1 ?>&filter[]=<?= implode('&filter[]=', $filters) ?>">Précédent</a>
            <?php endif; ?>
            <span>Page <?= $page ?></span>
            <?php if ($resultSearch->num_rows == $limit): // Si des résultats existent pour une page suivante ?>
                <a href="../search/?query=<?= htmlspecialchars($query) ?>&page=<?= $page + 1 ?>&filter[]=<?= implode('&filter[]=', $filters) ?>">Suivant</a>
            <?php endif; ?>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
