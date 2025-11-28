<?php
if (!isset($_SESSION['MFA_AUTHENTICATED']) || $_SESSION['MFA_AUTHENTICATED'] !== true) {
    header("Location: verify_otp.php");
    exit;
}
?>
