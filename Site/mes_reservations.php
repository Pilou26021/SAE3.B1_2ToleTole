<?php
include "header.php";
ob_start();

?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <link rel="stylesheet" href="./style.css">   
        <title>Contact</title>
        <style>
            main {
                background-color: #F2F1E9;
                display: flex;
                flex-direction: column; /* Ajouté pour aligner les éléments verticalement */
                justify-content: center;
                align-items: center;
                height: 60vh;
                margin: 0;
            }
            img {
                max-width: 100%;
                height: auto;
            }
        </style>
        <script
                src="https://code.jquery.com/jquery-3.3.1.js"
                integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
                crossorigin="anonymous">
        </script>
        <script> 
            $(function(){
                $("#footer").load("./footer.html"); 
            });
        </script> 
    </head>
    <body>
        <div id="header"></div>
        <main>
            <img src="./img/work-in-progress.png" alt="WORK IN PROGRESS">
            <br>
            <a style="text-decoration:none;" href="index.php"> <button class="offer-btn">Retour à la page d'Acceuil</button></a>
        </main>
        <div id="footer"></div>
        <script src="script.js"></script> 
    </body>
</html>
<?php
ob_end_flush();
?>