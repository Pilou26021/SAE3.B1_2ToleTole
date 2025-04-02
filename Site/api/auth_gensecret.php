<?php
require_once "../../COMPOSE/vendor/autoload.php";
include "../../SQL/connection_local.php";
use OTPHP\TOTP;

$idcompte = $_POST['idcompte'];
$secret = TOTP::generate(null);
$cle = $secret->getSecret();

$stmt = $conn->prepare("SELECT auth_parametre FROM _compte WHERE idcompte = :idcompte");
$stmt->bindParam(':idcompte', $idcompte, PDO::PARAM_INT);
$stmt->execute();
$parametre = $stmt->fetch(PDO::FETCH_ASSOC)['auth_parametre'];

if ($parametre === false) {   
    $stmt = $conn->prepare("UPDATE _compte SET auth_secret = :secret WHERE idcompte = :idcompte");
    $stmt->bindParam(':idcompte', $idcompte, PDO::PARAM_INT);
    $stmt->bindParam(':secret', $cle, PDO::PARAM_STR);
    $stmt->execute();

    $secret->setLabel('PACT - ToleTole');
    $grCodeUri = $secret->getQrCodeUri(
        'https://api.qrserver.com/v1/create-qr-code/?data=[DATA]&size=300x300&ecc=M',
        '[DATA]'
    );

    // envoie json avec secret et qrcode
    echo json_encode(['secret' => $cle, 'qrcode' => $grCodeUri]);
} else {
    echo json_encode(['secret' => 'nuh', 'qrcode' => 'uh']);
}