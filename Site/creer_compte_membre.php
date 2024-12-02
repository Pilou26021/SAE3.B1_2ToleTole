<?php
// Initialisation des variables
$step = 1;
$errors = [];

error_reporting(E_ALL ^ E_WARNING);

//start session
ob_start();
session_start();

//connecteur pour requête
include "../SQL/connection_local.php";   

// Vérification des étapes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $step = isset($_POST['step']) ? intval($_POST['step']) : 1;

    // Étape 1 : Validation des champs de l'utilisateur
    if ($step === 1) {
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $adresse = trim($_POST['adresse'] ?? '');
        $ville = trim($_POST['ville'] ?? '');
        $tel = trim($_POST['tel'] ?? '');

        // On vérifie que l'email n'existe pas déjà
        $stmt = $conn->prepare("SELECT * FROM _compte WHERE mailcompte = ?");
        $stmt->bindValue(1, $email, PDO::PARAM_STR);
        $stmt->execute();
        $email_exists = $stmt->fetch();

        // On vérifie que le numéro de téléphone n'existe pas déjà
        $stmt = $conn->prepare("SELECT * FROM _compte WHERE numtelcompte = ?");
        $stmt->bindValue(1, $tel, PDO::PARAM_STR);
        $stmt-> execute();
        $telephone_exists = $stmt->fetch();

        if (!empty($telephone_exists)) $errors['tel'] = "Le numéro de téléphone existe déjà.";
        if (!empty($email_exists)) $errors['email'] = "L'adresse e-mail existe déjà.";
        if (empty($nom)) $errors['nom'] = "Le champ 'Nom' est requis.";
        if (empty($prenom)) $errors['prenom'] = "Le champ 'Prénom' est requis.";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = "L'adresse e-mail n'est pas valide.";
        $phoneRegex = '/^(\+33|0)6\d{8}$/';
        if (!preg_match($phoneRegex, $tel)) $errors['tel'] = "Le numéro de téléphone doit contenir 10 chiffres.";

        if (empty($errors)) {
            $step = 2; // Passer à l'étape 2 si tout est valide
        }
    }
    // Etape 2 : Adresse résidentielle
    else if ($step === 2) {
        $adNumRue = trim($_POST['adNumRue'] ?? '');
        $supplementAdresse = trim($_POST['supplementAdresse'] ?? '');
        $adresse = trim($_POST['adresse'] ?? '');
        $code_postal = trim($_POST['code_postal'] ?? '');
        $ville = trim($_POST['ville'] ?? '');
        $departement = trim($_POST['departement'] ?? '');
        $pays = trim($_POST['pays'] ?? '');
        
        if (empty($adNumRue)) $errors['adNumRue'] = "Le champ 'Numéro de rue' est requis.";
        if (empty($adresse)) $errors['adresse'] = "Le champ 'Adresse' est requis.";
        if (empty($code_postal)) $errors['code_postal'] = "Le champ 'Code postal' est requis.";
        if (empty($ville)) $errors['ville'] = "Le champ 'Ville' est requis.";
        if (empty($departement)) $errors['departement'] = "Le champ 'Département' est requis.";
        if (empty($pays)) $errors['pays'] = "Le champ 'Pays' est requis.";

        if (empty($errors)) {
            $step = 3; // Passer à l'étape 3 si tout est valide
        }
    }
    // Étape 3 : Pseudomyne et Validation du mot de passe
    elseif ($step === 3) {

        $pseudomyne = trim($_POST['pseudomyne'] ?? '');
        $mot_de_passe = trim($_POST['mot-de-passe'] ?? '');
        $confirmation_mdp = trim($_POST['confirmation-mdp'] ?? '');

        // On vérifie que le pseudomyne n'existe pas déjà
        $stmt = $conn->prepare("SELECT * FROM _membre WHERE pseudonyme = ?");
        $stmt->bindValue(1, $pseudomyne, PDO::PARAM_STR);
        $stmt->execute();
        $pseudomyne_exists = $stmt->fetch();

        if (!empty($pseudomyne_exists)) $errors['pseudomyne'] = "Le pseudomyne existe déjà.";
        if (empty($pseudomyne)) $errors['pseudomyne'] = "Le champ 'Pseudomyne' est requis.";

        /*
        Le mot de passe doit contenir :
            Au minimun 8 caractères
            Au minimun 1 chiffre
            Au minimun 1 majuscule
            Au minimun 1 caractère spécial
        */
        $passwordRegex = '/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>])[A-Za-z\d!@#$%^&*(),.?":{}|<>]{8,}$/';
        if (!preg_match($passwordRegex, $mot_de_passe)) {
            $errors['mot-de-passe'] = "Le mot de passe doit contenir au moins 8 caractères, 1 chiffre, 1 majuscule et 1 caractère spécial.";
        }
        if ($mot_de_passe !== $confirmation_mdp) {
            $errors['confirmation-mdp'] = "Les mots de passe ne correspondent pas.";
        }

        if (empty($errors)) {
            // Formulaire complet
            echo "<h1>Votre compte a été créé avec succès !</h1>";
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer mon compte</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body class="body-creer-pro">
    <main class="form-container">
        <!-- Flèche de retour -->
        <div class="back-arrow">
            <a onclick="history.back();">&#8617;</a>
        </div>
        <!-- Étape 1 : Informations personnelles -->
        <?php if ($step === 1): ?>                
            <form method="POST" class="form-creer-pro">
                <input type="hidden" name="step" value="1">  
                <h1 class="subtitle">Créer mon compte Membre </h1>
                <h2>1. Apprenons à nous connaître</h2>

                <!-- Nom -->
                <div class="input-row">
                <div class="input-group">
                    <label for="nom">Nom</label>
                    <div class="input-container">
                        <input type="text" id="nom" name="nom" placeholder="Votre nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
                        <p class="error"><?= $errors['nom'] ?? '' ?></p>
                        <span class="required">*</span>
                    </div>
                </div>

                <!-- Prénom -->
                <div class="input-group">
                    <label for="prenom">Prénom</label>
                    <div class="input-container">
                        <input type="text" id="prenom" name="prenom" placeholder="Votre prénom" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required>
                        <p class="error"><?= $errors['prenom'] ?? '' ?></p>
                        <span class="required">*</span>
                    </div>
                </div>
                </div>

                <!-- Email -->
                <div class="input-group">
                    <label for="email">E-mail</label>
                    <div class="input-container">
                        <input type="email" id="email" name="email" placeholder="Votre email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        <p class="error"><?= $errors['email'] ?? '' ?></p>   
                        <span class="required">*</span>
                    </div>
                </div>

                <!-- Adresse -->
                <div class="input-group">
                    <label for="adresse">Adresse Postale</label>
                    <div class="input-container">
                        <input type="text" id="adresse" name="adresse" placeholder="Votre adresse" value="<?= htmlspecialchars($_POST['adresse'] ?? '') ?>"required>
                        <p class="error"><?= $errors['adresse'] ?? '' ?></p>
                        <span class="required">*</span>
                    </div>
                </div>

                <!-- Ville et téléphone -->
                <div class="input-row">
                    <div class="input-group">
                        <label for="ville">Ville</label>
                        <div class="input-container">
                            <input type="text" id="ville" name="ville" placeholder="Votre ville" value="<?= htmlspecialchars($_POST['ville'] ?? '') ?>"required>
                            <p class="error"><?= $errors['ville'] ?? '' ?></p>
                            <span class="required">*</span>
                        </div>
                    </div>
                    <div class="input-group">
                        <label for="tel">Téléphone</label>
                        <div class="input-container">
                            <input type="tel" id="tel" name="tel" placeholder="Votre téléphone" value="<?= htmlspecialchars($_POST['tel'] ?? '') ?>"required>
                            <p class="error"><?= $errors['tel'] ?? '' ?></p>
                            <span class="required">*</span>
                        </div>
                    </div>
                </div>

                <!-- Bouton suivant -->
                <div class="valide-groupe">
                    <button class="submit-btn" type="submit">SUIVANT</button>
                    <p class="almost-done">Plus qu'une étape 1/2</p>
                </div>
            </form>
        <?php endif; ?>

        <!-- Étape 2 : Sécurité du compte -->
        <?php if ($step === 2): ?>
            <h1 class="subtitle">Créer mon compte Membre </h1>
            <h2 class="form-section">2. Sécurisons votre compte</h2>
            <form method="POST" class="form-creer-pro">
                <input type="hidden" name="step" value="2">

                <div class="input-group">
                        <label for="tel">Pseudomyne</label>
                        <div class="input-container">
                            <input type="Pseudomyne" id="Pseudomyne" name="Pseudomyne" placeholder="Votre pseudomyne" value="<?= htmlspecialchars($_POST['Pseudomyne'] ?? '') ?>"required>
                            <p class="error"><?= $errors['Pseudomyne'] ?? '' ?></p>
                            <span class="required">*</span>
                        </div>
                    </div>

                <div class="input-group">
                    <label for="mot-de-passe">Mot de passe</label>
                    <div class="input-container">
                        <input type="password" id="mot-de-passe" name="mot-de-passe" placeholder="Votre mot de passe" required>
                        <p class="error"><?= $errors['mot-de-passe'] ?? '' ?></p>
                        <span class="required">*</span>
                    </div>
                </div>

                <div class="input-group">
                    <label class="label-mdp" for="confirmation-mdp">Confirmez votre mot de passe</label>
                    <div class="input-container">
                        <input class="confirmation" type="password" id="confirmation-mdp" name="confirmation-mdp" placeholder="Confirmez votre mot de passe" required>
                        <p class="error"><?= $errors['confirmation-mdp'] ?? '' ?></p>
                        <span class="required">*</span>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Créer mon compte</button>
                <div class="valide-groupe">
                <p class="terms">En cliquant sur "Créer mon compte", vous acceptez nos termes :
                    <ul>
                        <li>Conditions générales d'utilisation</li>
                        <li>Conditions générales de ventes</li>
                        <li>Politique de confidentialité</li>
                    </ul>
                </p>
                </div>
            </form>
        <?php endif; ?>
    </main>

</body>
</html>
