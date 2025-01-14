<?php 

    
error_reporting(E_ALL ^ E_WARNING);
include "../SQL/connection_local.php";
ob_start();
session_start();

$heure = $date = new DateTime('now', new DateTimeZone('Europe/Paris'));
$heure = $date->format('Hi');

$sql_horaires = "SELECT idoffre, horairesemaine from _offrerestaurant";
$stmt = $conn->prepare($sql_horaires);
$stmt->execute();
$horaires = $stmt->fetchAll();

foreach($horaires as $horaire){
    $horaire_decoded = json_decode($horaire['horairesemaine'], true);
    $horaire_decoded['lunchOpen'] = str_replace(':', '', $horaire_decoded['lunchOpen']);
    $horaire_decoded['lunchClose'] = str_replace(':', '', $horaire_decoded['lunchClose']);
    $horaire_decoded['dinnerOpen'] = str_replace(':', '', $horaire_decoded['dinnerOpen']);
    $horaire_decoded['dinnerClose'] = str_replace(':', '', $horaire_decoded['dinnerClose']);
    $results = array();

    if ($horaire_decoded['lunchOpen'] < $heure && $horaire_decodeds['lunchClose'] > $heure || $horaire_decoded['dinnerOpen'] < $heure && $horaire_decodeds['dinnerClose'] > $heure){
        $results.push($horaire['idoffre']) == true;
    } else {
        $results.push($horaire['idoffre']) == false;
    }
}


?>