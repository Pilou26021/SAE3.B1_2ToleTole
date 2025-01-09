<?php 

    ob_start();
    session_start();
    include "../SQL/connection_local.php";

    var_dump($_POST);

    $id_avis = intval($_POST['id_avis']);

    
    
?>