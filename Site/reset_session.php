<?php 

    session_start();
    session_unset();
    session_destroy();
    ob_clean();

?>