<?php 
include 'connexion.php';
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$user = null;

if ($user_id) {
    $stmt = $conn->prepare("SELECT username, profile_picture FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
    }
}
?>

<header class="hidden">
    <section class="header-button">
        <h1 class="site-logo">
            <img class='logo' src="../logo_Episodicd.webp" alt="Episodicd">
            <a href="../" class="logo replace">Episodicd</a>
        </h1>
        <div class="login-container" id="auth-buttons">
            <?php if ($user): ?>
                <div class="user-profile">
                    <a href="../profile.php" class="profile-link">
                        <img src="../<?= htmlspecialchars($user['profile_picture'], ENT_QUOTES, 'UTF-8') ?>" alt="Photo de profil">
                        <?= htmlspecialchars($user['username']) ?>
                    </a>
                </div>
            <?php else: ?>
                <a href="../login.php" class="login-button">Connexion</a>
                <a href="../register.php" class="register-button">Inscription</a>
            <?php endif; ?>
        </div>
        <div class="music-container">
            <a href="" class="music-button">Music</a>
        </div>
        <div class="member-container">
            <a href="../members/" class="member-button">Members</a>
        </div>
        <div class="search-bar-container">
            <form id="search-form" action="../search.php" method="get">
                <button id="search-btn" class="search-button" type="button" aria-label="Rechercher">
                    <svg id="search-icon" xmlns="http://www.w3.org/2000/svg" version="1.0" width="24" height="24" viewBox="0 0 1280.000000 1230.000000" preserveAspectRatio="xMidYMid meet">
                    <g transform="translate(0.000000,1230.000000) scale(0.100000,-0.100000)" fill="#9ab" stroke="none">
                        <path d="M4970 12294 c-25 -2 -106 -8 -180 -14 -1181 -95 -2334 -616 -3184 -1440 -317 -307 -568 -614 -792 -967 -449 -708 -709 -1478 -796 -2358 -17 -173 -17 -720 0 -900 69 -738 273 -1425 604 -2040 500 -928 1256 -1675 2189 -2164 919 -481 1996 -677 3049 -555 868 100 1728 430 2427 931 56 40 104 73 108 73 3 0 641 -588 1418 -1307 776 -718 1437 -1326 1467 -1350 72 -58 235 -138 335 -165 116 -31 355 -31 470 0 600 165 884 837 581 1375 -78 138 76 -8 -1913 1831 l-1080 998 84 142 c167 280 340 657 449 978 403 1187 368 2487 -98 3656 -388 976 -1074 1820 -1949 2402 -726 482 -1517 764 -2399 855 -144 15 -682 28 -790 19z m614 -1879 c656 -70 1247 -314 1771 -731 141 -112 406 -377 519 -519 594 -744 844 -1668 705 -2608 -183 -1239 -1087 -2308 -2284 -2700 -389 -128 -707 -174 -1138 -164 -268 6 -406 22 -632 72 -950 213 -1757 815 -2233 1666 -373 666 -506 1454 -372 2209 160 909 697 1723 1475 2236 455 300 954 479 1498 538 169 19 520 19 691 1z"/>
                    </g>
                    </svg>
                    <svg id="close-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" style="display: none;">
                    <path d="M19 5L5 19M5 5l14 14" stroke="#9ab" stroke-width="3" />
                    </svg>
                </button>
            <input type="text" id="search-input" class="search-input" placeholder="Rechercher un album ou un artiste..." aria-label="Rechercher un album ou un artiste...">
            </form>
        </div>
        <script>
            document.getElementById('search-form').addEventListener('submit', function(event) {
            event.preventDefault();
            var searchInput = document.getElementById('search-input').value;
            if (searchInput.trim() !== '') {
                window.location.href = this.action.replace('search.php', 'search/') + '?query=' + encodeURIComponent(searchInput);
            }
            });
        </script>
    </section>
    <script src="../script.js"></script>
</header>

