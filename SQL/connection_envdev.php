<?php 
// on va get les variables d'environnement depuis le fichier .env
$servername = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('MARIADB_PASSWORD');
$dbname = getenv('DB_NAME');

// Connection à la bdd
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>
