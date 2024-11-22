<?php
ob_start();
session_start(); // Démarre la session

include '../SQL/connection_local.php';

if (isset($_SESSION['membre'])) {
    // Si l'utilisateur est déjà connecté, le rediriger vers la page d'accueil
    header('Location: index.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    $email = $_POST['email_cp_mob'];
    $motdepasse = $_POST['mdp_cp_mob'];

    // Vérification de l'existence de l'utilisateur
    $sql = "SELECT * FROM membremdp WHERE mailcompte = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(1, $email, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && password_verify($motdepasse, $result['hashmdpcompte'])) {
        // Si la connexion est réussie, définir la session
        $_SESSION['membre'] = $result['idmembre']; // on utilise un autre champ pertinent
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
        <title>Connexion membre</title>
        <link rel="stylesheet" href="style.css">
    </head>


    <body class="cp_mobile">
        <div style=" position:sticky; top:20px; width: 100%;">
            <a style="text-decoration: none; font-size: 30px; color: #040316; cursor: pointer;" href="index.php">&#8617;</a>
        </div>


        <!-- Logo -->
        <img src="img/logos/fond_remove_big.png" alt="Logo" style="width: 230px; height: auto;">
        <h1 class="cp_mobile">Membre</h1>

        <form action="connexion_membre.php" method="POST" class="cp_mobile">

            <!-- Section pour permettre l'alignement du texte -->
            <section class="cp_mobile">
                <label for="email_cp_mob">E-mail:</label><br>
                <input type="email" id="email_cp_mob" name="email_cp_mob" placeholder="jeanDuchamp@exemple.com" required class="cp_mobile"><br><br><br>
                
                <label for="mdp_cp_mob">Mot de passe:</label><br>
                <input type="password" id="mdp_cp_mob" name="mdp_cp_mob" placeholder="***************" required class="cp_mobile">
                <br><br>
                <div>
                    <a style="display:flex;justify-content:center;" href="#" class="cp_mobile">Mot de passe oublié ?</a><br>
                </div>
            </section>

            <!-- Rester connecté ? -->
            <label><input type="checkbox" name="rester_co" class="cp_mobile_chkbox"> Rester connecté ?</label>
            
            <!-- Affichage des erreurs en rouge pastel -->
            <?php if (isset($erreur)) : ?>
                <p class="cp_mobile_erreur"><?php echo $erreur; ?></p>
            <?php endif; ?>

            <!-- Bouton de validation -->
            <input type="submit" value="Se connecter" class="cp_mobile_btn">
            <div style="display:flex;align-items:center;flex-direction:column;">
                <a class="offer-btn" style="text-decoration:none;" href="connexion_pro.php">Se connecter en tant que professionnel</a>
                <span>OU</span>
                <br>
                <a class="offer-btn" href="creer_compte_pro.php" class="cp_mobile">Créer un compte membre</a><br>
            </div>

        </form>

    </body>
</html>
