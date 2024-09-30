<?php 
include 'connexion.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="icon" type="image/webp" href="logo_Episodicd.webp">
    <link rel="preload" href="style.css" as="style">
    <noscript><link rel="stylesheet" href="style.css"></noscript>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const contentDiv = document.getElementById('content');
            const loadingDiv = document.getElementById('loading');
            
            loadingDiv.style.display = 'block';
            fetch('content.php')
                .then(response => response.text())
                .then(data => {
                    contentDiv.innerHTML = data;
                    contentDiv.style.display = 'block';
                    loadingDiv.style.display = 'none';
                })
                .catch(error => {
                    console.error('Erreur lors du chargement du contenu:', error);
                    loadingDiv.textContent = 'Erreur de chargement, veuillez réessayer.';
                });
        });
    </script>
</head>
<body>
<?php include 'header.php'; ?>
<div id="loading" class="loading-animation">
    <p class="loading-text">Calculating<span class="dot"></span><span class="dot"></span><span class="dot"></span></p>
</div>
<div class="container">
    <div id="content" class="site-body" style="display:none;">
</div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>