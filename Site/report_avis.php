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
        $_SESSION['signalement_avis_ok'] = true;
    } catch (Exception $e) {
        $_SESSION['signalement_avis_ok'] = false;
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
        
    <?php
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    ?>
        
    </body>

</html>