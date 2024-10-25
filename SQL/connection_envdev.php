<?php 
// On va get les variables d'environnement depuis le fichier .env
$host = 'dbadmin-tole-tole.ventsdouest.dev'; // anciennement $servername
$username = getenv('DB_USER');
$password = getenv('DB_ROOT_PASSWORD');
$dbname = getenv('DB_NAME');
$port = getenv('PGDB_PORT');
$driver = "pgsql";

// Connexion à la base de données PostgreSQL
try {
    // Création d'une nouvelle instance PDO
    $dsn = "$driver:host=$host;port=$port;dbname=$dbname;";
    $conn = new PDO($dsn, $username, $password); // ici on utilise $username

    // Définir le mode d'erreur de PDO sur Exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Gestion des erreurs de connexion
    die("Connection failed: " . $e->getMessage());
}

// On se place sur le schema sae (ou 'public')
$sql = "SET SCHEMA 'public';";
$stmt = $conn->prepare($sql);
$stmt->execute();