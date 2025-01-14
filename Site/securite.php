<?php

    ob_start();

    include "header.php";
    include "../SQL/connection_local.php";

    // On récupère l'id du compte
    $idPro = $_SESSION['idpro'];
    $stmt = $conn->prepare("SELECT idCompte FROM _professionnel WHERE idPro = ?");
    $stmt->execute(array($idPro));
    $idCompte = $stmt->fetch(PDO::FETCH_ASSOC);
    $idCompte = $idCompte['idCompte'];
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
                <form action="./securite.php" method="post">
                    <label for="mdp">Mot de passe actuel</label>
                    <input type="password" id="mdp" name="mdp" required>
                    <br>
                    <label for="mdp1">Nouveau mot de passe</label>
                    <input type="password" id="mdp1" name="mdp1" required>
                    <br>
                    <label for="mdp2">Confirmer le mot de passe</label>
                    <input type="password" id="mdp2" name="mdp2" required>
                    <input type="submit" value="Modifier">
                </form>
            </div>
            
            <div class="securite_div">
                <!-- Clé API dans une zone flouté and fait un appelle à la bdd pour la récupérer -->
                    <h2>Clé API</h2>
                    <script>

                    </script>
                    <div class="cleapi">
                        <p></p>
                    </div>
                    <?php
                        // Vérifie que la clé API n'a jamais été révélée
                        $stmt = $conn->prepare("SELECT chat_cledevoile FROM _compte WHERE idcompte = ?");
                        $stmt->bindParam(1, $idCompte['idCompte']);
                        $chat_cledevoile = $stmt->fetch(PDO::FETCH_ASSOC);
                        if($chat_cledevoile['chat_cledevoile'] == false){
                            echo "<button id='btn_cleapi' onclick='afficher_cleapi()'>Afficher la clé API</button>";
                        } else {
                            echo "La clé API a déjà été révélée, veuillez la regénérer si vous la perdez.";
                        }
                    ?>
                    <button id="btn_regenerer" onclick="regenerer_cleapi()">Regénérer la clé API</button>
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
