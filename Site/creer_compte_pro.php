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
        $denomination = trim($_POST['denomination']);
        $adresse = trim($_POST['adresse'] ?? '');
        $ville = trim($_POST['ville'] ?? '');
        $tel = trim($_POST['tel'] ?? '');

        if (empty($nom)) $errors['nom'] = "Le champ 'Nom' est requis.";
        if (empty($prenom)) $errors['prenom'] = "Le champ 'Prénom' est requis.";
        if (empty($denomination)) $errors['denomination'] = "Le champ 'Dénomination' est requis.";
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
        <!-- Flèche de retour -->
        <div class="back-arrow">
            <a onclick="history.back();">&#8617;</a>
        </div>
        <!-- Étape 1 : Informations personnelles -->
        <?php if ($step === 1): ?>                
            <form method="POST" class="form-creer-pro">
                <input type="hidden" name="step" value="1">  
                <h1 class="subtitle">Créer mon compte Professionnel </h1>
                <h2>1. Apprenons à nous connaître</h2>

                <!-- Nom -->
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

                <div class="input-group">
                    <label for="denomination">Dénomination</label>
                    <div class="input-container">
                        <input type="text" id="denomination" name="denomination" placeholder="Votre denomination" value="<?= htmlspecialchars($_POST['denomination'] ?? '') ?>" required>
                        <p class="error"><?= $errors['denomination'] ?? '' ?></p>
                        <span class="required">*</span>
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
                    <p class="almost-done">Vous y êtes presque</p>
                </div>
            </form>
        <?php endif; ?>

        <!-- Étape 2 : Informations sur l'entreprise -->
        <?php if ($step === 2): ?>
            <h2 class="form-section">2. Et votre entreprise ?</h2>
            <form method="POST" class="form-creer-pro">
                <input type="hidden" name="step" value="2">

                <div class="input-group">
                    <label for="siren">Numéro de SIREN</label>
                    <div class="input-container">
                        <input type="text" id="siren" name="siren" placeholder="SIREN" value="<?= htmlspecialchars($_POST['siren'] ?? '') ?>" required>
                        <p class="error"><?= $errors['siren'] ?? '' ?></p>
                        <span class="required">*</span>
                    </div>
                </div>

                <div class="input-group">
                    <label for="raison-sociale">Raison sociale</label>
                    <div class="input-container">
                        <input type="text" id="raison-sociale" name="raison-sociale" placeholder="Raison sociale" value="<?= htmlspecialchars($_POST['raison-sociale'] ?? '') ?>" required>
                        <p class="error"><?= $errors['raison-sociale'] ?? '' ?></p>
                        <span class="required">*</span>
                    </div>
                </div>

                <div class="input-group">
                    <label for="iban">IBAN</label>
                    <div class="input-container">
                        <input type="text" id="iban" name="iban" placeholder="Votre IBAN" value="<?= htmlspecialchars($_POST['iban'] ?? '') ?>" required>
                        <p class="error"><?= $errors['iban'] ?? '' ?></p>
                        <span class="required">*</span>
                    </div>
                </div>

                <button type="submit" class="submit-btn">VALIDER</button>
            </form>
        <?php endif; ?>

        <!-- Étape 3 : Sécurité du compte -->
        <?php if ($step === 3): ?>
            <h2 class="form-section">3. Sécurisons votre compte</h2>
            <form method="POST" class="form-creer-pro">
                <input type="hidden" name="step" value="3">

                <div class="input-group">
                    <label for="mot-de-passe">Mot de passe</label>
                    <div class="input-container">
                        <input type="password" id="mot-de-passe" name="mot-de-passe" placeholder="Votre mot de passe" required>
                        <p class="error"><?= $errors['mot-de-passe'] ?? '' ?></p>
                        <span class="required">*</span>
                    </div>
                </div>

                <div class="input-group">
                    <label for="confirmation-mdp">Confirmez votre mot de passe</label>
                    <div class="input-container">
                        <input class="confirmation" type="password" id="confirmation-mdp" name="confirmation-mdp" placeholder="Confirmez votre mot de passe" required>
                        <p class="error"><?= $errors['confirmation-mdp'] ?? '' ?></p>
                        <span class="required">*</span>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Créer mon compte</button>
                <p class="terms">
                    En cliquant sur "Créer mon compte", vous acceptez nos termes :
                    <ul>
                        <li>Conditions générales d'utilisation</li>
                        <li>Conditions générales de ventes</li>
                        <li>Politique de confidentialité</li>
                    </ul>
                </p>
            </form>
        <?php endif; ?>
    </main>
    <div id="footer"></div>

    <script src="./script.js" ></script>

</body>
</html>
