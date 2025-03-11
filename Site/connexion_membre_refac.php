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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        .container img {
            display: block;
            margin: 0 auto 20px;
            width: 150px;
        }

        .container h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .container label {
            display: block;
            margin-bottom: 5px;
        }

        .container input[type="email"],
        .container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .container .error {
            color: red;
            margin-bottom: 10px;
        }

        .container .forgot-password {
            text-align: center;
            margin-bottom: 20px;
        }

        .container .forgot-password a {
            color: #007BFF;
            text-decoration: none;
        }

        .container .forgot-password a:hover {
            text-decoration: underline;
        }

        .container input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }

        .container input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .container .create-account,
        .container .professional-platform {
            text-align: center;
            margin-top: 20px;
        }

        .container .create-account a,
        .container .professional-platform a {
            color: #007BFF;
            text-decoration: none;
        }

        .container .create-account a:hover,
        .container .professional-platform a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            body {
                height: auto;
                padding: 20px;
            }

            .container {
                padding: 20px;
                border-radius: 0;
                box-shadow: none;
                max-width: none;
                width: 100%;
                height: 100vh;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .container img {
                width: 100px;
            }

            .container h1 {
                font-size: 24px;
            }

            .container input[type="email"],
            .container input[type="password"],
            .container input[type="submit"] {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="img/logos/fond_remove_big.png" alt="Logo">
        <h1>Connexion Membre</h1>
        <?php if (isset($erreur)) : ?>
            <p class="error"><?php echo $erreur; ?></p>
        <?php endif; ?>
        <form action="connexion_membre.php" method="POST">
            <label for="email_cp_mob">E-mail:</label>
            <input type="email" id="email_cp_mob" name="email_cp_mob" placeholder="jeanDuchamp@exemple.com" required>
            <p id="erreur_email" class="error"></p>
            <label for="mdp_cp_mob">Mot de passe:</label>
            <input type="password" id="mdp_cp_mob" name="mdp_cp_mob" placeholder="***************" required>
            <div class="forgot-password">
                <a href="#">Mot de passe oublié ?</a>
            </div>
            <input type="submit" value="Se connecter">
        </form>
        <div class="create-account">
            <a href="creer_compte_membre.php">Créer un compte membre</a>
        </div>
        <div class="professional-platform">
            <a href="connexion_pro.php">Plateforme professionnelle</a>
        </div>
    </div>
</body>
</html>
<?php
ob_end_flush();
?>