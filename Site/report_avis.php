<?php 

    ob_start();
    session_start();
    include "../SQL/connection_local.php";

    $raison = $_POST['raison'];
    $idavis = $_POST['idavis'];

    var_dump($raison);
    var_dump($idavis);

    $sql = "INSERT INTO _alerteravis (idsignalement, idavis) VALUES ($raison, $idavis)";
    $stmt = $conn->prepare($sql);
    try {
        $stmt->execute();
    } catch (Exception $e) {
        echo $e;
    }
    
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Signaler un avis</title>
    </head>

    <body>

    <script>
        alert("Votre signalement a bien été pris en compte.");
    </script>

    <?php
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    ?>
        
    </body>

</html>