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
        $phoneRegex = '/^[0-9]{10}$/';
        if (!preg_match($phoneRegex, $tel)) $errors['tel'] = "Le numéro de téléphone doit contenir 10 chiffres.";

        if (empty($errors)) {
            // Stock les informations dans la session
            $_SESSION['nom'] = $nom;
            $_SESSION['prenom'] = $prenom;
            $_SESSION['email'] = $email;
            $_SESSION['tel'] = $tel;

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
            // Stock les informations dans la session
            $_SESSION['adNumRue'] = $adNumRue;
            $_SESSION['supplementAdresse'] = $supplementAdresse;
            $_SESSION['adresse'] = $adresse;
            $_SESSION['code_postal'] = $code_postal;
            $_SESSION['ville'] = $ville;
            $_SESSION['departement'] = $departement;
            $_SESSION['pays'] = $pays;

            $step = 3; // Passer à l'étape 3 si tout est valide
        }
    }
    // Étape 3 : Pseudonyme et Validation du mot de passe
    elseif ($step === 3) {

        $pseudonyme = trim($_POST['pseudonyme'] ?? '');
        $mot_de_passe = trim($_POST['mot-de-passe'] ?? '');
        $confirmation_mdp = trim($_POST['confirmation-mdp'] ?? '');

        // On vérifie que le pseudonyme n'existe pas déjà
        $stmt = $conn->prepare("SELECT * FROM _membre WHERE pseudonyme = ?");
        $stmt->bindValue(1, $pseudonyme, PDO::PARAM_STR);
        $stmt->execute();
        $pseudonyme_exists = $stmt->fetch();

        if (!empty($pseudonyme_exists)) $errors['pseudonyme'] = "Le pseudonyme existe déjà.";
        if (empty($pseudonyme)) $errors['pseudonyme'] = "Le champ 'Pseudonyme' est requis.";

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
            // Stock les informations dans la session
            $_SESSION['pseudonyme'] = $pseudonyme;
            $_SESSION['mot-de-passe'] = password_hash($mot_de_passe, PASSWORD_DEFAULT);

            $step = 4; // Passer à l'étape 4 si tout est valide
        }
    }
    
        // Confirme la création du compte et propose de se connecter et fait un insert dans la base de donnée
        elseif ($step === 4) {
            // Met le nom en majuscule
            $nom = strtoupper($_SESSION['nom']);
            $prenom = $_SESSION['prenom'];
            $email = $_SESSION['email'];
            $tel = $_SESSION['tel'];
            $adNumRue = $_SESSION['adNumRue'];
            $supplementAdresse = $_SESSION['supplementAdresse'];
            $adresse = $_SESSION['adresse'];
            $code_postal = $_SESSION['code_postal'];
            $ville = $_SESSION['ville'];
            $departement = $_SESSION['departement'];
            $pays = $_SESSION['pays'];
            $pseudonyme = $_SESSION['pseudonyme'];
            $mot_de_passe = $_SESSION['mot-de-passe'];

            // Insertion des données dans la base de données
            
            // Adresse
            $stmt = $conn->prepare("INSERT INTO _adresse (numRue, supplementAdresse, adresse, codePostal, ville, departement, pays) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bindValue(1, $adNumRue, PDO::PARAM_INT);
            $stmt->bindValue(2, $supplementAdresse, PDO::PARAM_STR);
            $stmt->bindValue(3, $adresse, PDO::PARAM_STR);
            $stmt->bindValue(4, $code_postal, PDO::PARAM_INT);
            $stmt->bindValue(5, $ville, PDO::PARAM_STR);
            $stmt->bindValue(6, $departement, PDO::PARAM_STR);
            $stmt->bindValue(7, $pays, PDO::PARAM_STR);
            $stmt->execute();

            $listError["adresse"] = $stmt->errorInfo();

            // Récupère l'id de l'adresse
            $idAdresse = $conn->lastInsertId();

            // Compte
            $stmt = $conn->prepare("INSERT INTO _compte (nomCompte, prenomCompte, mailCompte, numTelCompte, hashMdpCompte, idAdresse, idImagePdp, dateDerniereConnexionCompte) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bindValue(1, $nom, PDO::PARAM_STR);
            $stmt->bindValue(2, $prenom, PDO::PARAM_STR);
            $stmt->bindValue(3, $email, PDO::PARAM_STR);
            $stmt->bindValue(4, $tel, PDO::PARAM_STR);
            $stmt->bindValue(5, $mot_de_passe, PDO::PARAM_STR);
            $stmt->bindValue(6, $idAdresse, PDO::PARAM_INT);
            $stmt->bindValue(7, 15, PDO::PARAM_INT);
            $stmt->execute();

            // Liste des erreurs
            $listError["compte"] = $stmt->errorInfo();

            // Récupère l'id du compte
            $idCompte = $conn->lastInsertId();

            $stmt = $conn->prepare("INSERT INTO _membre (idCompte, pseudonyme) VALUES (?, ?)");
            $stmt->bindValue(1, $idCompte, PDO::PARAM_INT);
            $stmt->bindValue(2, $pseudonyme, PDO::PARAM_STR);
            $stmt->execute();

            // Liste des erreurs
            $listError["membre"] = $stmt->errorInfo();
    
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
    <?php if ($step !== 4): ?>
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

                    <!-- Téléphone -->
                    <div class="input-group">
                        <label for="tel">Téléphone</label>
                        <div class="input-container">
                            <input type="tel" id="tel" name="tel" placeholder="Votre téléphone" value="<?= htmlspecialchars($_POST['tel'] ?? '') ?>"required>
                            <p class="error"><?= $errors['tel'] ?? '' ?></p>
                            <span class="required">*</span>
                        </div>
                    </div> 

                    <!-- Bouton suivant -->
                    <div class="valide-groupe">
                        <button class="submit-btn" type="submit">SUIVANT</button>
                        <p class="almost-done">Vous y êtes presque 1/3</p>
                    </div>
                </form>
            <?php endif; ?>

            <!-- Étape 2 : Adresse du compte -->

            <?php if ($step === 2): ?>                
                <form method="POST" class="form-creer-pro">
                    <input type="hidden" name="step" value="2">  
                    <h1 class="subtitle">Créer mon compte Membre </h1>
                    <h2>2. Votre Adresse</h2>

                    <!-- Rue -->
                    <div class="input-row">
                    <div class="input-group">
                        <label for="nom">Numéro de rue</label>
                        <div class="input-container">
                            <input type="text" id="adNumRue" name="adNumRue" placeholder="Votre Rue" value="<?= htmlspecialchars($_POST['adNumRue'] ?? '') ?>" required>
                            <p class="error"><?= $errors['adNumRue'] ?? '' ?></p>
                            <span class="required">*</span>
                        </div>
                    </div>

                    <!-- supplementAdresse -->
                    <div class="input-group">
                        <label for="supplementAdresse">Adresse Supplémentaire</label>
                        <div class="input-container">
                            <input type="text" id="supplementAdresse" name="supplementAdresse" placeholder="Addresse supp (ex: bis)" value="<?= htmlspecialchars($_POST['supplementAdresse'] ?? '') ?>">
                            <p class="error"><?= $errors['supplementAdresse'] ?? '' ?></p>
                        </div>
                    </div>
                    </div>

                    <!-- Adresse -->
                    <div class="input-group">
                        <label for="adresse">Adresse</label>
                        <div class="input-container">
                            <input type="text" id="adresse" name="adresse" placeholder="Votre Adresse" value="<?= htmlspecialchars($_POST['adresse'] ?? '') ?>" required>
                            <p class="error"><?= $errors['adresse'] ?? '' ?></p>   
                            <span class="required">*</span>
                        </div>
                    </div>

                    <!-- code Postal -->
                    <div class="input-row">
                    <div class="input-group">
                        <label for="code_postale">Code Postal</label>
                        <div class="input-container">
                            <input type="number" id="code_postal" name="code_postal" placeholder="Votre code postal" value="<?= htmlspecialchars($_POST['code_postal'] ?? '') ?>"required>
                            <p class="error"><?= $errors['code_postal'] ?? '' ?></p>
                            <span class="required">*</span>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="ville">Ville</label>
                        <div class="input-container">
                            <input type="text" id="ville" name="ville" placeholder="Votre ville" value="<?= htmlspecialchars($_POST['ville'] ?? '') ?>"required>
                            <p class="error"><?= $errors['ville'] ?? '' ?></p>
                            <span class="required">*</span>
                        </div>
                    </div>
                    </div>

                    <!-- Département et pays -->
                    <div class="input-row">
                        <div class="input-group">
                            <label for="departement">Département</label>
                            <div class="input-container">
                                <input type="text" id="departement" name="departement" placeholder="Votre departement" value="<?= htmlspecialchars($_POST['departement'] ?? '') ?>"required>
                                <p class="error"><?= $errors['departement'] ?? '' ?></p>
                                <span class="required">*</span>
                            </div>
                        </div>
                        <div class="input-group">
                            <label for="pays">Pays</label>
                            <div class="input-container">
                                <input type="pays" id="pays" name="pays" placeholder="Votre pays" value="<?= htmlspecialchars($_POST['pays'] ?? '') ?>"required>
                                <p class="error"><?= $errors['pays'] ?? '' ?></p>
                                <span class="required">*</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bouton suivant -->
                    <div class="valide-groupe">
                        <button class="submit-btn" type="submit">SUIVANT</button>
                        <p class="almost-done">Plus qu'une étape 2/3</p>
                    </div>
                </form>
            <?php endif; ?>

            <!-- Étape 3 : Sécurité du compte -->
            <?php if ($step === 3): ?>
                <h1 class="subtitle">Créer mon compte Membre </h1>
                <h2 class="form-section">3. Sécurisons votre compte</h2>
                <form method="POST" class="form-creer-pro">
                    <input type="hidden" name="step" value="3">

                    <div class="input-group">
                        <label for="pseudonyme">Pseudonyme</label>
                        <div class="input-container">
                            <input type="text" id="pseudonyme" name="pseudonyme" placeholder="Votre pseudonyme" value="<?= htmlspecialchars($_POST['pseudonyme'] ?? '') ?>" required>
                            <p class="error"><?= $errors['pseudonyme'] ?? '' ?></p>
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
    <?php endif; ?>
    <?php if ($step === 4): ?>
        <div class="success">
            <h1>Votre compte a été créé avec succès !</h1>
            <p>Vous pouvez maintenant vous connecter.</p>
            <a href="connexion_membre.php" class="submit-btn">Se connecter</a>
        </div>

        <?php var_dump($_SESSION); ?>       
    <?php endif; ?>

</body>
</html>
