<?php
// Création de la session
ob_start();
session_start();

include "../SQL/connection_local.php";   

$professionel = false;
$membre = false;


// On vérifie si l'utilisateur est connecté. Il peut être connecté en tant que membre ou professionnel. Si il n'est pas connecté alors il sera visiteur.
if (isset($_SESSION['membre'])) {
    $membre = true;
    $idcompte = $_SESSION['membre'];
} elseif (isset($_SESSION['professionnel'])) {
    $professionel = true;
    $idcompte = $_SESSION['professionnel'];
}

?>
<header>
    <nav id="mySidenav" class="sidenav">
        <div class="profil">
            <?php if ($professionel || $membre): ?>

                <!-- vue à utiliser compteProfessionnelImage -->
                <?php
                // l'idpro est dans la session dans professionel
                $sql = "SELECT nomcompte, prenomcompte, mailcompte, pathImage FROM compteImage WHERE idcompte = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(1, $idcompte, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                ?>
                <img src="<?= $result['pathimage'] ?>" alt="" width="70px" height="70px" style="border-radius:50%;">
                <p><?= $result['prenomcompte'] . ' ' . $result['nomcompte'] ?></p>
                <p><?= $result['mailcompte'] ?></p>
                <?php if ($professionel){ ?>
                    <p>Professionnel</p>
                <?php } else { ?>
                    <p>Membre</p>
                <?php } ?>
            <?php else: ?>

                <img src="img/logos/tripenarvor_nobg.png" alt="" width="70px" height="70px">
                <p>Visiteur</p>

            <?php endif; ?>
        </div>
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <ul>

            <!-- REPETITION !!! -->
            <?php if ($professionel): ?>
                <li><a href="mon_compte.php">Mon compte</a></li>
                <!-- <li><a href="#">Mes offres</a></li> -->
                <li><a href="contact.php">Contact</a></li>
                <li><a href="deconnexion.php">Déconnexion</a></li>
            <?php elseif ($membre): ?>
                <li><a href="mon_compte.php">Mon compte</a></li>
                <li><a href="mes_reservations.php">Mes réservations</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="deconnexion.php">Déconnexion</a></li>
            <?php else: ?>
                <li><a href="connexion_membre.php">Me Connecter</a></li>
                <!-- <li><a href="#">M'inscrire</a></li> -->
                <li><a href="contact.php">Contact</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <span class="openbtn" onclick="openNav()">&#9776;</span>
    <a href="index.php"> <img src="img/logos/fond_remove_big.png" alt="logo site noir" title="logo site noir" style="width: 90px; height: 90x;"></a>
    <?php if(!empty($professionel) || !empty($membre)) {
        ?> <a href="mon_compte.php"> <?php
    } else {
        ?> <a href="connexion_membre.php"> <?php
    } ?>
        <img class="header-pdp-user" src="<?php echo (!empty($professionel) || !empty($membre)) ? $result['pathimage'] : './img/icons/user.png'; ?>" alt="image user" title="image user">
    </a>
</header>
<?php
ob_end_flush();
?>