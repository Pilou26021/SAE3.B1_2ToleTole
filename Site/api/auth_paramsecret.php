<?php
require_once("../../COMPOSE/vendor/autoload.php");
include "../../SQL/connection_local.php";
use OTPHP\TOTP;

$idcompte = $_POST['idcompte'];
$codeotp = $_POST['codeotp'];

$stmt = $conn->prepare("SELECT auth_secret FROM _compte WHERE idcompte = :idcompte");
$stmt->bindParam(':idcompte', $idcompte, PDO::PARAM_INT);
$stmt->execute();
$secret = $stmt->fetch(PDO::FETCH_ASSOC)['auth_secret'];

$otp = TOTP::create($secret);
if ($otp->verify($codeotp)) {
    echo "true";
    $stmt = $conn->prepare("UPDATE _compte SET auth_paremetre = true WHERE idcompte = :idcompte");
    $stmt->bindParam(':idcompte', $idcompte, PDO::PARAM_INT);
    $stmt->execute();
} else {
    echo "false";
}
?>