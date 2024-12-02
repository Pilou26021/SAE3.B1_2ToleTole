<?php
    include "header.php";
    ob_start();
    include "../SQL/connection_local.php";

    $professionel = false;
    $membre = false;
    if (isset($_SESSION['membre'])) {
        $membre = true;
        $idcompte = $_SESSION['membre'];
    } elseif (isset($_SESSION['professionnel'])) {
        $professionel = true;
        $idcompte = $_SESSION['professionnel'];
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link rel="stylesheet" href="./style.css">   
    <title>Mon Compte</title>

        <style>
            main {
                background-color: #F2F1E9;
                display: flex;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                width: 100%;
                margin-bottom: 40px;
            }

            .mes_infos_main_zone_gauche {
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            margin-left: 5%;
            width: 60%;
            padding: 20px;

            }

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
                padding: 10px 20px;
                color: black;
                text-decoration: none;
                font-size: 15px;
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
                margin-top: 30px;
                
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
                margin: 30px;        
                flex: 1;   
            }

        @media (max-width: 1200px){

            .mes_infos_ligne {
                flex-direction: column;
            }

            .mes_infos_main_zone_gauche{
                margin: 0;
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

    <!-- Récupération des informations depuis la base de données -->
    <?php

                if (isset($_SESSION['professionnel'])){
                    $userID = $_SESSION['professionnel'];

                    $sql = "SELECT * FROM _compte c JOIN _professionnel p
                            ON c.idcompte = p.idcompte
                            WHERE c.idcompte = :idcompte";

                    $stmt = $conn->prepare($sql);
                    $stmt->bindValue(':idcompte', $userID, PDO::PARAM_INT);
                }
                else {
                    $userID = $_SESSION['membre'];

                    $sql = "SELECT * FROM _compte c
                            WHERE idcompte = :idcompte";

                    $stmt = $conn->prepare($sql);
                    $stmt->bindValue(':idcompte', $userID, PDO::PARAM_INT);
                }

                $stmt->execute();
                $infos_compte = $stmt->fetch();
    ?>

</head>
    <body>

        <!-- Flèche de retour à l'accueil -->
        <div style=" position:sticky; top:20px; left:20px; width: 100%;">
                <a style="text-decoration: none; font-size: 30px; color: #040316; cursor: pointer;" href="./index.php">&#8617;</a>
                <!-- onclick="history.back(); -->
        </div>
        
        <main>
                <section class="mes_infos_main_zone_gauche">

                    <div class="mes_infos_conteneur">
                        <h3 class="mes_infos_titre">Modification de vos informations personnelles</h3>

                        <form class="mes_infos_form" id="infoForm" action="modifier_infos.php" method="POST">

                            <!-- Première ligne -->
                            <div class="mes_infos_ligne">
                                <div class="mes_infos_colonne">
                                    <label for="nom">Nom</label>
                                    <input type="text" id="nom" name="nom" value="<?php echo $infos_compte['nomcompte'] ?>" required readonly>
                                </div> 
                                <div class="mes_infos_colonne">
                                    <label for="prenom">Prénom</label>
                                    <input type="text" id="prenom" name="prenom" value="<?php echo $infos_compte['prenomcompte'] ?>" required readonly>
                                </div>
                            </div>

                            <!-- Deuxième ligne -->
                            <div class="mes_infos_ligne">
                                <div class="mes_infos_colonne">
                                    <label for="telephone">Téléphone</label>
                                    <input type="tel" id="tel" name="telephone" value="<?php echo $infos_compte['numtelcompte'] ?>" required readonly>
                                </div>
                                <div class="mes_infos_colonne">
                                    <label for="email">Email</label>
                                    <input type="text" id="mail" name="email" value="<?php echo $infos_compte['mailcompte'] ?>" required readonly>
                                </div>
                            </div>

                            <?php
                                //On s'adapte en fonction du profil
                                if (isset($_SESSION['professionnel'])){ ?>
                                
                                <!-- Troisième ligne, si professionnel -->
                                <div class="mes_infos_ligne">
                                    <div class="mes_infos_colonne">
                                        <label for="denominationpro">Dénomination professionnelle</label>
                                        <input type="text" id="denomination" name="denominationpro" value="<?php echo $infos_compte['denominationpro'] ?>" required readonly>
                                    </div>
                                    <div class="mes_infos_colonne">
                                        <label for="numsiren">Numéro de siren</label>
                                        <input type="text" id="numsiren" name="numsiren" value="<?php echo $infos_compte['numsirenpro'] ?>" required readonly>
                                    </div>
                                </div>
                            <?php } ?>
                                    
                            <button type="button" class="liens-boutons" id="mes_infos_toggleButton">Modifier mes informations personnelles</button>
                            <p id="message_erreur"></p>
                        </form>
                    </div>
                </section>

                <section class="mes_infos_main_zone_droite">
                        <a class="liens-boutons" id="lien_page" href="mes_infos.php">Gérer mes informations personnelles</a>
                        <a class="liens-boutons" href="">Gérer mon mot de passe</a>
                        <a class="liens-boutons" href="">Gérer mon coordonnées bancaires</a>
                        <a class="liens-boutons" href="">Consulter mes offres</a>
                        <a class="liens-boutons" href="">Consulter les signalements</a>
                        <a class="liens-boutons" href="">Ajouter une offre</a>
                        <a class="liens-boutons" href="">Mes factures</a>  
                        <a class="liens-boutons" href="">Supprimer mon compte</a>
                </section>
            
        </main>
        <div id="footer"></div>

        <!-- Script pour le bouton de modification des informations personnelles -->
        <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const form = document.getElementById('infoForm');
                    const toggleButton = document.getElementById('mes_infos_toggleButton');
                    const inputs = form.querySelectorAll('input');
                    let isEditing = false;
                    var pro_valide = true;

                    toggleButton.addEventListener('click', () => {
                        const messageErreur = document.getElementById('message_erreur');
                        messageErreur.style.color = "red"; 

                        if (isEditing) {
                            const phoneNumber = document.getElementById('tel').value;
                            const email = document.getElementById('mail').value;

                            // Ici, indirectement, on vérifie si l'utilisateur est un pro
                            if ((document.getElementById('numsiren')) !== null){
                                const numsirenP = document.getElementById('numsiren').value;

                                const regexSiren = /^[0-9]{9}$/;

                                if (!regexSiren.test(numsirenP)) {
                                        if (messageErreur) {
                                            messageErreur.textContent = "Veuillez renseigner un numéro de siren valide";
                                            pro_valide = false;
                                        }
                                    }
                            }

                            const regex = /^[0-9]{10}$/;
                            const regexEmail = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

                            if (!regex.test(phoneNumber)) {
                                if (messageErreur) {
                                    messageErreur.textContent = "Le numéro de téléphone doit comporter exactement 10 chiffres.";
                                }
                            }

                            else if (!regexEmail.test(email)) {
                                // Si l'email est valide, on efface le message d'erreur
                                if (messageErreur) {
                                    messageErreur.textContent = 'Veuillez renseigner une adresse E-mail valide';
                                }
                            }

                            else if (pro_valide == true){
                                inputs.forEach(input => input.setAttribute('readonly', 'readonly'));
                                form.submit();  // Soumettre le formulaire si tout est valide
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
