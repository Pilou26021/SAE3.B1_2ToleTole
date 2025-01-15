<?php
// Création de la session

error_reporting(0);

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
                <li><a href="mes_factures.php">Mes factures</a></li>
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
    <a href="index.php"> <img src="img/logos/fond_remove_big.png" alt="logo site noir" title="logo site noir" style="width: 90px; height: 90x; margin-left: 40%;"></a>
  

    <!-- Partie notifications -->
    <section class="header_partie_notif">

        <?php if (!empty($professionel)){

            $getNotifs = "SELECT *
                          FROM _notification n
                          WHERE n.idcompte = :idPro AND n.lu = false
                          ORDER BY n.datenotification DESC";

            $getNotifs = $conn->prepare($getNotifs);
            $getNotifs->bindValue(':idPro', $idcompte, PDO::PARAM_INT);
            $getNotifs->execute();
            $notifications = $getNotifs->fetchAll();

            //Compteur pour avoir le nombre d'avis non-lus
            $compteur = 0;

            foreach($notifications as $notif){
                $compteur++;
            }

        ?>

        <!-- Affichage du nombre d'avis non-lus -->
        <p><?php
            echo $compteur;
            ?></p>
        
        <!-- Icon de notifications -->
        <img class="header-pdp-user" src="
        <?php if ($notifications != false){
            ?>../img/icons/notifs_true.png<?php
        }
        else{?>../img/icons/notifs_false.png<?php
        }?>" id="myBtn" alt="notifications" title="Mes notifications">
            
        <!-- Le Modal -->
        <div id="myModal" class="notif_modal">
        
          <!-- Surmodal pour cacher le débordement de la barre de scroll -->
            <div class="notif_surmodal">
                <div class="notif_modal-content">
                    <!-- Croix pour fermer le modal -->
                    <span class="notif_close">&times;</span>

                    <?php
                        $offre_affichee = null;
                        if ($notifications != NULL){

                            echo "<h3 style='text-align: center;'>Vous avez " . $compteur . " avis non-lu(s) !</h3>";
                            echo "<a href='avis_mes_offres.php' style='display: block; text-align: center; text-decoration: none; color: black;'>Cliquez ici pour voir les avis sur vos offres</a>";


                            $dateDepart = Null;

                            foreach($notifications as $notif){
                                if (($notif["datenotification"] < $dateDepart) OR ($dateDepart == Null)){
                                
                                    $dateDepart = $notif["datenotification"];

                                    $timestamp = strtotime($notif["datenotification"]);

                                    // Tableau associatif pour traduire les mois en français
                                    $months = [
                                        'January' => 'janvier',
                                        'February' => 'février',
                                        'March' => 'mars',
                                        'April' => 'avril',
                                        'May' => 'mai',
                                        'June' => 'juin',
                                        'July' => 'juillet',
                                        'August' => 'août',
                                        'September' => 'septembre',
                                        'October' => 'octobre',
                                        'November' => 'novembre',
                                        'December' => 'décembre'
                                    ];


                                    $formatted_date = date('d F Y', $timestamp);

                                    $english_month = date('F', $timestamp);
                                    $formatted_date = str_replace($english_month, $months[$english_month], $formatted_date);

                                    echo "<h5>" . $formatted_date . "</h5>";
                                    
                                }

                                //Avoir les offres
                                $getOffre = "SELECT *
                                             FROM _offre o
                                             WHERE o.idoffre = :idoffre";

                                $getOffre = $conn->prepare($getOffre);

                                $getOffre->bindValue(':idoffre', $notif["idoffre"]);
                                $getOffre->execute();
                                $mon_offre = $getOffre->fetch();

    

                                if ($mon_offre["idoffre"] != $offre_affichee) {
                                    echo "<p> Offre: " . $mon_offre["titreoffre"] . "</p>";
                                    // Mettre à jour l'ID de l'offre affichée
                                    $offre_affichee = $mon_offre["idoffre"];
                                }

                                echo "<p class='notif_comment'>" . $notif["messagenotification"] . "</p>";
                            }

                            // On met les avis en "lu"
                            // $mettreEnLu = "UPDATE _notification
                            //             SET lu = true
                            //             WHERE idcompte = :idPro AND lu = false";

                            // $mettreEnLu = $conn->prepare($mettreEnLu);
                            // $mettreEnLu->bindValue(':idPro', $idcompte, PDO::PARAM_INT);
                            // $mettreEnLu->execute();

                        }
                        else{
                            echo "<h3 style='text-align: center;'> Vous n'avez aucune nouvelle notification </h3>";
                        }

                    ?>
                </div>
            </div>
        </div>
        
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                var modal = document.getElementById("myModal");
                var btn = document.getElementById("myBtn");
                var span = document.getElementsByClassName("notif_close")[0];

                btn.onclick = function() {
                    // Si le modal est déjà visible, le cacher, sinon l'afficher
                    if (modal.style.display === "block") {
                        modal.style.display = "none";
                        location.reload();
                        
                    } else {
                        var rect = btn.getBoundingClientRect();
                        var top = rect.top + window.scrollY;
                        var left = rect.left + window.scrollX;

                        // Positionnement du modal
                        modal.style.top = (top) + 50 + 'px';
                        modal.style.right = '95px';

                        modal.style.display = "block";
                    }
                };

                // Lorsque l'utilisateur clique sur la croix pour fermer le modal
                span.onclick = function() {
                    modal.style.display = "none";
                    location.reload();

                };

            });
        </script>

        <?php
        }?>

        <?php if(!empty($professionel) || !empty($membre)) {
            ?> <a href="mon_compte.php"> <?php
        } else {
            ?> <a href="connexion_membre.php"> <?php
        } ?>
            <img class="header-pdp-user" src="<?php echo (!empty($professionel) || !empty($membre)) ? $result['pathimage'] : './img/icons/user.png'; ?>" alt="image user" title="image user">
        </a>
    </section>

    

</header>
<?php
ob_end_flush();
?>