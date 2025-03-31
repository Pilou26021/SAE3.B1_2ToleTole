<?php

    ob_start();

    include "header.php";
    include "../SQL/connection_local.php";
    // Vérifie qu'un utilisateur est connecté
    if (!isset($_SESSION['membre']) && !isset($_SESSION['professionnel'])) {
        header("Location: ./connexion_membre.php");
        exit();
    }

    if (isset($_SESSION['membre'])) {
        $idCompte = $_SESSION['membre'];
    } elseif (isset($_SESSION['professionnel'])) {
        $id = $_SESSION['idpro'];
        $stmt = $conn->prepare("SELECT idcompte FROM _professionnel WHERE idpro = ?");
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->execute();
        $idCompte = $stmt->fetch(PDO::FETCH_ASSOC);
        $idCompte = $idCompte['idcompte'];
    }

    $professionel = false;
    $membre = false;
    // On vérifie si l'utilisateur est connecté. Il peut être connecté en tant que membre ou professionnel. Si il n'est pas connecté alors il sera visiteur.
    if (isset($_SESSION['membre'])) {
        $membre = true;
        $typecompte = "membre";
    } elseif (isset($_SESSION['professionnel'])) {
        $professionel = true;
        $typecompte = "professionel";
    }

    // On récupère si l'authentifikator est activé
    $stmt = $conn->prepare("SELECT auth_parametre FROM _compte WHERE idcompte = ?");
    $stmt->bindParam(1, $idCompte, PDO::PARAM_INT);
    $stmt->execute();
    $authParametre = $stmt->fetch(PDO::FETCH_ASSOC);
    $authParametre = $authParametre['auth_parametre'];
?>  

<!DOCTYPE html>
<html lang="fr">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link rel="stylesheet" href="./style.css">   
    <title>Sécurité</title>
</head>
    <body>
        <!-- Flèche de retour à la page mon_compte -->
        <div style=" position:sticky; top:20px; left:20px; width: 100%;">
                <a style="text-decoration: none; font-size: 30px; color: #040316; cursor: pointer;" href="./mon_compte.php">&#8617;</a>
        </div>
        
        <script>
            function verifierComplexiteMdp() {
                const mdp1 = document.getElementById("mdp1").value;
                const message = document.getElementById("complexiteMessage");
                const regex = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>])[A-Za-z\d!@#$%^&*(),.?":{}|<>]{8,}$/;
                if (mdp1 === "") {
                    message.textContent = "";
                } else if (!regex.test(mdp1)) {
                    message.textContent = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre.";
                    message.style.color = "red";
                } else {
                    message.textContent = "Mot de passe valide.";
                    message.style.color = "green";
                }
            }
            function verifierCorrespondanceMdp() {
                const mdp1 = document.getElementById("mdp1").value;
                const mdp2 = document.getElementById("mdp2").value;
                const message = document.getElementById("correspondanceMessage");
                if (mdp2 === "") {
                    message.textContent = "";
                } else if (mdp1 !== mdp2) {
                    message.textContent = "Les mots de passe ne correspondent pas.";
                    message.style.color = "red";
                } else {
                    message.textContent = "Les mots de passe correspondent.";
                    message.style.color = "green";
                }
            }
            function verifierMotDePasse() {
                const mdp1 = document.getElementById("mdp1").value;
                const mdp2 = document.getElementById("mdp2").value;
                const regex = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>])[A-Za-z\d!@#$%^&*(),.?":{}|<>]{8,}$/;
                if (!regex.test(mdp1)) {
                    alert("Le mot de passe ne respecte pas les critères de sécurité.");
                    return false;
                }
                if (mdp1 !== mdp2) {
                    alert("Les mots de passe ne correspondent pas.");
                    return false;
                }
                // Requete pour changer le mot de passe
                valideMotDePasse(function(success) {
                    if (success) {
                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", "./api/changer_mdp.php", true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhr.send("mdp=" + mdp1 + "&idcompte=" + <?php echo $idCompte; ?>);
                        // Alerte l'utilisateur que le mot de passe a été modifié et raffaichie la page quand il clique sur ok
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === 4 && xhr.status === 200) {
                                alert("Le mot de passe a été modifié.");
                                location.reload();
                            }
                        };
                    } else {
                        alert("Le mot de passe actuel est incorrect.");
                    }
                });
            }

            function valideMotDePasse(callback) {
                var mdp = document.getElementById("mdp").value;
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "./api/valider_mdp.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            var response = JSON.parse(xhr.responseText);
                            callback(response.success); // Exécute le callback avec true ou false.
                        } else {
                            callback(false); // En cas d'erreur serveur, retourne false.
                        }
                    }
                };
                xhr.send("mdp=" + encodeURIComponent(mdp) + "&idcompte=" + <?php echo $idCompte; ?>);
            }
                    
            // Fonction pour regénérer la clé API
            function regenerer_cleapi() {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "./api/cleapi_generation.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.send("typecompte=" + "<?php echo $typecompte; ?>");
                // On reçois la clé API générée
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        var cleapi = xhr.responseText;
                        document.querySelector(".cleapi p").innerHTML = 'xxx-xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx';
                        document.querySelector(".cleapi").classList.add("floutage");
                        changer_cleapi(cleapi);
                    }
                };
            }
                
            // Fonction pour afficher la clé API
            function afficher_cleapi() {
                cleapi_devoile(function (cleEstDevoile) {
                    if (cleEstDevoile == 0) {
                        get_cleapi(function (cleapi) {
                        document.querySelector(".cleapi p").innerHTML = cleapi;
                            document.querySelector(".cleapi").classList.remove("floutage");
                            devoiler_cleapi();
                        });
                    } else {
                        document.querySelector(".cleapi p").innerHTML = "La clé API a déjà été dévoilée.";
                        document.querySelector(".cleapi").classList.remove("floutage");
                    }
                });
            }
            
            function changer_cleapi(cleapi) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "./api/changer_cleapi.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.send("cleapi=" + cleapi + "&idcompte=" + <?php echo $idCompte; ?>);
            }
                
            function devoiler_cleapi() {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "./api/devoiler_cleapi.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.send("idcompte=" + <?php echo $idCompte; ?>);
            }
                
            function get_cleapi(callback) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "./api/get_cleapi.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.send("idcompte=" + <?php echo $idCompte; ?>);
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        callback(xhr.responseText);
                    }
                };
            }
                
            function cleapi_devoile(callback) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "./api/cleapi_devoile.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.send("idcompte=" + <?php echo $idCompte; ?>);
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        callback(xhr.responseText);
                    }
                };
            }
        </script>

