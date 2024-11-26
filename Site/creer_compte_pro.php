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

        if (empty($nom)) $errors['nom'] = "Le champ 'Nom' est requis.";
        if (empty($prenom)) $errors['prenom'] = "Le champ 'Prénom' est requis.";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = "L'adresse e-mail n'est pas valide.";
        if (empty($adresse)) $errors['adresse'] = "Le champ 'Adresse Postale' est requis.";
        if (empty($ville)) $errors['ville'] = "Le champ 'Ville' est requis.";
        if (!preg_match('/^\d{10}$/', $tel)) $errors['tel'] = "Le numéro de téléphone doit contenir 10 chiffres.";

        if (empty($errors)) {
            $step = 2; // Passer à l'étape 2 si tout est valide
        }
    }
    // Étape 2 : Validation des informations de l'entreprise
    elseif ($step === 2) {
        $siren = trim($_POST['siren'] ?? '');
        $raison_sociale = trim($_POST['raison-sociale'] ?? '');
        $iban = trim($_POST['iban'] ?? '');

        if (!preg_match('/^\d{9}$/', $siren)) $errors['siren'] = "Le numéro de SIREN doit contenir 9 chiffres.";
        if (empty($raison_sociale)) $errors['raison-sociale'] = "Le champ 'Raison sociale' est requis.";
        if (!preg_match('/^FR\d{12,27}$/', $iban)) $errors['iban'] = "L'IBAN doit être valide et commencer par 'FR'.";

        if (empty($errors)) {
            $step = 3; // Passer à l'étape 3 si tout est valide
        }
    }
    // Étape 3 : Validation du mot de passe
    elseif ($step === 3) {
        $mot_de_passe = trim($_POST['mot-de-passe'] ?? '');
        $confirmation_mdp = trim($_POST['confirmation-mdp'] ?? '');

        if (strlen($mot_de_passe) < 8) $errors['mot-de-passe'] = "Le mot de passe doit contenir au moins 8 caractères.";
        if ($mot_de_passe !== $confirmation_mdp) $errors['confirmation-mdp'] = "Les mots de passe ne correspondent pas.";

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
        <?php if ($step === 1): ?>
            <!-- Étape 1 -->
            <h2>1. Apprenons à nous connaître</h2>
            <form method="POST">
                <input type="hidden" name="step" value="1">
                <div class="input-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                    <p class="error"><?= $errors['nom'] ?? '' ?></p>
                </div>
                <div class="input-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
                    <p class="error"><?= $errors['prenom'] ?? '' ?></p>
                </div>
                <div class="input-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    <p class="error"><?= $errors['email'] ?? '' ?></p>
                </div>
                <div class="input-group">
                    <label for="adresse">Adresse Postale</label>
                    <input type="text" id="adresse" name="adresse" value="<?= htmlspecialchars($_POST['adresse'] ?? '') ?>">
                    <p class="error"><?= $errors['adresse'] ?? '' ?></p>
                </div>
                <div class="input-group">
                    <label for="ville">Ville</label>
                    <input type="text" id="ville" name="ville" value="<?= htmlspecialchars($_POST['ville'] ?? '') ?>">
                    <p class="error"><?= $errors['ville'] ?? '' ?></p>
                </div>
                <div class="input-group">
                    <label for="tel">Tel</label>
                    <input type="tel" id="tel" name="tel" value="<?= htmlspecialchars($_POST['tel'] ?? '') ?>">
                    <p class="error"><?= $errors['tel'] ?? '' ?></p>
                </div>
                <button type="submit">SUIVANT</button>
            </form>
        <?php elseif ($step === 2): ?>
            <!-- Étape 2 -->
            <h2>2. Et votre entreprise ?</h2>
            <form method="POST">
                <input type="hidden" name="step" value="2">
                <div class="input-group">
                    <label for="siren">Numéro de SIREN</label>
                    <input type="text" id="siren" name="siren" value="<?= htmlspecialchars($_POST['siren'] ?? '') ?>">
                    <p class="error"><?= $errors['siren'] ?? '' ?></p>
                </div>
                <div class="input-group">
                    <label for="raison-sociale">Raison sociale</label>
                    <input type="text" id="raison-sociale" name="raison-sociale" value="<?= htmlspecialchars($_POST['raison-sociale'] ?? '') ?>">
                    <p class="error"><?= $errors['raison-sociale'] ?? '' ?></p>
                </div>
                <div class="input-group">
                    <label for="iban">IBAN</label>
                    <input type="text" id="iban" name="iban" value="<?= htmlspecialchars($_POST['iban'] ?? '') ?>">
                    <p class="error"><?= $errors['iban'] ?? '' ?></p>
                </div>
                <button type="submit">VALIDER</button>
            </form>
        <?php elseif ($step === 3): ?>
            <!-- Étape 3 -->
            <h2>4. Sécurisons votre compte</h2>
            <form method="POST">
                <input type="hidden" name="step" value="3">
                <div class="input-group">
                    <label for="mot-de-passe">Mot de passe</label>
                    <input type="password" id="mot-de-passe" name="mot-de-passe">
                    <p class="error"><?= $errors['mot-de-passe'] ?? '' ?></p>
                </div>
                <div class="input-group">
                    <label for="confirmation-mdp">Confirmez votre mot de passe</label>
                    <input type="password" id="confirmation-mdp" name="confirmation-mdp">
                    <p class="error"><?= $errors['confirmation-mdp'] ?? '' ?></p>
                </div>
                <button type="submit">Créer mon compte</button>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>
