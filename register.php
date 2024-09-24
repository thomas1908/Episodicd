<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <script>
        function register() {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const email = document.getElementById('email').value;

            fetch('register_process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `username=${username}&password=${password}&email=${email}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Inscription réussie !');
                    window.location.href = 'login.php';
                } else {
                    alert(data.message); // Affiche le message d'erreur renvoyé par le PHP
                }
            })
            .catch(error => console.error('Erreur:', error));
        }
    </script>
</head>
<body>
    <h2>Inscription</h2>
    <form onsubmit="event.preventDefault(); register();">
        <label for="username">Nom d'utilisateur:</label>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Mot de passe:</label>
        <input type="password" id="password" name="password" required><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>
        <button type="submit">S'inscrire</button>
    </form>
</body>
</html>
