<?php

    ob_start();

    include "header.php";
    include "../SQL/connection_local.php";

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link rel="stylesheet" href="./style.css">   
    <title>Mes infos</title>

        <style>
            main {
                background-color: #F2F1E9;
                display: flex;
                justify-content: space-between;
                align-items: center;
                width: 100%;
            }

            /* Zone gauche de la page (du main) */
            .mes_infos_main_zone_gauche {
                display: flex;
                flex-direction: row;
                justify-content: center;
                align-items: center;
                margin-left: 5%;
                width: 60%;
                padding: 20px;
            }

            /* Zone droite de la page (du main) */
            .mes_infos_main_zone_droite {
                display: flex;
                flex-direction: column;
                align-items: flex-end;
                box-sizing: border-box;
                margin: 40px;
                width: 30%;
                min-width: 200px;
            }

            /* On montre à l'utilisateur sur quelle page il se trouve */
            #lien_page{
                background-color: #36D673;
            }

            /*======================
             Style des boutons-liens 
             ======================*/

            .liens-boutons {
                width: 90%;
                padding: 15px 15px;
                color: black;
                text-decoration: none;
                font-size: 16px;
                border-radius: 5px;
                text-align: center;
                transition: background-color 0.3s ease;
                background-color: #F2F1E9;
                margin: 10px 0px 10px 0px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                box-sizing: border-box;
                flex-grow: 1;
                min-width: 200px;
            }

            .liens-boutons:hover {
                color: inherit;
                background-color: #36D673;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.6);
            }

            /*===================================================================================
             Style du conteneur central pour la modification des informations de l'utilisateur 
             ===================================================================================*/

            .mes_infos_titre{
                margin-bottom: 30px;
                text-decoration: none;
            }

            .mes_infos_conteneur {
                width: 90%;
                border-radius: 10px;
                
                text-align: center;
                padding: 0;
            }

            .mes_infos_form label {
                font-size: 14px;
                margin-bottom: 5px;
                text-align: left;
            }

            .mes_infos_form input {
                width: 100%;
                padding: 10px;
                border: none;
                border-radius: 5px;
                background-color: #F2F1E9;
                border: 1px solid #31CEA6;
                font-size: 16px;
                box-sizing: border-box;
                text-align: center;
                color: black;
            }

            .mes_infos_form input:focus {
                outline: none;
            }

            #mes_infos_toggleButton {
                font-size: 16px;
                width: 80%;
                border-radius: 5px;
                border: 1px solid #a5d6a7;
                color: black;
                cursor: pointer;
                margin-top: 40px;
                
            }

            #mes_infos_toggleButton:hover {
                background-color: #36D673;
            }

            /* Style des lignes du conteneur */
            .mes_infos_ligne {
                display: flex;
                flex-direction: row;
                justify-content: center;
                max-width: none;
            }

            /* Style des colonnes du conteneur */
            .mes_infos_colonne {
                display: flex;
                flex-direction: column;
                margin: 15px;        
                flex: 1;   
            }

            /* Styles particuliers */
            #colonne_champRue, #colonne_champTel, #colonne_champNumsiren, #colonne_champCP{
                flex: 0;
            }

            #champRue{
                width: 100px;
            }

            #champTel, #champNumsiren, #champCP{
                width: 200px;
            }


        @media (max-width: 1200px){

            .mes_infos_ligne {
                flex-direction: column;
            }

            .mes_infos_main_zone_gauche{
                margin: 0;
            }

            .liens-boutons {
                font-size: 15px;
                padding: 20px 20px;
                width: 100%;
            }
        }

        @media (max-width: 700px){

            main{
                flex-direction: column;
            }

            .mes_infos_conteneur{
                width: 100%;
            }

            .mes_infos_form{
                width: 100%;
            }

            .mes_infos_main_zone_gauche{
                width: 90%;
            }

            .mes_infos_main_zone_droite{
                width: 90%;
            }

            .mes_infos_lignes{
                width: 100%;
            }

            .mes_infos_colonnes{
                margin: 30px 0 30px 0;
            }

            .liens-boutons {
                font-size: 15px;
                padding: 20px 20px;
                width: 100%;
            }
        }

    </style>

