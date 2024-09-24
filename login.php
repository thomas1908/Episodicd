<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <script>
        function login() {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            fetch('login_process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    localStorage.setItem('session_id', data.session_id);
                    localStorage.setItem('user_id', data.user_id);
                    window.location.href = 'index.php';
                }
            })
            .catch(error => console.error('Erreur:', error));
        }
    </script>
</head>
<body>
    <h2>Connexion</h2>
    <form onsubmit="event.preventDefault(); login();">
        <label for="username">Nom d'utilisateur:</label>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Mot de passe:</label>
        <input type="password" id="password" name="password" required><br>
        <button type="submit">Se connecter</button>
    </form>
</body>
</html>
