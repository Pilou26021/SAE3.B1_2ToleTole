<?php
ob_start();
session_start(); // Démarre la session

include '../SQL/connection_local.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    $email = $_POST['email_cp_mob'];
    $motdepasse = $_POST['mdp_cp_mob'];

    // Vérification de l'existence de l'utilisateur
    $sql = "SELECT * FROM professionnelMdp WHERE mailcompte = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(1, $email, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && password_verify($motdepasse, $result['hashmdpcompte'])) {
        // Si la connexion est réussie, définir la session
        $_SESSION['professionnel'] = $result['idpro']; // Ou utilisez un autre champ pertinent
        header('Location: index.php'); // Redirection vers la page d'accueil ou une autre page
        exit();
    } else {
        // Gérer l'erreur de connexion
        $erreur = "L'adresse email ou le mot de passe est incorrect.";
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
    
    <head>
        <meta charset="UTF-8">
        <title>Connexion professionnel</title>
        <link rel="stylesheet" href="style.css">
    </head>


    <body class="cp_mobile">
        <div style="display: flex; justify-content: left; width: 100%;">
            <a style="text-decoration: none; font-size: 30px; color: #040316;" onclick="history.back();">&#8617;</a>
        </div>

        <!-- Logo -->
        <img src="img/fond_remove_big.png" alt="Logo" style="width: 230px; height: auto;">
        <h1 class="cp_mobile">Professionnel</h1>

        <form action="connexion_pro.php" method="POST" class="cp_mobile">

            <!-- Section pour permettre l'alignement du texte -->
            <section class="cp_mobile">
                <label for="email_cp_mob">E-mail:</label><br>
                <input type="email" id="email_cp_mob" name="email_cp_mob" placeholder="jeanDuchamp@exemple.com" required class="cp_mobile"><br><br><br>
                
                <label for="mdp_cp_mob">Mot de passe:</label><br>
                <input type="password" id="mdp_cp_mob" name="mdp_cp_mob" placeholder="***************" required class="cp_mobile"><br>
            </section>

            <!-- Rester connecté ? -->
            <label><input type="checkbox" name="rester_co" class="cp_mobile_chkbox"> Rester connecté ?</label><br><br><br>
            
            <!-- Affichage des erreurs en rouge pastel -->
            <?php if (isset($erreur)) : ?>
                <p class="cp_mobile_erreur"><?php echo $erreur; ?></p>
            <?php endif; ?>

            <!-- Bouton de validation -->
            <input type="submit" value="Se connecter" class="cp_mobile_btn">

        </form>

        <!-- Liens vers les autres pages -->
        <a href="#" class="cp_mobile">Mot de passe oublié ?</a><br>
        <a href="#" class="cp_mobile">Se connecter en tant que membre</a><br>
        <a href="#" class="cp_mobile">Créer un compte professionnel</a><br>

    </body>
</html>
