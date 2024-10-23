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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="deconnexion-message">
        Vous avez été déconnecté. À bientôt !
    </div>
    <?php
    // redirection vers la page d'accueil après 3 secondes
    header("refresh:3;url=index.php");
    ?>
</body>
</html>