</head>
    <body>
        <?php

            // Récupération des informations de l'utilisateur depuis la base de données
            if (isset($_SESSION['professionnel'])){
                $userID = $_SESSION['professionnel'];

                $sql = "SELECT * 
                        FROM _compte c 
                        JOIN _professionnel p ON c.idcompte = p.idcompte
                        JOIN _adresse a ON c.idadresse = a.idadresse
                        WHERE c.idcompte = :idcompte";

                // Préparation
                $stmt = $conn->prepare($sql);
                
                // Liaison
                $stmt->bindValue(':idcompte', $userID, PDO::PARAM_INT);
            }
            else {
                $userID = $_SESSION['membre'];

                $sql = "SELECT * 
                        FROM _compte c
                        JOIN _membre m ON c.idcompte = m.idcompte
                        JOIN _adresse a ON c.idadresse = a.idadresse
                        WHERE c.idcompte = :idcompte";

                // Préparation
                $stmt = $conn->prepare($sql);

                // Liaison
                $stmt->bindValue(':idcompte', $userID, PDO::PARAM_INT);
            }

            // Exécution
            $stmt->execute();
            $infos_compte = $stmt->fetch();
        ?>
        <!-- Flèche de retour à la page mon_compte -->
        <div style=" position:sticky; top:20px; left:20px; width: 100%;">
                <a style="text-decoration: none; font-size: 30px; color: #040316; cursor: pointer;" href="./mon_compte.php">&#8617;</a>
        </div>
        
        <main>
                <!-- Zone gauche de la page (Le conteneur avec les informations de l'utilisateur) -->
                <section class="mes_infos_main_zone_gauche">

                    <div class="mes_infos_conteneur">

                        <h3 class="mes_infos_titre">Mes informations personnelles</h3>

                        <form class="mes_infos_form" id="infoForm" action="modifier_infos.php" method="POST">

                            <!-- Première ligne SI membre -->
                            <?php if (isset($_SESSION['membre'])){ ?>

                                    <!-- Pseudo -->
                                    <div class="mes_infos_ligne">
                                        <div class="mes_infos_colonne">
                                            <label for="champPseudo">Pseudonyme</label>
                                            <input type="text" id="champPseudo" name="pseudo" value="<?php echo $infos_compte['pseudonyme'] ?>" required readonly>
                                        </div> 
                                    </div>
                                
                            <?php } ?>

                            <!-- Première ligne sinon -->
                            <div class="mes_infos_ligne">

                                <!-- Nom -->
                                <div class="mes_infos_colonne">
                                    <label for="nom">Nom</label>
                                    <input type="text" id="nom" name="nom" value="<?php echo $infos_compte['nomcompte'] ?>" required readonly>
                                </div> 

                                <!-- Prénom -->
                                <div class="mes_infos_colonne">
                                    <label for="prenom">Prénom</label>
                                    <input type="text" id="prenom" name="prenom" value="<?php echo $infos_compte['prenomcompte'] ?>" required readonly>
                                </div>
                            </div>

                            <!-- Deuxième ligne -->
                            <div class="mes_infos_ligne">

                                <!-- Téléphone -->
                                <div id="colonne_champTel" class="mes_infos_colonne">
                                    <label for="telephone">Téléphone</label>
                                    <input type="tel" id="champTel" name="telephone" value="<?php echo $infos_compte['numtelcompte'] ?>" required readonly>
                                </div>

                                <!-- Email -->
                                <div class="mes_infos_colonne">
                                    <label for="email">Email</label>
                                    <input type="text" id="mail" name="email" value="<?php echo $infos_compte['mailcompte'] ?>" required readonly>
                                </div>
                            </div>

                            <!-- Troisième ligne -->
                            <div class="mes_infos_ligne">

                                <!-- Ville -->
                                <div class="mes_infos_colonne">
                                    <label for="ville">Ville</label>
                                    <input type="text" id="champVille" name="ville" value="<?php echo $infos_compte['ville'] ?>" required readonly>
                                </div>

                                <!-- Code Postal -->
                                <div id ="colonne_champCP" class="mes_infos_colonne">
                                    <label for="cp">Code Postal</label>
                                    <input type="text" id="champCP" name="cp" value="<?php echo $infos_compte['codepostal'] ?>" required readonly>
                                </div>
                            </div>

                            <!-- Quatrième ligne -->
                            <div class="mes_infos_ligne">

                                <!-- Numéro de rue -->
                                <div id="colonne_champRue" class="mes_infos_colonne">
                                    <label for="rue">N° de rue</label>
                                    <input type="text" id="champRue" name="rue" value="<?php echo $infos_compte['numrue'] ?>" required readonly>
                                </div>

                                <!-- Adresse -->
                                <div class="mes_infos_colonne">
                                    <label for="adresse">Adresse</label>
                                    <input type="text" id="champAdresse" name="adresse" value="<?php echo $infos_compte['adresse'] ?>" required readonly>
                                </div>

                                <!-- Supplément d'adresse -->
                                <div class="mes_infos_colonne">
                                    <label for="supAdresse">Supplément d'adresse (facultatif)</label>
                                    <input type="text" id="champSupAdresse" name="supAdresse" value="<?php echo $infos_compte['supplementadresse'] ?>" readonly>
                                </div>

                            </div>

                            <?php
                                if (isset($_SESSION['professionnel'])){ ?>
                                
                                <!-- Autre ligne, si professionnel -->
                                <div class="mes_infos_ligne">

                                    <!-- Dénomination sociale -->
                                    <div class="mes_infos_colonne">
                                        <label for="denominationpro">Dénomination professionnelle</label>
                                        <input type="text" id="denomination" name="denominationpro" value="<?php echo $infos_compte['denominationpro'] ?>" required readonly>
                                    </div>
                                    
                                    <?php if ($_SESSION['typePro'] == 'prive'){ ?>
                                        <!-- Numéro de siren -->
                                        <div id="colonne_champNumsiren" class="mes_infos_colonne">
                                            <label for="numsiren">N° de siren</label>
                                            <input type="text" id="champNumsiren" name="numsiren" value="<?php echo $infos_compte['numsirenpro'] ?>" required readonly>
                                        </div>
                                    <?php } ?>

                                </div>

                            <?php } ?>
                            
                            <!-- Bouton pour modifier ses informations personnelles -->
                            <button type="button" class="liens-boutons" id="mes_infos_toggleButton">Modifier mes informations personnelles</button>

                                

                            <!-- Message d'erreur qui n'apparait pas tant qu'il n'y a pas d'erreur -->
                            <p id="message_erreur"></p>
                        </form>
                    </div>
                </section>
                
                <a style="width=10px;" href="mon_compte.php" class="offer-btn">Retour à mon compte</a>

                <!-- Zone droite de la page (Liens-boutons de navigation) -->
                <section class="mes_infos_main_zone_droite">
                    <?php
                        if (isset($_SESSION['professionnel'])){ ?>

                            <!-- <a id="lien_page" class="liens-boutons" href="mes_infos.php">Gérer mes informations personnelles</a> -->
                            <!-- <a class="liens-boutons" href="">Gérer mon mot de passe</a> -->

                            <?php if ($_SESSION['typePro'] == 'prive'){ ?>
                                <a class="liens-boutons" href="mes_infos_bancaires.php">Gérer mon coordonnées bancaires</a>
                            <?php } ?>

                            <!-- <a class="liens-boutons" href="">Consulter mes offres</a>
                            <a class="liens-boutons" href="">Consulter les signalements</a>
                            <a class="liens-boutons" href="">Ajouter une offre</a>

                            <?php if ($_SESSION['typePro'] == 'prive'){ ?>
                                <a class="liens-boutons" href="">Mes factures</a>  
                            <?php } ?> 

                            <a class="liens-boutons" href="">Supprimer mon compte</a> -->

                    <?php
                    // Affichage point de vue membre
                    } else { ?>
                            <!-- <a id="lien_page" class="liens-boutons" href="mes_infos.php">Gérer mes informations personnelles</a> -->
                            <!-- <a class="liens-boutons" href="">Gérer mon mot de passe</a>
                            <a class="liens-boutons" href="">Consulter mes visites</a>
                            <a class="liens-boutons" href="">Aide</a>
                            <a class="liens-boutons" href="">Supprimer mon compte</a> -->
                    <?php
                    } ?>
                </section>
            
        </main>
        <div id="footer"></div>

        <!-- Script pour le bouton de modification des informations personnelles -->
        <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const form = document.getElementById('infoForm');
                    const toggleButton = document.getElementById('mes_infos_toggleButton');
                    const inputs = form.querySelectorAll('input');
                    const messageErreur = document.getElementById('message_erreur');
                    messageErreur.style.color = "red"; 
                    let isEditing = false;

                    // Action quand le bouton de modification est cliqué
                    toggleButton.addEventListener('click', () => {
                        if (isEditing) {

                            // On récupère les champs à vérifier
                            const phoneNumber = document.getElementById('champTel').value;
                            const email = document.getElementById('mail').value;
                            const rue = document.getElementById('champRue').value;
                            const cp = document.getElementById('champCP').value;

                            var pro_valide = true;
                            

                            // Ici, indirectement, on vérifie si l'utilisateur est un pro
                            if ((document.getElementById('champNumsiren')) !== null){
                                const numsirenP = document.getElementById('champNumsiren').value;

                                const regexSiren = /^\d{9}$/;

                                if (!regexSiren.test(numsirenP)) {
                                        if (messageErreur) {
                                            messageErreur.textContent = "Veuillez renseigner un numéro de siren valide";
                                            pro_valide = false;
                                        }
                                    }
                            }
                            
                            // Constantes regex pour nous permettre de vérifier les champs répérés plus tôt
                            const regex = /^[0-9]{10}$/;
                            const regexEmail = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
                            const regexRue = /^\d+$/
                            const regexCP = /^\d{5}$/;

                            // Test du numéro de téléphone
                            if (!regex.test(phoneNumber)) {
                                if (messageErreur) {
                                    messageErreur.textContent = "Le numéro de téléphone doit comporter exactement 10 chiffres.";
                                }
                            }

                            // Test de l'email
                            else if (!regexEmail.test(email)) {
                                if (messageErreur) {
                                    messageErreur.textContent = 'Veuillez renseigner une adresse E-mail valide';
                                }
                            }

                            // Test du numéro de rue
                            else if (!regexRue.test(rue)) {
                                if (messageErreur) {
                                    messageErreur.textContent = 'Veuillez renseigner un numéro de rue valide';
                                }
                            }

                            // Test du code postal
                            else if (!regexCP.test(cp)) {
                                if (messageErreur) {
                                    messageErreur.textContent = 'Le code postal doit faire exactement 5 chiffres';
                                }
                            }

                            // Si tout est bon on peut soumettre le formulaire
                            else if (pro_valide == true){
                                inputs.forEach(input => input.setAttribute('readonly', 'readonly'));
                                form.submit();
                            }


                        } else {
                            inputs.forEach(input => input.removeAttribute('readonly'));
                            inputs.forEach(input => input.style.color = "#31CEA6");
                            toggleButton.textContent = 'Enregistrer les modifications';
                        }
                        isEditing = !isEditing;
                    });
                });

        </script>

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
