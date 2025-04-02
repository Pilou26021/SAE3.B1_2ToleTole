<?php
error_reporting(E_ALL ^ E_WARNING);
ob_start();
session_start(); // Démarre la session

include '../SQL/connection_local.php';
require_once '../COMPOSE/vendor/autoload.php';
use OTPHP\TOTP;

if (isset($_SESSION['membre'])) {
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
    $sql = "SELECT * FROM membre WHERE mailcompte = :email";
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
<<<<<<< HEAD
        // Si la connexion est réussie, définir la session
        $_SESSION['membre'] = $result['idcompte']; // on utilise un autre champ pertinent
        $_SESSION['idmembre'] = $result['idmembre'];
        header('Location: index.php'); // Redirection vers la page d'accueil ou une autre page
        exit();
=======
        if ($auth_parametre === false || $auth_parametre === 'false') {
            // Si la connexion est réussie, définir la session
            $_SESSION['membre'] = $result['idcompte']; // on utilise un autre champ pertinent
            updateLastConnection($conn, $result['idcompte']); // Mettre à jour la date de dernière connexion
            header('Location: index.php'); // Redirection vers la page d'accueil ou une autre page
            exit();
        } else {
            if (!empty($otp)) { // Vérifie si l'OTP est fourni
                // Vérification de l'OTP
                $secret = getsecret($conn, $result['idcompte']);
                $totp = TOTP::create($secret);
                if ($totp->verify($otp, leeway: 15)) {
                    // OTP valide, connexion réussie
                    $_SESSION['membre'] = $result['idcompte'];
                    updateLastConnection($conn, $result['idcompte']); // Mettre à jour la date de dernière connexion
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
>>>>>>> ad46d8dd60f6b780b273f0eded6d8e87caeb54ac
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
        <h1 class="cp_mobile">Membre</h1>
        <form action="connexion_membre.php" method="POST" class="cp_mobile">
            <input type="email" name="email_cp_mob" placeholder="Email" required>
            <input type="password" name="mdp_cp_mob" placeholder="Mot de passe" required>
            <p><a class="lien-creer" href="#">Mot de passe oublié ?</a></p>
            <input type="text" name="otp_cp_mob" placeholder="OTP (si activé)">
            <?php if (isset($erreur)) { ?>
                <div class="erreur"><?php echo $erreur; ?></div>
            <?php } ?>
            <input type="submit" value="Se connecter">
        </form>
        <a class="cp_mobile_btn" href="creer_compte_membre.php">Créer un compte</a>
        <a class="cp_mobile_btn orange" href="connexion_pro.php">Plateforme professionnelle</a>
    </section>
</body>
</html>

<script>

    document.addEventListener("DOMContentLoaded", function () {
        setTimeout(() => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get("cpteSup") === "true") {
                alert("Votre compte à bien été supprimé");
            }
        }, 1000);
    });

</script>

<?php
ob_end_flush();
?>