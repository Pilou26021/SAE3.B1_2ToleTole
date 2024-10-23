<?php
// Page de déconnexion
session_start();
session_destroy();

// On affiche un message comme quoi on a bien été déconnecté
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Déconnexion</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        .deconnexion-message {
            font-family: 'Montserrat', sans-serif;
            font-size: 40px;
            text-align: center;
            margin: 0;  /* Supprime les marges par défaut des <p> */
            padding: 5px;  /* Ajoute un peu d'espace interne */
        }

        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
    </style>
</head>
<body>
    <p class="deconnexion-message">Vous avez été déconnecté. À bientôt !</p>
    <p class="deconnexion-message">Si vous n'êtes pas redirigé dans 3 secondes, <a href="index.php">cliquez ici.</a></p>
    <?php
    // redirection vers la page d'accueil après 3 secondes
    header("refresh:3;url=index.php");
    ?>
</body>
</html>
