<?php
session_start();
ob_start();

$idcompte = null;
if (isset($_SESSION['membre'])) {
    $idcompte = $_SESSION['membre'];
} elseif (isset($_SESSION['professionnel'])) {
    $idcompte = $_SESSION['professionnel'];
}
?>

<script>
    function get_secret() {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'api/auth_gensecret.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var response = JSON.parse(xhr.responseText);
                document.querySelector('.popup-content-right-inner-qr img').src = response.qrcode;
                document.getElementById('secret').innerText = response.secret;
            }
        };
        xhr.send('idcompte=' + <?php echo $idcompte; ?>);
    }
    // array avec secret et qrcode
    get_secret();
    
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('.popup').style.display = 'block';
    });

    function etape_suivante() {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'api/auth_paramsecret.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                if (xhr.responseText == 'true') {
                    closePopup();
                    window.location.href = 'index.php';
                } else {
                    alert('Code OTP invalide');
                }
            }
        };
        var codeotp = prompt('Entrez le code OTP');
        if (codeotp != null) {
            xhr.send('idcompte=' + <?php echo $idcompte; ?> + '&codeotp=' + codeotp);
        }
    }
</script>
<div>
    <div class="popup-header">
        <h2>Authentification à deux facteurs</h2>
        <button class="close-popup" onclick="closePopup();">X</button>
    </div>
    <div class="popup-body">
        <div class="popup-content">
            <div class="popup-content-inner">
                <div class="popup-content-left">
                    <div class="popup-content-left-inner">
                        <h3>Étape 1: Installer une application d'authentification à deux facteurs</h3>
                    </div>
                </div>
                <div class="popup-content-right">
                    <div class="popup-content-right-inner">
                        <h3>Étape 2: Scannez le code QR ou entrez la clé secrète ci-dessous</h3>
                        <p>Ouvrir votre application d'authentification et scanner le code QR ci-dessous ou entrez la clé secrète manuellement.</p>
                        <div class="popup-content-right-inner-qr">
                            <img src="" alt="QR Code">
                        </div>
                        <div class="popup-content-right-inner-secret">
                            <p>Clé secrète: <span id="secret"></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="popup-footer">
        <button class="button button-primary" onclick="closePopup();">suivant</button>
    </div>
</div>