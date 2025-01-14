<?php
include "../../SQL/connection_local.php";

// On passe la clé API comme devoilée
$idcompte = $_POST['idcompte'];
$stmt = $conn->prepare("SELECT chat_cleapi FROM _compte WHERE idcompte = :idcompte");
$stmt->bindParam(':idcompte', $idcompte, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

echo $result['chat_cleapi'];

?>
