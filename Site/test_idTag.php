<?php 

    include '../SQL/connection_local.php';

    function getTagIdByValue($value) {
        $sql_get = "SELECT idTag FROM public._tag WHERE typeTag = :typeTag";

        global $conn;

        try {
            // Préparation de la requête
            $stmt = $conn->prepare($sql_get);
            
            // Liaison du paramètre
            $stmt->bindParam(':typeTag', $value, PDO::PARAM_STR);
            
            // Exécution de la requête
            $stmt->execute();
            
            // Vérification si un résultat a été trouvé
            if ($stmt->rowCount() > 0) {
                // Récupération de l'ID du tag
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result['idtag']; // Retourne l'ID
            } else {
                return null; // Aucun tag trouvé
            }
        } catch (PDOException $e) {
            // Gestion d'erreur
            echo "<br>Erreur lors de la récupération de l'ID du tag : " . $e->getMessage();
            return null; // En cas d'erreur, retourne null
        }
    }

    print_r(getTagIdByValue("Culturel"));

?>