<?php 
// on va get les variables d'environnement depuis le fichier .env
$servername = 'dbadmin-tole-tole.ventsdouest.dev';
$username = getenv('sae');
$password = getenv('barclay-ass1ed-laSer');
$dbname = getenv('sae');
$port = getenv('5432');
$driver = "pgsql";

// Connexion à la base de données PostgreSQL
try {
    // Création d'une nouvelle instance PDO
    $dsn = "$driver:host=$servername;port=$port;dbname=$dbname;";
    $conn = new PDO($dsn, $username, $password);
    
    // Définir le mode d'erreur de PDO sur Exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected successfully";

} catch (PDOException $e) {
    // Gestion des erreurs de connexion
    die("Connection failed: " . $e->getMessage());
}

// Fermer la connexion quand elle n'est plus nécessaire
$conn = null;