<?php 

    error_reporting(E_ALL ^ E_WARNING);

    //start session
    ob_start();
    session_start();

    //connecteur pour requête
    include "../SQL/connection_local.php";

    // On vérifie si l'utilisateur est connecté. Il peut être connecté en tant que membre ou professionnel. Si il n'est pas connecté alors il sera visiteur.
    if (isset($_SESSION['professionnel'])) {
        $professionel = true;
        $idProPropose = $_SESSION['professionnel'];
    } else {
        header("Location: ".$_SERVER['HTTP_REFERER']);
    }

    // On récupère les factures de l'utilisateur
    $sql = "SELECT * FROM _facture f
            JOIN _professionnelprive p ON f.idproprive = p.idproprive
            WHERE p.idpro = $idProPropose
            ORDER BY f.datefacture DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $factures = $stmt->fetchAll();

    if(!isset($factures)){
        header("Location: index.php");
    }

    function nbr2month($nbr){
        switch($nbr){
            case '01':
                return 'Janvier';
            case '02':
                return 'Février';
            case '03':
                return 'Mars';
            case '04':
                return 'Avril';
            case '05':
                return 'Mai';
            case '06':
                return 'Juin';
            case '07':
                return 'Juillet';
            case '08':
                return 'Août';
            case '09':
                return 'Septembre';
            case '10':
                return 'Octobre';
            case '11':
                return 'Novembre';
            case '12':
                return 'Décembre';
            default:
                return 'Mois invalide';
        }
    }


    include "header.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes factures</title>
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
            $("#footer").load("footer.html"); 
            });
        </script> 

        <div id="header"></div>

    <main>

        <?php 
        
            foreach($factures as $facture){
                $mois_facture = substr($facture['datefacture'], 0, -3);
                ?> 
                    <div class="container-facture">
                        <h3><?= nbr2month(explode("-", $facture['datefacture'])[1]) . " " . explode("-", $facture['datefacture'])[0]?></h3>
                        <a href="convert_facture.php?id=$facture" . <?= $facture['idfacture'] ?>>Générer le pdf de la facture</a>
                    </div>
                <?php
            }

        ?>

    </main>

    <div id="footer"></div>
    <script src="./script.js" ></script>
    
</body>
</html>