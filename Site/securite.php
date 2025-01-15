<?php

    ob_start();

    include "header.php";
    include "../SQL/connection_local.php";
    include "apikeygen.php";

    // Vérifie qu'un utilisateur est connecté
    if (!isset($_SESSION['membre']) && !isset($_SESSION['professionnel'])) {
        header("Location: ./connexion_membre.php");
        exit();
    }

    // On récupère l'id du compte
    $idPro = $_SESSION['idpro'];
    $stmt = $conn->prepare("SELECT idcompte FROM _professionnel WHERE idpro = ?");
    $stmt->bindParam(1, $idPro, PDO::PARAM_INT);
    $stmt->execute();
    $idCompte = $stmt->fetch(PDO::FETCH_ASSOC)['idcompte'];

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
        
        <main class = "securite_main">
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

            </script>

            </div>
            <div class="securite_div <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>">
                <h2>Clé API</h2>
                <script>
                    // Fonction pour regénérer la clé API
                    function regenerer_cleapi() {
                        var cleapi = "<?php echo generateAPIKey($typecompte, $conn); ?>";
                        document.querySelector(".cleapi p").innerHTML = 'xxx-xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx';
                        document.querySelector(".cleapi").classList.add("floutage");
                        changer_cleapi(cleapi);
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

                <div class="cleapi floutage">
                    <p>xxx-xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx</p>
                </div>
                <button id="btn_regenerer" onclick="regenerer_cleapi()">Regénérer la clé API</button>
                <button id="btn_afficher" onclick="afficher_cleapi()">Afficher la clé API</button>
                <p>Conservez cette clé API en lieu sûr. Elle vous permet d'accéder à l'API de notre site. Une fois générée, vous ne pourrez plus la voir en clair.</p>
                <p>Si vous avez perdu votre clé API, vous pouvez en générer une nouvelle en cliquant sur le bouton "Regénérer la clé API".</p>
            </div>

        </main>
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
