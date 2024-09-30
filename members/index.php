<?php
include('../connexion.php');
include('../header.php');
include('../functions.php');

$sqlMembers = "SELECT id, username, profile_picture FROM users";
$resultMembers = $conn->query($sqlMembers);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members</title>
    <link rel="stylesheet" type="text/css" href="../style.css">
    <link rel="icon" type="image/webp" href="../../logo_Episodicd.webp" />
</head>
<body>
    <div class="container">
        <div id="content" class="site-body">
            <div class="content-wrap">
                <section class="section">
                    <h2 class="section-heading">MEMBERS</h2>
                    <ul class="member-poster-list-horizontal">
                        <?php 
                        if ($resultMembers->num_rows > 0): 
                            while ($rowMembers = $resultMembers->fetch_assoc()): 
                                ?>
                                <li class="member-list-horizontal">
                                    <a href="/user/<?= htmlspecialchars($rowMembers['username']) ?>" data-original-title="<?= htmlspecialchars($rowMembers['username']) ?>">
                                        <img src="../<?= htmlspecialchars($rowMembers['profile_picture'], ENT_QUOTES, 'UTF-8') ?>" alt="Profile picture of <?= htmlspecialchars($rowMembers['username'], ENT_QUOTES, 'UTF-8') ?>">
                                    </a>
                                    <p class="member-name"><?= htmlspecialchars($rowMembers['username'], ENT_QUOTES, 'UTF-8') ?></p>
                                </li>
                            <?php endwhile; 
                        else: ?>
                            <li class="zero-result">Aucun membre disponible.</li>
                        <?php endif; ?>
                    </ul>
                </section>
            </div>
        </div>
        </div>
<?php include('../footer.php'); ?>
</body>
</html>
