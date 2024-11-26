<?php
    include "header.php";
    ob_start();
    include "../SQL/connection_local.php";

    $professionel = false;
    $membre = false;
    if (isset($_SESSION['membre'])) {
        $membre = true;
        $idmembre = $_SESSION['membre'];
    } elseif (isset($_SESSION['professionnel'])) {
        $professionel = true;
        $idpro = $_SESSION['professionnel'];
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
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        .image-edit-btn {
            display: inline-block;
            position: relative;
        }

        .edit-btn {
            width: 30px;
            height: 30px;
            cursor: pointer;
            position: absolute;
            bottom: 0;
            right: 0;
        }

        /* Styles pour la modale */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            width: 20%;
            text-align: center;

        }

        .modal-content-btn {
            display: flex;
            flex-direction: column;
            align-items: center;

        }

        .close-btn {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-btn:hover,
        .close-btn:focus {
            color: black;
        }

        input[type="file"] {
            margin-top: 20px;
        }

        .modal-footer button {
            margin-top: 15px;
        }

        .monCompteAffichageInfos{
            background-color: #31CEA6;
            border-radius: 20px;
            padding: 20px;
            margin: 20px;
            width: 50%;
            text-align: center;
        }

        a {
            text-decoration: none;
            color: inherit; /* Utilise la couleur du texte parent */
            font-size: 16px;
        }

        /* Styles spécifiques pour les différents états des liens */
        a:visited {
            text-decoration: none;
            color: inherit; /* Garde la même couleur pour les liens visités */
        }

        a:hover {
            text-decoration: underline; /* Ajoute un soulignement au survol (optionnel) */
            color: blue; /* Vous pouvez personnaliser cette couleur */
        }

        a:hover {
            text-decoration: none;
            color: #F2F1E9; /* Garde la même couleur pour les liens visités */
        }

        hr {
            border: none; /* Supprimer la bordure par défaut */
            border-top: 2px solid black; /* Ajouter une bordure en haut de la ligne, couleur bleue */
            margin: 20px 0; /* Espace autour de la ligne */
        }

    </style>
    
</head>
<body>
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
    <main>
        <h1>Mon Compte</h1>
        
        <?php 
            if (isset($_SESSION['membre'])) {
                $idcompte = $idmembre;
            } elseif (isset($_SESSION['professionnel'])) {
                $idcompte = $idpro;
            }

            //requete pour récupérer l'image de l'utilisateur
            $sql = "SELECT i.pathimage, c.*, numtelcompte FROM _image i JOIN _compte c 
                    ON i.idimage = c.idimagepdp
                    WHERE idcompte = :idcompte";

            // Préparer et exécuter la requête
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':idcompte', $idcompte, PDO::PARAM_INT);
            $stmt->execute();

            // Récupérer les avis
            $res = $stmt->fetchAll();
        ?>

        <div class="image-edit-btn">
            <img src="<?php echo $res[0]['pathimage']; ?>" alt="Photo de profil" style="width: 200px; height: 200px; border-radius: 50%;">
            <!-- Bouton d'édition -->
            <img class="edit-btn" src="./img/icons/edit.svg" alt="Éditer">
        </div>

        <h4><?php echo $res[0]['prenomcompte'] . " " . $res[0]['nomcompte']?></h4>

        <section>
            <!-- Gestion de l'affichage de la date -->
            <?php
            $dateCreation = $res[0]['datecreationcompte']; // Exemple : "2024-11-22"
            $dateConnexion = $res[0]['datederniereconnexioncompte']; // Exemple : "2024-11-22"

        
            // Convertir la date en timestamp
            $timestampCreation = strtotime($dateCreation);
            $timestampConnexion = strtotime($dateConnexion);

            $mois_francais = [
                '01' => 'janvier', '02' => 'février', '03' => 'mars', '04' => 'avril',
                '05' => 'mai', '06' => 'juin', '07' => 'juillet', '08' => 'août',
                '09' => 'septembre', '10' => 'octobre', '11' => 'novembre', '12' => 'décembre'
            ];
        
            $jourCrea = date('d', $timestampCreation);       // 22
            $moisCrea = $mois_francais[date('m', $timestampCreation)]; // novembre
            $anneeCrea = date('Y', $timestampCreation);

            $jourCo = date('d', $timestampConnexion);       // 22
            $moisCo = $mois_francais[date('m', $timestampConnexion)]; // novembre
            $anneeCo = date('Y', $timestampConnexion);
        
            ?>

            <p>Vous êtes inscris depuis le <?php echo $jourCrea . " " . $moisCrea . " " . $anneeCrea . " et votre dernière connexion remonte au " . $jourCo . " " . $moisCo . " " . $anneeCo?></p>
        </section>

        <section class="monCompteAffichageInfos">
            <a href="mes_infos.php">Gérer mes informations personnelles</a>
            <hr>
            <a href="">Gérer mon mot de passe</a>  
        </section>

        <section class="monCompteAffichageInfos">
            <a href="">Consulter mes offres</a>
            <hr>
            <a href="">Consulter les signalements</a>
            <hr>
            <a href="">Ajouter une offre</a>  
        </section>

        <section class="monCompteAffichageInfos">
            <a href="">Vos informations</a>
            <hr>
            <a href="">Vos factures</a>
        </section>

        <section class="monCompteAffichageInfos">
            <a href="">Supprimer mon compte</a>
        </section>
        

        <br>
        <a style="text-decoration:none;" href="index.php"> <button class="offer-btn">Retour à la page d'Accueil</button></a>
    </main>

    <!-- Modale pour l'upload de l'image -->
    <div id="imageModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Modifier votre photo de profil</h2>
            <form id="editImageForm" action="upload_profile_pic.php" method="post" enctype="multipart/form-data">
                <div class="modal-content-btn">
                    <input class="offer-btn" type="file" name="newProfileImage" accept="image/*" required>
                    <div class="modal-footer">
                        <button type="submit" class="offer-btn">Enregistrer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="footer"></div>

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
</body>
</html>
