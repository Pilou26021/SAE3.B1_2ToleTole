<?php
// script de connection à la bdd mariadb
$servername = "dbadmin-tole-tole.ventsdouest.dev";
$username = "sae";
$password = "rico-sl33py-polo6ne";
$dbname = "sae";

// Connection à la bdd
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>
