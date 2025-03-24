<style>
    .modale {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(5px);
        align-items: center;
        justify-content: center;
    }
    #modale-content {
        background: #f5f5e9;
        border: 2px solid red;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        min-width: 300px;
    }
    .modale button {
        margin: 10px;
        padding: 10px 20px;
        border: 1px solid red;
        cursor: pointer;
    }

    #ouvrirModalSuppression{
        border-color: red;
    }

    #ouvrirModalSuppression:hover{
        background-color: red;
    }
    
                .conteneur-confirmation{
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    flex-direction: column;
                }
                .warning {
            font-size: 50px;
            color: red;
            text-align: center;
            background-color: #F2F1E9;
            border: none;
            }

            .loader {
            margin-bottom: 10px;
            border: 8px solid #F2F1E9; /* Couleur du fond */
            border-top: 8px solid red; /* Couleur de l'animation */
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 2s linear infinite; /* Animation pour faire tourner le cercle */
            }

            /* Définir l'animation */
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }


</style>


<?php

    ob_start();

    include "header.php";
    include "../SQL/connection_local.php";

    $professionel = false;
    $membre = false;
    if (isset($_SESSION['membre'])) {
        $membre = true;
        $idmembre = $_SESSION['membre'];
        $userID = $_SESSION['membre'];

    } elseif (isset($_SESSION['professionnel'])) {
        $professionel = true;
        $idpro = $_SESSION['professionnel'];
        $userID = $_SESSION['professionnel'];

        // On vérifie si c'est un pro public ou privé
        $sql = "SELECT idpro
                FROM professionnelpublic
                WHERE idpro = :id
                ;";

        // Préparer et exécuter la requête
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $userID, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetchAll();

        foreach($res as $resultat){

            if ($resultat['idpro'] == $userID && $resultat['idpro'] !== 1){
                $_SESSION['typePro'] = "publique";
            }
    
            else{
                $_SESSION['typePro'] = "prive";
            }  
        }

    }
?>


