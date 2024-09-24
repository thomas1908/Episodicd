<?php
// Détruire la session de l'utilisateur
session_start();
session_destroy();
header('Location: index.php');
exit;