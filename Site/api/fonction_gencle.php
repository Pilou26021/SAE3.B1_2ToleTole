<?php

function cleapigen($typecompte, $conn) {
    $cleinvalide = true;
    while ($cleinvalide == true) {
        $key = '';
        if ($typecompte == "admin") {
            $key = 'rwda-';
            return $key;
        } else if ($typecompte == "membre") {
            $key = 'rw-';
        } else if ($typecompte == "professionel") {
            $key = 'rwd-';
        } else {
            return null;
        }
        $key .= substr(md5(uniqid(rand(), true)), 0, 8) . '-';
        $key .= substr(md5(uniqid(rand(), true)), 0, 4) . '-';
        $key .= substr(md5(uniqid(rand(), true)), 0, 4) . '-';
        $key .= substr(md5(uniqid(rand(), true)), 0, 4) . '-';
        $key .= substr(md5(uniqid(rand(), true)), 0, 12);
        // Vérifie si la cléapi n'est pas déjà utilisée
        $stmt = $conn->prepare("SELECT COUNT(*) FROM _compte WHERE chat_cleapi = :key");
        $stmt->bindParam(':key', $key, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        if ($count == 0) {
            $cleinvalide = false;
        }
    }

    return $key;
}