<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <link rel="stylesheet" href="./style.css">   
        <title>Mon Compte</title>
        
    </head>
    <body>

        <!-- Chargement du header et du footer -->
        <script
            src="https://code.jquery.com/jquery-3.3.1.js"
            integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
            crossorigin="anonymous">
        </script>
        
        <script> 
            $(function(){ 
                $("#footer").load("footer.html"); 
            });
        </script> 

        <div id="header"></div>

        <!-- Flèche de retour à mon_compte -->
        <div style=" position:sticky; top:20px; left:20px; width: 100%;">
                <a style="text-decoration: none; font-size: 30px; color: #040316; cursor: pointer;" href="./index.php">&#8617;</a>
        </div>
        
        <main class="mc_main">
            <h1>Mon Compte</h1>
            
            <!-- ===========================
               PARTIE PHOTO DE PROFIL
            =========================== -->

            <?php 
                if (isset($_SESSION['membre'])) {
                    $idcompte = $idmembre;

                    $sql = "SELECT pseudonyme 
                            FROM _membre
                            WHERE idcompte = :idcompte";
                    
                    $stmt = $conn->prepare($sql);
                    $stmt->bindValue(':idcompte', $idcompte, PDO::PARAM_INT);
                    $stmt->execute();
                    $pseudo = $stmt->fetch();

                } elseif (isset($_SESSION['professionnel'])) {
                    $idcompte = $idpro;
                }            

                //requete pour récupérer l'image de l'utilisateur
                $sql = "SELECT i.pathimage, c.*, numtelcompte FROM _image i 
                        JOIN _compte c ON i.idimage = c.idimagepdp
                        WHERE idcompte = :idcompte";

                // Préparer et exécuter la requête
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(':idcompte', $idcompte, PDO::PARAM_INT);
                $stmt->execute();

                $res = $stmt->fetchAll();
            ?>

            <div class="image-edit-btn">
                <img src="<?php echo $res[0]['pathimage']; ?>" alt="Photo de profil" style="width: 200px; height: 200px; border-radius: 50%;">
                <!-- Bouton d'édition -->
                <img class="edit-btn" src="./img/icons/edit.svg" alt="Éditer">
            </div>

            <!-- Modale pour l'upload de l'image -->
            <div id="imageModal" class="imageModal">
                <div class="image-modal-content">
                    <span class="close-btn">&times;</span>
                    <h2>Modifier votre photo de profil</h2>
                    <form id="editImageForm" action="upload_profile_pic.php" method="post" enctype="multipart/form-data">
                        <div class="modal-content-btn">
                            <input class="offer-btn <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>" type="file" name="newProfileImage" accept="image/*" required>
                            <div class="modal-footer">
                                <button type="submit" class="offer-btn <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>">Enregistrer</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
            <script>
                $(function() {
                    $("#footer").load("./footer.html");
                });

                // Récupération des éléments modale
                const modal = document.getElementById("imageModal");
                const btn = document.querySelector(".edit-btn");
                const closeBtn = document.querySelector(".close-btn");

                // Ouverture du popup lorsque l'utilisateur clique sur le bouton 
                btn.onclick = function() {
                    modal.style.display = "flex";
                }

                // Fermeture du popup lorsqu'on clique sur le bouton de fermeture
                closeBtn.onclick = function() {
                    modal.style.display = "none";
                }

                // Fermeture du popup lorsqu'on clique en dehors du contenu
                window.onclick = function(event) {
                    if (event.target == modal) {
                        modal.style.display = "none";
                    }
                }

            </script>
            <script src="script.js"></script> 

            <!-- =============================
               PARTIE CENTRALE BOUTONS-LIENS
            ============================= -->

            <?php if (isset($_SESSION['professionnel'])){

                if ($_SESSION['typePro'] == 'publique'){
                    ?><h2><?php echo $res[0]['prenomcompte'] . " " . $res[0]['nomcompte'] ?></h2><?php
                    echo "<p id='afficher_cat'> Professionnel public </p>";
                }
                else{
                    ?><h2><?php echo $res[0]['prenomcompte'] . " " . $res[0]['nomcompte'] ?></h2><?php
                    echo "<p id='afficher_cat'> Professionnel privé </p>";
                }
            } else{
                    ?><h2><?php echo $res[0]['prenomcompte'] . " " . $res[0]['nomcompte'] . " | " . $pseudo['pseudonyme'] ?></h2><?php
                    echo "<p id='afficher_cat'> Membre </p>";
            }
            ?>

            <section>
                <!-- Gestion de l'affichage de la date -->
                <?php
                $dateCreation = $res[0]['datecreationcompte'];
                $dateConnexion = $res[0]['datederniereconnexioncompte'];

            
                // Convertir la date en timestamp
                $timestampCreation = strtotime($dateCreation);
                $timestampConnexion = strtotime($dateConnexion);

                $mois_francais = [
                    '01' => 'janvier', '02' => 'février', '03' => 'mars', '04' => 'avril',
                    '05' => 'mai', '06' => 'juin', '07' => 'juillet', '08' => 'août',
                    '09' => 'septembre', '10' => 'octobre', '11' => 'novembre', '12' => 'décembre'
                ];
            
                $jourCrea = date('d', $timestampCreation);
                $moisCrea = $mois_francais[date('m', $timestampCreation)];
                $anneeCrea = date('Y', $timestampCreation);

                $jourCo = date('d', $timestampConnexion);
                $moisCo = $mois_francais[date('m', $timestampConnexion)];
                $anneeCo = date('Y', $timestampConnexion);
            
                ?>

                <p class="p-moncompte">Vous êtes inscris depuis le <?php echo $jourCrea . " " . $moisCrea . " " . $anneeCrea . " et votre dernière connexion remonte au " . $jourCo . " " . $moisCo . " " . $anneeCo?></p>
            </section>

            <?php
            // Affichage du professionnel
            if (isset($_SESSION['professionnel'])){ ?>
                <?php if ($_SESSION['typePro'] == 'prive'){ ?>
                    <!-- Conteneur des boutons-liens -->
                    <div class="conteneur-boutons">
                        <section class="creer_ligne">
                            <!-- <div class="creer_colonne conteneur-gauche"> -->
                                <a class="liens-boutons <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>" href="mes_infos.php">Gérer mes informations personnelles</a>
                                <!-- <a class="liens-boutons" href="">Gérer mon mot de passe</a> -->
                                <a class="liens-boutons <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>" href="mes_infos_bancaires.php">Gérer mes coordonnées bancaires</a>
                            <!-- </div> -->

                            <!-- <div class="creer_colonne conteneur-droit"> -->
                                <!-- <a class="liens-boutons" href="">Consulter mes offres</a>
                                <a class="liens-boutons" href="">Consulter les signalements</a>
                                <a class="liens-boutons" href="">Ajouter une offre</a>   -->
                            <!-- </div> -->
                        </section>
                        <section class="creer_ligne">
                            <a class="liens-boutons <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>" href="avis_mes_offres.php">Consulter les avis sur mes offres</a>
                            <a class="liens-boutons <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>" href="securite.php">Gérer mon mot de passe</a> 
                        </section>

                        <!-- <a class="liens-boutons" href="">Mes factures</a>  
                        <a class="liens-boutons" href="">Supprimer mon compte</a> -->
                    </div>
                <?php } ?>

                <?php if ($_SESSION['typePro'] == 'publique'){ ?>
                    <!-- Conteneur des boutons-liens -->
                    <div class="conteneur-boutons">
                        <section class="creer_ligne">
                            <!-- <div class="creer_colonne conteneur-gauche"> -->
                                <a class="liens-boutons <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>" href="mes_infos.php">Gérer mes informations personnelles</a>
                                <!-- <a class="liens-boutons" href="">Gérer mon mot de passe</a> -->
                            <!-- </div> -->

                            <!-- <div class="creer_colonne conteneur-droit"> -->
                                <!-- <a class="liens-boutons" href="">Consulter mes offres</a>
                                <a class="liens-boutons" href="">Ajouter une offre</a>   -->
                            <!-- </div> -->
                        </section>
                        <section class="creer_ligne">
                            <a class="liens-boutons <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>" href="avis_mes_offres.php">Consulter les avis sur mes offres</a>
                            <a class="liens-boutons <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>" href="securite.php">Gérer mon mot de passe</a> 
                        </section>


                        <!-- <a class="liens-boutons" href="">Consulter les signalements</a>
                        <a class="liens-boutons" href="">Supprimer mon compte</a> -->
                    </div>
                <?php } ?>
            <?php 
            // Affichage du membre
            } else { ?>
                <!-- Conteneur des boutons-liens -->
                <div class="conteneur-boutons">
                    <section class="creer_ligne">
                        <!-- <div class="creer_colonne conteneur-gauche"> -->
                            <a class="liens-boutons" href="mes_infos.php">Gérer mes informations personnelles</a>
                            <a class="liens-boutons" href="securite.php">Gérer mon mot de passe</a> 
                            <!-- <a class="liens-boutons" href="">Gérer mon mot de passe</a> -->
                        <!-- </div> -->
                        <!-- <div class="creer_colonne conteneur-droit"> -->
                            <!-- <a class="liens-boutons" href="">Consulter mes visites</a>
                            <a class="liens-boutons" href="">Aide</a> -->
                        <!-- </div> -->
                    </section>
                    <section>
                        <button id="ouvrirModalSuppression" class="liens-boutons" type="button">Supprimer mon compte</button>
                    </section>

            
                    <!-- <a class="liens-boutons" href="">Supprimer mon compte</a> -->
                </div>
                
            <?php
            } 



            //Autre partie

            




            ?>



            <div id="confirmationModal" class="modale">
                <div id="modale-content">
                    <section id="truc1">
                        <form action="<?=($_SERVER['PHP_SELF'])?>" method="post">
                            <p>Êtes-vous sûr de vouloir supprimer votre compte ?</p>
                            <p>⚠ Cet action est irréversible</p>
                            <input type="password" id="pswInput" name="pswInput">
                            <button id="confirmerOui" type="submit">Oui</button>
                            <button id="confirmerNon">Non</button> 
                            <p id="messageIndication">Message info</p>
                        </form>
                        
                    </section>
                    <section id="redirection">
                        <section class="conteneur-confirmation">
                            <div class="loader"></div>
                            <p style='color: red; font-size: 22px; margin: 0;'> Suppression de votre compte en cours ... <p>
                            <p style='color: red; font-size: 22px; margin: 0;'> Vous allez être redirigé dans 3 secondes <p>

                        </section>
                    </section>
                </div>
            </div>

                
        </main>



        

    <script>
                const modale = document.getElementById("confirmationModal");
                const contenu = document.getElementById("modale-content");
                const contenuBis = document.getElementById("redirection");
                const boutonOuverture = document.getElementById("ouvrirModalSuppression");
                const confirmerOui = document.getElementById("confirmerOui");
                const confirmNo = document.getElementById("confirmNo");
                const getMDP = document.getElementById("divMDP");
                const getInput = document.getElementById("pswInput");
                const msgIndication = document.getElementById("messageIndication");


                // Ouvre la modale
                boutonOuverture.addEventListener("click", () => {
                    event.preventDefault();
                    modale.style.display = "flex";
                    contenuBis.style.display = "none";
                });

                // Si "Oui" est cliqué, affiche dans la console et ferme la modale
             

                    document.getElementById("confirmerOui").addEventListener("click", function(event) {
                    event.preventDefault();
                    
                    let motDePasse = document.getElementById("pswInput").value;



                    let valeur = document.getElementById("pswInput").value;
                    let formData = new FormData();
                    formData.append("texte", valeur);

                    fetch("unTest.php", {
                        method: "POST",
                        body: formData,
                        credentials: "include",
                    })
                    .then(response => response.text()) // Ne récupère que la réponse, pas la page entière
                    .then(data => {
                        msgIndication.innerHTML = data; // Met à jour uniquement le texte
                    })
                    .catch(error => console.error("Erreur :", error));
                    




                    // fetch("verifier_mdp.php", {
                    //     method: "POST",
                    //     headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    //     body: "pswInput=" + encodeURIComponent(motDePasse)
                    // })
                    // .then(response => response.text())
                    // .then(data => {
                    //     if (data.trim() === "oui") {
                    //         contenuBis.style.display = "flex";
                    //         window.location.href = "suppression_compte.php"; // Redirection si succès
                    //     } else {
                    //         msgIndication.innerHTML = 'Nique ta mère';
                    //     }
                    // })
                });


                // Si "Non" est cliqué, ferme simplement la modale
                confirmNo.addEventListener("click", () => {
                    modale.style.display = "none";
                });
    </script>



    
    <div id="footer"></div>
    </body>
</html>
