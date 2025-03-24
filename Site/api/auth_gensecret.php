<?php
require_once("../../COMPOSE/vendor/autoload.php");
include "../../SQL/connection_local.php";
use OTPHP\TOTP;

$idcompte = $_POST['idcompte'];
$secret = TOTP::generate(null);

$stmt = $conn->prepare("UPDATE _compte SET auth_secret = :secret WHERE idcompte = :idcompte");
$stmt->bindParam(':idcompte', $idcompte, PDO::PARAM_INT);
$stmt->bindParam(':secret', $secret->getSecret(), PDO::PARAM_STR);
$stmt->execute();

$secret->setLabel('PACT - ToleTole');
$grCodeUri = $secret->getQrCodeUri(
    'https://api.qrserver.com/v1/create-qr-code/?data=[DATA]&size=300x300&ecc=M',
    '[DATA]'
);

// envoie json avec secret et qrcode
echo json_encode(array('secret' => $secret->getSecret(), 'qrcode' => $grCodeUri));
?>