<main class="securite_main">
    <div class="securite_container">
        <!-- Modification du mot de passe -->
        <div class="securite_div <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>">
            <h2>Modifier le mot de passe</h2>
            <form>
                <label for="mdp">Mot de passe actuel</label>
                <input type="password" id="mdp" name="mdp" required>
                <br>
                <label for="mdp1">Nouveau mot de passe</label>
                <input type="password" id="mdp1" name="mdp1" required oninput="verifierComplexiteMdp()">
                <small id="complexiteMessage" class="message-erreur"></small>
                <br>
                <label for="mdp2">Confirmer le mot de passe</label>
                <input type="password" id="mdp2" name="mdp2" required oninput="verifierCorrespondanceMdp()">
                <small id="correspondanceMessage" class="message-erreur"></small>
                <br>
                <button type="button" onclick="verifierMotDePasse()">Modifier</button>
            </form>
            <section class="alerte_mdp <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>">
                <p>Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre.</p>
            </section>
        </div>

        <!-- Clé API -->
        <div class="securite_div <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>">
            <h2>Clé API</h2>
            <div class="securite_center">
            <div class="cleapi floutage">
                <p>xxx-xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx</p>
            </div>
            <button id="btn_regenerer" onclick="regenerer_cleapi()">Regénérer la clé API</button>
            <button id="btn_afficher" onclick="afficher_cleapi()">Afficher la clé API</button>
            </div>
            <p>Conservez cette clé API en lieu sûr. Elle vous permet d'accéder à l'API de notre site. Une fois générée, vous ne pourrez plus la voir en clair.</p>
            <p>Si vous avez perdu votre clé API, vous pouvez en générer une nouvelle en cliquant sur le bouton "Regénérer la clé API".</p>
            <hr class="securite_hr">
            <?php if ($authParametre == true) { ?>
                <h2>Authentification à deux facteurs</h2>
                <p>L'authentification à deux facteurs est activée pour votre compte. Pour la désactiver, cliquez sur le bouton ci-dessous.</p>
                <button id="btn_des-activer" onclick="">Désactiver l'authentification à deux facteurs</button>
            <?php } else { ?>
                <h2>Authentification à deux facteurs</h2>
                <p>L'authentification à deux facteurs est désactivée pour votre compte. Pour l'activer, cliquez sur le bouton ci-dessous.</p>
                <button class="securite_center" id="btn_des-activer" onclick="popup_2fa()">Activer l'authentification à deux facteurs</button>
            <?php } ?>
        </div>
    </div>
