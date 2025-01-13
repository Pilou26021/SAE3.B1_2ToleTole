<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

        <script
            src="https://code.jquery.com/jquery-3.3.1.js"
            integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
            crossorigin="anonymous">
        </script>
        <script> 
            $(function(){
            $("#header").load("header.php");
            $("#footer").load("footer.html"); 
            });
        </script> 

        <div id="header"></div>

    <main>

        <h1>La page que vous essayez d'ouvrir est soit inexistante</h1>
        <h1>soit actuellement indisponible, veuillez réessayer plus tard.</h1>
        <img class="erreur404" src="./img/icons/404.svg" alt="Image erreur 404">
        <a href="index.php" class="offer-btn <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>">Retour aux offres</a>

    </main>

    <div id="footer"></div>
    <script src="./script.js" ></script>
</body>
</html>