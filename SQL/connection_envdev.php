<?php 
// on va get les variables d'environnement depuis le fichier .env
$servername = 'localhost';
$username = getenv('DB_USER');
$password = getenv('DB_ROOT_PASSWORD');
$dbname = getenv('DB_NAME');
$port = getenv('PGDB_PORT');

// Connexion à la base de données PostgreSQL
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

// Vérification de la connexion
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}
echo "Connected successfully";

// Fermer la connexion quand elle n'est plus nécessaire
pg_close($conn);

