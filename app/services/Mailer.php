<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

class Mailer {

    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);

        try {
            // Use SMTP
            $this->mail->isSMTP();
            $this->mail->Host       = 'smtp.gmail.com';
            $this->mail->SMTPAuth   = true;

            // YOUR EMAIL + APP PASSWORD
            $this->mail->Username   = 'homeservices101adl@gmail.com';
            $this->mail->Password   = 'tnyd jlad vngi hyvo'; // Not Gmail password, app password!

            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port       = 587;

            // Default From address
            $this->mail->setFrom('homeservices101adl@gmail.com', 'Home Services App');

        } catch (Exception $e) {
            error_log("Mailer Setup Error: " . $e->getMessage());
        }
    }

    public function sendMail($to, $subject, $body) {
        try {
            $this->mail->clearAddresses();   
            $this->mail->addAddress($to);
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;

            return $this->mail->send();

        } catch (Exception $e) {
            error_log("Mailer Error: " . $e->getMessage());
            return false;
        }
    }
}