</main>

<script>
    function popup_2fa() {
        var popup = document.querySelector('.auth-container .display-none');
        var popup2 = document.querySelector('.auth-container');
        if (popup2) {
            popup2.style.display = 'block';
        } else {
            console.error("L'élément .auth-container n'existe pas !");
        }
        if (popup) {
            popup.style.display = 'block';
        } else {
            console.error("L'élément .display-none à l'intérieur de .auth-container n'existe pas !");
        }
    }

    function closePopup() {
        var popup = document.querySelector('.auth-container');
        if (popup) {
            popup.style.display = 'none';
        } else {
            console.error("L'élément .auth-container n'existe pas !");
        }
    }

    function get_secret() {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'api/auth_gensecret.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var response = JSON.parse(xhr.responseText);
                document.querySelector('.popup-content-right-inner-qr img').src = response.qrcode;
                document.getElementById('secret').innerText = response.secret;
            }
        };
        xhr.send('idcompte=' + <?php echo $idcompte; ?>);
    }
    // array avec secret et qrcode
    get_secret();
    
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('.popup').style.display = 'block';
    });

    function valider_otp() {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'api/auth_paramotp.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                if (xhr.responseText == 'true') {
                    var popup = document.querySelectorAll('.display-none');
                    if (popup.length > 1) {
                        popup[1].style.display = 'none';
                        popup[2].style.display = 'block';
                    } else {
                        console.error("L'élément .popup n'existe pas !");
                    }

                    // Stock a value in the session
                    sessionStorage.setItem('forceLogout', 'true');

                    setTimeout(function() {
                        // Redirige vers la page de déconnexion
                        window.location.replace('deconnexion.php');
                    }, 5000); // 5 secondes
                } else {
                    alert('Code OTP invalide');
                }
            }
        };
        var codeotp = document.getElementById('otp').value;
        xhr.send('idcompte=' + <?php echo $idcompte; ?> + '&codeotp=' + codeotp);
    }


</script>

<div class="auth-container display-none">
    <div class="display-none">
        <div class="popup-header">
            <h2>Authentification à deux facteurs</h2>
            <button class="close-popup" onclick="closePopup();">X</button>
        </div>
        <div class="popup-body">
            <div class="popup-content">
                <div class="popup-content-inner">
                    <div class="popup-content-left">
                        <div class="popup-content-left-inner">
                            <h3>Étape 1: Installer une application d'authentification à deux facteurs</h3>
                        </div>
                    </div>
                    <div class="popup-content-right">
                        <div class="popup-content-right-inner">
                            <h3>Étape 2: Scannez le code QR ou entrez la clé secrète ci-dessous</h3>
                            <p>Ouvrir votre application d'authentification et scanner le code QR ci-dessous ou entrez la clé secrète manuellement.</p>
                            <div class="popup-content-right-inner-qr">
                                <img src="" alt="QR Code">
                            </div>
                            <div class="popup-content-right-inner-secret">
                                <p>Clé secrète: <span id="secret"></span></p>
                            </div>
                        </div>
                    </div>

                    <div class="popup-content-left">
                        <div class="popup-content-left-inner">
                            <h3>Étape 3: Entrez le code OTP</h3>
                            <p>Entrez le code OTP généré par votre application d'authentification à deux facteurs.</p>
                            <div class="input-row">    
                                <input type="text" id="otp" name="otp" placeholder="Code OTP">
                                <button class="button button-primary" onclick="valider_otp();">Valider</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="popup-footer">
            <button class="button button-primary" onclick="popup_2fa2();">Suivant</button>
        </div>
    </div>

    <div class="display-none">
        <div class="popup-header">
            <h2>Authentification à deux facteurs</h2>
        </div>
        <div class="popup-body">
            <div class="popup-content">
                <div class="popup-content-inner">
                    <div class="popup-content-left">
                        <div class="popup-content-left-inner">
                            <h3>Authentification à deux facteurs activée</h3>
                            <p>Vous allez être déconnecté. Veuillez vous reconnecter avec l'authentification à deux facteurs.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="footer"></div>

<!-- Script pour header et footer -->
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script>
    $(function() {
        $("#footer").load("./footer.html");
    });
</script>
<script src="./script.js" ></script>
    </body>
</html>
