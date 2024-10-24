<?php
// Création de la session
ob_start();
session_start();

include "./SQL/connection_envdev.php";   

$professionel = false;
$membre = false;


// On vérifie si l'utilisateur est connecté. Il peut être connecté en tant que membre ou professionnel. Si il n'est pas connecté alors il sera visiteur.
if (isset($_SESSION['membre'])) {
    $membre = true;
    $idmembre = $_SESSION['membre'];
} elseif (isset($_SESSION['professionnel'])) {
    $professionel = true;
    $idpro = $_SESSION['professionnel'];
}

?>
<header>
    <nav id="mySidenav" class="sidenav">
        <div class="profil">
            <?php if ($professionel): ?>
                <!-- vue à utiliser compteProfessionnelImage -->
                <?php
                // l'idpro est dans la session dans professionel
                $sql = "SELECT nomCompte, prenomCompte, mailCompte, pathImage FROM compteProfessionnelImage WHERE idPro = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(1, $idpro, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                /* result :
                array(4) { ["nomcompte"]=> string(5) "Smith" ["prenomcompte"]=> string(4) "John" ["mailcompte"]=> string(22) "john.smith@example.com" ["pathimage"]=> string(18) "path/to/image1.jpg" }
                */
                ?>
                <img src="<?= $result['pathimage'] ?>" alt="" width="70px" height="70px">
                <p><?= $result['prenomcompte'] . ' ' . $result['nomcompte'] ?></p>
                <p><?= $result['mailcompte'] ?></p>
                <p>Professionnel</p>
            <?php elseif ($membre): ?>
                <!-- Affichage pour le membre (à compléter) -->
            <?php else: ?>
                <img src="img/TripEnarvor.png" alt="" width="70px" height="70px">
                <p>Visiteurs</p>
            <?php endif; ?>
        </div>
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <ul>
            <?php if ($professionel): ?>
                <li><a href="mon_compte.php">Mon compte</a></li>
                <!-- <li><a href="#">Mes offres</a></li> -->
                <li><a href="contact.php">Contact</a></li>
                <li><a href="deconnexion.php">Déconnexion</a></li>
            <?php elseif ($membre): ?>
                <li><a href="mon_compte">Mon compte</a></li>
                <li><a href="#">Mes réservations</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="deconnexion.php">Déconnexion</a></li>
            <?php else: ?>
                <li><a href="connexion_pro.php">Me Connecter</a></li>
                <!-- <li><a href="#">M'inscrire</a></li> -->
                <li><a href="contact.php">Contact</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <span class="openbtn" onclick="openNav()">&#9776;</span>
    <a href="index.php"> <img src="img/fond_remove.png" alt="logo site noir" title="logo site noir"></a>
    <a href="connexion_pro.php">
        <img src="<?php echo (!empty($professionel) || !empty($membre)) ? $result['pathimage'] : './img/User.png'; ?>" alt="image user" title="image user" style="width: 50px; height: 50px;">
    </a>
</header>