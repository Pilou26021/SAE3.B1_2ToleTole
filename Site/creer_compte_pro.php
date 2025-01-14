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
        if (!preg_match('/^\d{10}$/', $tel)) $errors['tel'] = "Le numéro de téléphone doit contenir 10 chiffres.";

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
    
    // Étape 3 : Validation des informations de l'entreprise
    elseif ($step === 3) {
        $denomination = trim($_POST['denomination'] ?? '');
        $siren = trim($_POST['siren'] ?? '');
        $raison_sociale = trim($_POST['raison-sociale'] ?? '');
        $iban = trim($_POST['iban'] ?? '');
        $bic = trim($_POST['bic'] ?? '');
        $organisation_type = trim($_POST['organisation_type'] ?? '');

        // Retire tout les espaces du SIREN
        $siren = str_replace(' ', '', $siren);

        if (empty($denomination)) $errors['denomination'] = "Le champ 'Dénomination' est requis.";
        if (!preg_match('/^\d{9,14}$/', $siren)) $errors['siren'] = "Le numéro de SIREN doit contenir entre 9 et 14 chiffres.";
        if (empty($raison_sociale)) $errors['raison-sociale'] = "Le champ 'Raison sociale' est requis.";
        if (!empty($iban) && !preg_match('/^FR\d{12,27}$/', $iban)) {
            $errors['iban'] = "L'IBAN doit être valide et commencer par 'FR'.";
        }
        if (!empty($bic) && !preg_match('/^[A-Z]{4}[A-Z]{2}[A-Z0-9]{2}([A-Z0-9]{3})?$/', $bic)) {
            $errors['bic'] = "Le BIC doit comporter 8 ou 11 caractères, être en majuscules et respecter le format.";
        }
        if (empty($organisation_type)) {
            $errors['organisation_type'] = "Veuillez sélectionner le type d'organisation.";
        }

        if (empty($errors)) {
            // Stock les informations dans la session
            $_SESSION['denomination'] = $denomination;
            $_SESSION['siren'] = $siren;
            $_SESSION['raison_sociale'] = $raison_sociale;
            $_SESSION['iban'] = $iban;
            $_SESSION['bic'] = $bic;
            $_SESSION['organisation_type'] = $organisation_type;

            $step = 4; // Passer à l'étape 4 si tout est valide
        }
    }

    
    // Étape 4 : Validation du mot de passe
    elseif ($step === 4) {
        $mot_de_passe = trim($_POST['mot-de-passe'] ?? '');
        $confirmation_mdp = trim($_POST['confirmation-mdp'] ?? '');

        $passwordRegex = '/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>])[A-Za-z\d!@#$%^&*(),.?":{}|<>]{8,}$/';
        if (!preg_match($passwordRegex, $mot_de_passe)) {
            $errors['mot-de-passe'] = "Le mot de passe doit contenir au moins 8 caractères, 1 chiffre, 1 majuscule et 1 caractère spécial.";
        }
        if ($mot_de_passe !== $confirmation_mdp) {
            $errors['confirmation-mdp'] = "Les mots de passe ne correspondent pas.";
        }

        if (empty($errors)) {
            $nom = strtoupper($_SESSION['nom']);
            $prenom = ucfirst($_SESSION['prenom']);
            $email = $_SESSION['email'];
            $tel = $_SESSION['tel'];
            $adNumRue = $_SESSION['adNumRue'];
            $supplementAdresse = $_SESSION['supplementAdresse'];
            $adresse = $_SESSION['adresse'];
            $code_postal = $_SESSION['code_postal'];
            $ville = $_SESSION['ville'];
            $departement = $_SESSION['departement'];
            $pays = $_SESSION['pays'];
            $denomination = $_SESSION['denomination'];
            $siren = $_SESSION['siren'];
            $raison_sociale = $_SESSION['raison_sociale'];
            $iban = $_SESSION['iban'];
            $bic = $_SESSION['bic'];
            $organisation_type = $_SESSION['organisation_type'];
            $mot_de_passe = password_hash($mot_de_passe, PASSWORD_DEFAULT);

            // Insérer les informations dans la base de données

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

            // Récupère l'id du compte
            $idCompte = $conn->lastInsertId();

            // Attache raison sociale à la denonimation
            $denomination = $denomination . " - " . $raison_sociale;

            // Professionnel
            $stmt = $conn->prepare("INSERT INTO _professionnel (idcompte, denominationpro, numsirenpro) VALUES (?, ?, ?)");
            $stmt->bindValue(1, $idCompte, PDO::PARAM_INT);
            $stmt->bindValue(2, $denomination, PDO::PARAM_STR);
            $stmt->bindValue(3, $siren, PDO::PARAM_STR);
            $stmt->execute();

            // Récupère l'id du professionnel
            $idPro = $conn->lastInsertId();

            if ($organisation_type === 'prive') {
                // Insertion dans proprive
                $stmt = $conn->prepare("INSERT INTO _professionnelprive (idpro, coordbancairesiban, coordbancairesbic) VALUES (?, ?, ?)");
                $stmt->bindValue(1, $idPro, PDO::PARAM_INT);
                $stmt->bindValue(2, $iban, PDO::PARAM_STR);
                $stmt->bindValue(3, $bic, PDO::PARAM_STR);
                $stmt->execute();
            }
            elseif ($organisation_type === 'public') {
                // Insertion dans propublic
                $stmt = $conn->prepare("INSERT INTO _professionnelpublic (idpro) VALUES (?)");
                $stmt->bindValue(1, $idPro, PDO::PARAM_INT);
                $stmt->execute();
            }

            session_destroy();
            $step = 5; // Passer à l'étape 5 si tout est valide
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
<?php if ($step !== 5): ?>
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

                <!-- Ville et téléphone -->
                <div class="input-row">
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
                    <p class="almost-done">Vous y êtes presque 1/4</p>
                </div>
            </form>
        <?php endif; ?>

        <!-- Étape 2 : Adresse du compte -->

            <?php if ($step === 2): ?>                
        <form method="POST" class="form-creer-pro">
            <input type="hidden" name="step" value="2">  
            <h1 class="subtitle">Créer mon compte Professionnel</h1>
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
                    <input type="text" id="code_postal" name="code_postal" placeholder="Votre code postal" value="<?= htmlspecialchars($_POST['code_postal'] ?? '') ?>"required>
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
                <p class="almost-done">C'est bientôt la fin 2/4</p>
            </div>
        </form>
    <?php endif; ?>

        <!-- Étape 3 : Informations sur l'entreprise -->
        <?php if ($step === 3): ?>
            <h1 class="subtitle">Créer mon compte Professionnel </h1>
            <h2>3. Et votre entreprise ?</h2>
            <form method="POST" class="form-creer-pro">
                <input type="hidden" name="step" value="3">

                <div class="input-row">
                <div class="input-group">
                    <label for="denomination">Dénomination</label>
                    <div class="input-container">
                        <input type="text" id="denomination" name="denomination" placeholder="Votre denomination" value="<?= htmlspecialchars($_POST['denomination'] ?? '') ?>" required>
                        <p class="error"><?= $errors['denomination'] ?? '' ?></p>
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
                </div>

                <!-- Type d'organisation -->
                <div class="input-group">
                    <label>Type d'organisation</label>
                    <div class="input-row2">
                        <input type="radio" id="prive" name="organisation_type" value="prive" 
                            <?= (isset($_POST['organisation_type']) && $_POST['organisation_type'] === 'prive') ? 'checked' : '' ?>>
                        <label for="prive">Organisation privé</label>

                        <input type="radio" id="association" name="organisation_type" value="public" 
                            <?= (isset($_POST['organisation_type']) && $_POST['organisation_type'] === 'association') ? 'checked' : '' ?>>
                        <label for="association">Association</label>
                        
                        <input type="radio" id="public" name="organisation_type" value="public" 
                            <?= (isset($_POST['organisation_type']) && $_POST['organisation_type'] === 'public') ? 'checked' : '' ?>>
                        <label for="public">Organisation publique</label>
                    </div>
                    <p class="error"><?= $errors['organisation_type'] ?? '' ?></p>
                </div>

                <!-- Message d'avertissement pour les organisations privées -->
                <div id="iban-bic-warning" class="warning" style="display: none;">
                    <p>Pour les organisations privées, il est recommandé de remplir l'IBAN et le BIC pour les paiements. Vous pourrez aussi le faire plus tard.</p>
                </div>

                <div class="input-row">
                <div class="input-group">
                    <label for="siren">Numéro de SIREN</label>
                    <div class="input-container">
                        <input type="text" id="siren" name="siren" placeholder="SIREN" value="<?= htmlspecialchars($_POST['siren'] ?? '') ?>" required>
                        <p class="error"><?= $errors['siren'] ?? '' ?></p>
                        <span class="required">*</span>
                    </div>
                </div>

                <div class="input-group">
                    <label for="iban">BIC</label>
                    <div class="input-container">
                        <input type="text" id="bic" name="bic" placeholder="Votre BIC" value="<?= htmlspecialchars($_POST['bic'] ?? '') ?>" >
                        <p class="error"><?= $errors['bic'] ?? '' ?></p>
                    </div>
                </div>
                </div>

                <div class="input-group">
                    <label for="iban">IBAN</label>
                    <div class="input-container">
                        <input type="text" id="iban" name="iban" placeholder="Votre IBAN" value="<?= htmlspecialchars($_POST['iban'] ?? '') ?>" >
                        <p class="error"><?= $errors['iban'] ?? '' ?></p>
                    </div>
                </div>



                <div class="valide-groupe">
                <button type="submit" class="submit-btn">VALIDER</button>
                <p class="almost-done">Plus qu'une étape 3/4</p>
                </div>
            </form>
        <?php endif; ?>


        <!-- Étape 4 : Sécurité du compte -->
        <?php if ($step === 4): ?>
            <h1 class="subtitle">Créer mon compte Professionnel </h1>
            <h2 class="form-section">4. Sécurisons votre compte</h2>
            <form method="POST" class="form-creer-pro">
                <input type="hidden" name="step" value="4">

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

                <div class="valide-groupe3">
                    <p class="terms">Le mot de passe doit contenir :</p>
                    <ul>
                        <li>Au minimun 8 caractères</li>
                        <li>Au minimun 1 chiffre</li>
                        <li>Au minimun 1 majuscule</li>
                        <li>Au minimun 1 caractère spécial</li>
                    </ul>
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
<?php if ($step === 5): ?>
    <div class="success">
        <h1>Votre compte a été créé avec succès !</h1>
        <p>Vous allez être redirigé vers la page de connexion...</p>
        <?php header("Location: connexion_pro.php"); ?>
    </div>
<?php endif; ?>


    <script>
    // Sélectionner les éléments
    const radioButtons = document.querySelectorAll('input[name="organisation_type"]');
    const ibanField = document.getElementById('iban');
    const bicField = document.getElementById('bic');
    const warningMessage = document.getElementById('iban-bic-warning');

    // Fonction pour gérer le changement
    function handleOrganisationTypeChange(event) {
        if (event.target.value === 'prive') {
            // Afficher le message et activer les champs
            warningMessage.style.display = 'block';
            ibanField.disabled = false;
            bicField.disabled = false;
        } else {
            // Cacher le message et désactiver les champs
            warningMessage.style.display = 'none';
            ibanField.disabled = true;
            bicField.disabled = true;
            ibanField.value = ''; // Réinitialiser les valeurs
            bicField.value = '';
        }
    }

    // Ajouter des écouteurs d'événements à chaque bouton radio
    radioButtons.forEach(button => {
        button.addEventListener('change', handleOrganisationTypeChange);
    });

    // Initialiser l'état (utile pour les valeurs pré-sélectionnées)
    document.addEventListener('DOMContentLoaded', () => {
        const selected = document.querySelector('input[name="organisation_type"]:checked');
        if (selected) {
            handleOrganisationTypeChange({ target: selected });
        }
    });
    </script>

</body>
</html>
