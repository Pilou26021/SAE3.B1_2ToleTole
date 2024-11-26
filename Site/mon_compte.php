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
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 60vh;
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

    </style>
</head>
<body>

    <main>
        <div style=" position:sticky; top:20px; left:20px; width: 100%;">
                <a style="text-decoration: none; font-size: 30px; color: #040316; cursor: pointer;" href="./index.php">&#8617;</a>
                <!-- onclick="history.back(); -->
        </div>
        
        <h1>Mon Compte</h1>
        
        <?php 
            //requete pour récupérer l'image de l'utilisateur
            $sql = "SELECT c.nomcompte, c.prenomcompte, c.mailcompte, numtelcompte, i.pathimage FROM _compte c JOIN _image i
                    ON c.idimagepdp = i.idimage
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

        <p>Nom : <?php echo $res[0]['nomcompte']; ?></p>
        <p>Prénom : <?php echo $res[0]['prenomcompte']; ?></p>
        <p>Mail : <?php echo $res[0]['mailcompte']; ?></p>
        <p>Numéro de téléphone : <?php echo $res[0]['numtelcompte']; ?></p>

        <br>
        <a style="text-decoration:none;" href="index.php"> <button class="offer-btn">Retour à la page d'Accueil</button></a>
    </main>

    <!-- Modale pour l'upload de l'image -->
    <div id="imageModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Modifier votre photo de profil</h2>
            <form id="editImageForm" action="upload_profile_pic.php" method="post" enctype="multipart/form-data" onsubmit="return validImages(document.querySelectorAll('.image-input-fn'))">
                <div class="modal-content-btn">
                    <input class="offer-btn image-input-fn" type="file" name="newProfileImage" accept=".png, .jpg, .jpeg" required>
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
    <script src="./script.js" ></script>
</body>
</html>
