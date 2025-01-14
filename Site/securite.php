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
            <div class="securite_div">
                <h2>Modifier le mot de passe</h2>
                <form onsubmit="return verifierMotDePasse()">
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
                    <input type="submit" value="Modifier">
                </form>
                <section class="alerte_mdp">
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
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "./api/changer_mdp.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.send("mdp=" + mdp1 + "&idcompte=" + <?php echo $idCompte; ?>);
                    alert("Mot de passe modifié avec succès.");
                    return true;
                }

                function valideMotDePasse() {
                    var mdp = document.getElementById("mdp").value;
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "./api/valider_mdp.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.send("mdp=" + mdp + "&idcompte=" + <?php echo $idCompte; ?>);
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            if (xhr.responseText == "true") {
                                alert("Mot de passe valide.");
                            } else {
                                alert("Mot de passe invalide.");
                            }
                        }
                    }
                }
            </script>

            </div>
            <div class="securite_div">
                <!-- Clé API dans une zone flouté and fait un appelle à la bdd pour la récupérer -->
                    <h2>Clé API</h2>
                    <script>
                        // Fonction php pour regénérer la clé API function generateAPIKey($typecompte, $conn)
                        function regenerer_cleapi(){
                            var cleapi = "<?php echo generateAPIKey($typecompte, $conn); ?>";
                            // On change la valeur de la variable php
                            // Cache la zone de texte avec du flou
                            document.querySelector(".cleapi p").innerHTML = cleapi;
                            document.querySelector(".cleapi p").style.color = "white";
                            changer_cleapi();
                        }

                        function afficher_cleapi(){
                            // Si la clé n'a pas été dévoilée alors on la dévoile
                            cleapi_devoile(function(cleEstDevoile) {
                                if (cleEstDevoile == 0) {
                                    get_cleapi(function(cleapi) {
                                        document.querySelector(".cleapi p").innerHTML = cleapi;
                                        document.querySelector(".cleapi p").style.color = "black";
                                        devoiler_cleapi();
                                    });
                                } else {
                                    // Texte pour dire de regénérer la clé API
                                    document.querySelector(".cleapi p").innerHTML = "La clé API a déjà été dévoilée";
                                    document.querySelector(".cleapi p").style.color = "black";
                                }
                            });
                        }

                        // Requête pour changer la clé API
                        function changer_cleapi(){
                            var cleapi = document.querySelector(".cleapi p").innerHTML;
                            var xhr = new XMLHttpRequest();
                            xhr.open("POST", "./api/changer_cleapi.php", true);
                            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                            xhr.send("cleapi=" + cleapi + "&idcompte=" + <?php echo $idCompte; ?>);
                        }

                        // Requête pour passer la cle API comme devoiler
                        function devoiler_cleapi(){
                            var cleapi = document.querySelector(".cleapi p").innerHTML;
                            var xhr = new XMLHttpRequest();
                            xhr.open("POST", "./api/devoiler_cleapi.php", true);
                            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                            xhr.send("idcompte=" + <?php echo $idCompte; ?>);
                        }
                        // Requête pour récupérer la clé API
                        function get_cleapi(callback){
                            var xhr = new XMLHttpRequest();
                            xhr.open("POST", "./api/get_cleapi.php", true);
                            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                            xhr.send("idcompte=" + <?php echo $idCompte; ?>);
                            xhr.onreadystatechange = function() {
                                if (xhr.readyState == 4 && xhr.status == 200) {
                                    callback(xhr.responseText);
                                }
                            }
                        }

                        // Clé dévoilé ?
                        function cleapi_devoile(callback){
                            var xhr = new XMLHttpRequest();
                            xhr.open("POST", "./api/cleapi_devoile.php", true);
                            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                            xhr.send("idcompte=" + <?php echo $idCompte; ?>);
                            xhr.onreadystatechange = function() {
                                if (xhr.readyState == 4 && xhr.status == 200) {
                                    callback(xhr.responseText);
                                }
                            }
                        }
                    </script>
                    <p><?php echo $cleapi; ?></p>

                    <div class="cleapi">
                        <p></p>
                    </div>
                    <button id="btn_regenerer" onclick="regenerer_cleapi()">Regénérer la clé API</button>
                    <button id="btn_afficher" onclick="afficher_cleapi()">Afficher la clé API</button>
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
