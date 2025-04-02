<?php
error_reporting(E_ALL ^ E_WARNING);
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
    $sql = "SELECT * FROM membre WHERE mailcompte = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":email", $email, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && password_verify($motdepasse, $result['hashmdpcompte'])) {
        // Si la connexion est réussie, définir la session
        $_SESSION['membre'] = $result['idcompte']; // on utilise un autre champ pertinent
        $_SESSION['idmembre'] = $result['idmembre'];
        header('Location: index.php'); // Redirection vers la page d'accueil ou une autre page
        exit();
    } else {
        // Gérer l'erreur de connexion
        $erreur = "L'adresse email ou le mot de passe est incorrect.";
    }
}
?>

<script>
    // Pop up de succès stylisé
    function alerte(message) {
        var alert = document.createElement('div');
        alert.style.position = 'fixed';
        alert.style.top = '50%';
        alert.style.left = '50%';
        alert.style.transform = 'translate(-50%, -50%)';
        alert.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
        alert.style.padding = '20px';
        alert.style.borderRadius = '10px';
        alert.style.boxShadow = '0 0 10px rgba(0, 0, 0, 0.5)';
        alert.style.zIndex = '1000';
        alert.innerHTML = message;
        document.body.appendChild(alert);
        setTimeout(function() {
            alert.remove();
        }, 5000);

        alert.addEventListener('click', function() {
            alert.remove();
        });
    }

</script>

<!DOCTYPE html>
<html lang="fr">
    
    <head>
        <meta charset="UTF-8">
        <title>Connexion membre</title>
        <link rel="stylesheet" href="style.css">
    </head>


    <body class="cp_mobile" style="overflow:hidden;">
    <a class="fleche" href="index.php">&#8617;</a>
        <?php
            if (isset($_GET['success'])) {
                // Pop up de succès
                echo '<script>alerte("Votre compte a bien été créé. Vous pouvez maintenant vous connecter.")</script>';
            }
        ?>

        <main class="cp_mobile_test">
        <!-- Logo -->
        <img src="img/logos/fond_remove_big.png" alt="Logo" style="width: 230px; height: auto;">
        <h1 class="cp_mobile">Membre</h1>

        <form action="connexion_membre.php" method="POST" class="cp_mobile">

            

            <!-- Section pour permettre l'alignement du texte -->
            <section class="cp_mobile">
                <label for="email_cp_mob">E-mail:</label><br>
                <input type="email" id="email_cp_mob" name="email_cp_mob" placeholder="jeanDuchamp@exemple.com" required class="cp_mobile"><br>

                <p id="erreur_email" class="cp_mobile_erreur"></p>

                <script>
                    const validateEmail = (email) => {
                        return String(email)
                            .toLowerCase()
                            .match(
                            /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                        );
                    };

                    document.getElementById('email_cp_mob').addEventListener('input', function() {
                        document.getElementById('erreur_email').innerHTML = '';
                        if (!validateEmail(this.value)) {
                            console.log('Adresse email invalide');
                            document.getElementById('erreur_email').innerHTML = 'Adresse email invalide';
                        }
                        else {
                            document.getElementById('erreur_email').innerHTML = '';
                        }
                    });
                </script>
                    
                <label for="mdp_cp_mob">Mot de passe:</label><br>
                <input type="password" id="mdp_cp_mob" name="mdp_cp_mob" placeholder="***************" required class="cp_mobile">
                <br>
                <div>
                    <a style="display:flex;justify-content:center;" href="#" class="cp_mobile">Mot de passe oublié ?</a><br>
                </div>
            </section>
            
            <!-- Affichage des erreurs en rouge pastel -->
            <?php if (isset($erreur)) : ?>
                <p class="cp_mobile_erreur"><?php echo $erreur; ?></p>
            <?php endif; ?>

            <!-- Bouton de validation -->
            <input type="submit" value="Se connecter" class="cp_mobile_btn">
            
            <a class="lien-creer" href="creer_compte_membre.php">Créer un compte membre</a>
        </form>
        <div class="right-links">            
            <a class="offer-btn orange" href="connexion_pro.php">Plateforme professionnelle</a>
        </div>
        </main> 
    </body>
</html>
<?php
ob_end_flush();
?>