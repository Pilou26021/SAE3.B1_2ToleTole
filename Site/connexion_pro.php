<?php
ob_start();
session_start(); // Démarre la session

include '../SQL/connection_local.php';
require_once '../COMPOSE/vendor/autoload.php';
use OTPHP\TOTP;

if (isset($_SESSION['professionnel'])) {
    // Si l'utilisateur est déjà connecté, le rediriger vers la page d'accueil
    header('Location: index.php');
    exit();
}

function getsecret($conn, $userId) {
    $sql = "SELECT auth_secret FROM _compte WHERE idcompte = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn();
}
function updateLastConnection($conn, $userId) {
    $sql = "UPDATE _compte   SET datederniereconnexioncompte = NOW() WHERE idcompte = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    $email = $_POST['email_cp_mob'];
    $motdepasse = $_POST['mdp_cp_mob'];
    $otp = $_POST['otp_cp_mob'] ?? null; // OTP facultatif

    // Vérification de l'existence de l'utilisateur
    $sql = "SELECT * FROM professionnel WHERE mailcompte = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":email", $email, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérification du paramètre d'authentification
    $sql = "SELECT auth_parametre FROM _compte WHERE idcompte = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":id", $result['idcompte'], PDO::PARAM_INT);
    $stmt->execute();
    $auth_parametre = $stmt->fetchColumn();

    if ($result && password_verify($motdepasse, $result['hashmdpcompte'])) {
        if ($auth_parametre === false || $auth_parametre === 'false') {
            // Si la connexion est réussie, définir la session
            $_SESSION['professionnel'] = $result['idcompte']; // on utilisez un autre champ pertinent
            $_SESSION['idpro'] = $result['idpro'];
            updateLastConnection($conn, $result['idcompte']); // Mettre à jour la date de dernière connexion

            //On regarde si c'est un pro public
            $sql = "SELECT * FROM _professionnelpublic WHERE idpro = :idpro";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(":idpro", $_SESSION['idpro'], PDO::PARAM_INT);
            $stmt->execute();
            $ispropublic = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($ispropublic){
                $_SESSION['propublic'] = true;
            } else {
                $_SESSION['propublic'] = false;
            }

            header('Location: index.php'); // Redirection vers la page d'accueil ou une autre page
            exit();
        } else {
            if (!empty($otp)) { // Vérifie si l'OTP est fourni
                // Vérification de l'OTP
                $secret = getsecret($conn, $result['idcompte']);
                $totp = TOTP::create($secret);
                if ($totp->verify($otp, leeway: 15)) {
                    // OTP valide, connexion réussie
                    $_SESSION['professionnel'] = $result['idcompte'];
                    $_SESSION['idpro'] = $result['idpro'];
                    updateLastConnection($conn, $result['idcompte']); // Mettre à jour la date de dernière connexion

                    //On regarde si c'est un pro public
                    $sql = "SELECT * FROM _professionnelpublic WHERE idpro = :idpro";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindValue(":idpro", $_SESSION['idpro'], PDO::PARAM_INT);
                    $stmt->execute();
                    $ispropublic = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($ispropublic){
                        $_SESSION['propublic'] = true;
                    } else {
                        $_SESSION['propublic'] = false;
                    }

                    header('Location: index.php');
                    exit();
                } else {
                    // OTP invalide
                    $erreur = "L'OTP est incorrect.";
                }
            } else {
                // OTP manquant
                $erreur = "Veuillez entrer l'OTP.";
            }
        }
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
    <title>Connexion professionnelle</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<a class="fleche" href="index.php">&#8617;</a>
    <?php
        if (isset($_GET['success'])) {
            // Pop up de succès
            echo '<script>alerte("Votre compte a bien été créé. Vous pouvez maintenant vous connecter.")</script>';
        }
    ?>

    <!-- Logo -->
    <section class="cp_logo_nom">
        <img src="img/logos/fond_remove_big.png" alt="Logo" style="width: 140px; height: auto;">
    </section>

    <section class="cp_form">
        <h1 class="cp_mobile">Professionnel</h1>
        <form action="connexion_pro.php" method="POST" class="cp_mobile">
            <input type="email" name="email_cp_mob" placeholder="Email" required>
            <input type="password" name="mdp_cp_mob" placeholder="Mot de passe" required>
            <p><a class="lien-creer" href="#">Mot de passe oublié ?</a></p>
            <input type="text" name="otp_cp_mob" placeholder="OTP (si activé)">
            <?php if (isset($erreur)) { ?>
                <div class="erreur"><?php echo $erreur; ?></div>
            <?php } ?>
            <input type="submit" value="Se connecter">
        </form>
        <a class="cp_mobile_btn orange" href="creer_compte_pro.php">Créer un compte</a>
        <a class="cp_mobile_btn" href="connexion_membre.php">Plateforme membre</a>
    </section>
</body>
</html>
<?php
ob_end_flush();
?>