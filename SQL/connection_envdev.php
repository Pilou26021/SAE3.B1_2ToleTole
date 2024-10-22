<?php 
// Charger le fichier .env situé à /docker/sae/.env
// phpdotenv ne peut pas être utilisé


// on va get les variables d'environnement depuis le fichier .env
$servername = 'dbadmin-tole-tole.ventsdouest.dev';
$username = getenv('DB_USER');
$password = getenv('DB_ROOT_PASSWORD');
$dbname = getenv('DB_NAME');
$port = getenv('PGDB_PORT');
$driver = "pgsql";

print_r($servername);
print_r($username);
print_r($password);
print_r($dbname);
print_r($port);
print_r($driver);

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