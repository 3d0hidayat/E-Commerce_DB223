<?php
session_start();
if (isset($_POST['unset'])) {
    $_SESSION['notif_order_shown'] = true;
}
?>
