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
        
        <main class = "mes_infos_main">
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
                                            <label for="MI_champPseudo">Pseudonyme</label>
                                            <input type="text" id="MI_champPseudo" name="pseudo" value="<?php echo $infos_compte['pseudonyme'] ?>" required readonly>
                                        </div> 
                                    </div>
                                
                            <?php } ?>

                            <!-- Première ligne sinon -->
                            <div class="mes_infos_ligne">

                                <!-- Nom -->
                                <div class="mes_infos_colonne">
                                    <label for="MI_champNom">Nom</label>
                                    <input type="text" id="MI_champNom" name="nom" value="<?php echo $infos_compte['nomcompte'] ?>" required readonly>
                                </div> 

                                <!-- Prénom -->
                                <div class="mes_infos_colonne">
                                    <label for="MI_champPrenom">Prénom</label>
                                    <input type="text" id="MI_champPrenom" name="prenom" value="<?php echo $infos_compte['prenomcompte'] ?>" required readonly>
                                </div>
                            </div>

                            <!-- Deuxième ligne -->
                            <div class="mes_infos_ligne">

                                <!-- Téléphone -->
                                <div id="MI_colonne_champTel" class="mes_infos_colonne">
                                    <label for="MI_champTel">Téléphone</label>
                                    <input type="tel" id="MI_champTel" name="telephone" value="<?php echo $infos_compte['numtelcompte'] ?>" required readonly>
                                </div>

                                <!-- Email -->
                                <div class="mes_infos_colonne">
                                    <label for="MI_champEmail">Email</label>
                                    <input type="text" id="MI_champEmail" name="email" value="<?php echo $infos_compte['mailcompte'] ?>" required readonly>
                                </div>
                            </div>

                            <!-- Troisième ligne -->
                            <div class="mes_infos_ligne">

                                <!-- Ville -->
                                <div class="mes_infos_colonne">
                                    <label for="MI_champVille">Ville</label>
                                    <input type="text" id="MI_champVille" name="ville" value="<?php echo $infos_compte['ville'] ?>" required readonly>
                                </div>

                                <!-- Code Postal -->
                                <div id ="MI_colonne_champCP" class="mes_infos_colonne">
                                    <label for="MI_champCP">Code Postal</label>
                                    <input type="text" id="MI_champCP" name="cp" value="<?php echo $infos_compte['codepostal'] ?>" required readonly>
                                </div>
                            </div>

                            <!-- Quatrième ligne -->
                            <div class="mes_infos_ligne">

                                <!-- Numéro de rue -->
                                <div id="MI_colonne_champRue" class="mes_infos_colonne">
                                    <label for="MI_champRue">N° de rue</label>
                                    <input type="text" id="MI_champRue" name="rue" value="<?php echo $infos_compte['numrue'] ?>" required readonly>
                                </div>

                                <!-- Adresse -->
                                <div class="mes_infos_colonne">
                                    <label for="MI_champAdresse">Adresse</label>
                                    <input type="text" id="MI_champAdresse" name="adresse" value="<?php echo $infos_compte['adresse'] ?>" required readonly>
                                </div>

                                <!-- Supplément d'adresse -->
                                <div class="mes_infos_colonne">
                                    <label for="MI_champSupAdresse">Supplément d'adresse (facultatif)</label>
                                    <input type="text" id="MI_champSupAdresse" name="supAdresse" value="<?php echo $infos_compte['supplementadresse'] ?>" readonly>
                                </div>

                            </div>

                            <?php
                                if (isset($_SESSION['professionnel'])){ ?>
                                
                                <!-- Autre ligne, si professionnel -->
                                <div class="mes_infos_ligne">

                                    <!-- Dénomination sociale -->
                                    <div class="mes_infos_colonne">
                                        <label for="MI_champDenominationPro">Dénomination professionnelle</label>
                                        <input type="text" id="MI_champDenominationPro" name="denominationpro" value="<?php echo $infos_compte['denominationpro'] ?>" required readonly>
                                    </div>
                                    
                                    <?php if ($_SESSION['typePro'] == 'prive'){ ?>
                                        <!-- Numéro de siren -->
                                        <div id="MI_colonne_champNumsiren" class="mes_infos_colonne">
                                            <label for="MI_champNumSiren">N° de siren</label>
                                            <input type="text" id="MI_champNumSiren" name="numsiren" value="<?php echo $infos_compte['numsirenpro'] ?>" required readonly>
                                        </div>
                                    <?php } ?>

                                </div>

                            <?php } ?>
                            
                            <!-- Bouton pour modifier ses informations personnelles -->
                            <button type="button" class="mes_infos_liens-boutons" id="mes_infos_toggleButton">Modifier mes informations personnelles</button>

                            <!-- Message d'erreur qui n'apparait pas tant qu'il n'y a pas d'erreur -->
                            <p id="message_erreur"></p>
                        </form>
                    </div>
                </section>

                <!-- Zone droite de la page (Liens-boutons de navigation) -->
                <section class="mes_infos_main_zone_droite" style="display:none;">
                    <?php
                        if (isset($_SESSION['professionnel'])){ ?>

                            <!-- <a id="lien_page" class="liens-boutons" href="mes_infos.php">Gérer mes informations personnelles</a>-->
                            <!-- <a class="liens-boutons" href="">Gérer mon mot de passe</a> -->

                            <?php if ($_SESSION['typePro'] == 'prive'){ ?>
                                <!--<a class="liens-boutons" href="mes_infos_bancaires.php">Gérer mes coordonnées bancaires</a>-->
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

        <!-- Script pour la vérification des informations personnelles -->
        <script>
                document.addEventListener("DOMContentLoaded", function() {

                    const form = document.getElementById('infoForm');
                    const inputs = form.querySelectorAll('input');
                    const toggleButton = document.getElementById('mes_infos_toggleButton');
                    const messageErreur = document.getElementById('message_erreur');
                    messageErreur.style.color = "red"; 
                    let isEditing = false;
                    let valide = true;

                    //////////////////////
                    // Vérification du nom
                    //////////////////////

                    const ChampNomJS = document.getElementById('MI_champNom');

                    ChampNomJS.addEventListener('input', () => {
                        if (ChampNomJS.value.trim() === ""){
                            messageErreur.textContent = 'Le nom ne peut être vide';
                            valide = false;
                        }
                        else{
                            messageErreur.textContent = '';
                            valide = true;
                        }
                    });

                    /////////////////////////
                    // Vérification du prenom
                    /////////////////////////

                    const ChampPrenomJS = document.getElementById('MI_champPrenom');

                    ChampPrenomJS.addEventListener('input', () => {
                        if (ChampPrenomJS.value.trim() === ""){
                            messageErreur.textContent = 'Le prenom ne peut être vide';
                            valide = false;
                        }
                        else{
                            messageErreur.textContent = '';
                            valide = true;
                        }
                    });

                    //////////////////////////////////////
                    // Vérification du numéro de telephone
                    //////////////////////////////////////

                    const ChampNumJS = document.getElementById('MI_champTel');
                    const regex = /^[0-9]{10}$/;

                    ChampNumJS.addEventListener('input', () => {
                        if (!regex.test(ChampNumJS.value)) {
                                messageErreur.textContent = "Le numéro de téléphone doit comporter exactement 10 chiffres.";
                                valide = false;
                        }
                        else {
                                messageErreur.textContent = "";
                                valide = true;
                        }
                    });

                    //////////////////////////
                    // Vérification de l'Email
                    //////////////////////////

                    const ChampEmailJS = document.getElementById('MI_champEmail');
                    const regexEmail = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

                    ChampEmailJS.addEventListener('input', () => {
                        if (!regexEmail.test(ChampEmailJS.value)) {
                                messageErreur.textContent = 'Veuillez renseigner une adresse E-mail valide';
                                valide = false;
                        }
                        else {
                                messageErreur.textContent = "";
                                valide = true;
                        }
                    });

                    ///////////////////////////
                    // Vérification de la ville
                    ///////////////////////////

                    const ChampVilleJS = document.getElementById('MI_champVille');

                    ChampVilleJS.addEventListener('input', () => {
                        if (ChampVilleJS.value.trim() === '') {
                                messageErreur.textContent = 'Le nom de la ville ne peut être vide';
                                valide = false;
                        }
                        else {
                                messageErreur.textContent = "";
                                valide = true;
                        }
                    });

                    /////////////////////////
                    // Vérification de la rue
                    /////////////////////////

                    const ChampRueJS = document.getElementById('MI_champRue');
                    const regexRue = /^\d+$/;

                    ChampRueJS.addEventListener('input', () => {
                        if (!regexRue.test(ChampRueJS.value)) {
                            messageErreur.textContent = 'Veuillez renseigner un numéro de rue valide';
                            valide = false;
                        }
                        else{
                            messageErreur.textContent = "";
                            valide = true;
                        }
                    });

                    ////////////////////////////
                    // Vérification de l'adresse
                    ////////////////////////////

                    const ChampAdresseJS = document.getElementById('MI_champAdresse');

                    ChampAdresseJS.addEventListener('input', () => {
                        if (ChampAdresseJS.value.trim() === '') {
                            messageErreur.textContent = "L'adresse ne peut être vide";
                            valide = false;
                        }
                        else{
                            messageErreur.textContent = "";
                            valide = true;
                        }
                    });

                    //////////////////////////////
                    // Vérification du code postal
                    //////////////////////////////

                    const ChampCPJS = document.getElementById("MI_champCP");
                    const regexCP = /^\d{5}$/;

                    ChampCPJS.addEventListener('input', () => {
                        if (!regexCP.test(ChampCPJS.value)) {
                            messageErreur.textContent = 'Le code postal doit faire exactement 5 chiffres';
                            valide = false;
                        }
                        else{
                            messageErreur.textContent = "";
                            valide = true;
                        }
                    });

                    ///////////////////////////
                    // Vérification de la siren
                    ///////////////////////////
                    
                    if ((document.getElementById('MI_champNumSiren')) !== null){

                        const ChampSirenJS = document.getElementById('MI_champNumSiren');
                        const regexSiren = /^\d{9}$/;

                        ChampSirenJS.addEventListener('input', () => {
                            if (!regexSiren.test(ChampSirenJS.value)) {
                                messageErreur.textContent = "Veuillez renseigner un numéro de siren valide";
                                valide = false;
                            }
                            else{
                                messageErreur.textContent = "";
                                valide = true;
                            }
                        });
                    }

                    //////////////////////////////////
                    // Vérification de la dénomination
                    //////////////////////////////////
                    
                    if ((document.getElementById('MI_champDenominationPro')) !== null){

                        const ChampDenominationJS = document.getElementById('MI_champDenominationPro');

                        ChampDenominationJS.addEventListener('input', () => {
                            if (ChampDenominationJS.value.trim() === '') {
                                messageErreur.textContent = "La dénomination sociale ne peut être vide";
                                valide = false;
                            }
                            else{
                                messageErreur.textContent = "";
                                valide = true;
                            }
                        });
                    }

                    ///////////////////////
                    // Clique sur le bouton
                    ///////////////////////

                    toggleButton.addEventListener('click', () => {
                        if (isEditing) {

                            if (valide == true){
                                inputs.forEach(input => input.setAttribute('readonly', 'readonly'));
                                form.submit();
                            }

                        } else {
                            inputs.forEach(input => input.removeAttribute('readonly'));
                            inputs.forEach(input => input.style.backgroundColor = "#79AFA6");
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
