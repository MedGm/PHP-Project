<?php 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

function prepareMailer() {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'confirmationfsttinscri@gmail.com';
    $mail->Password = 'wwsz xtux pumu jkcm';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->setFrom('confirmationfsttinscri@gmail.com', 'UAE Administration');
    $mail->isHTML(true);
    $mail->clearAddresses(); // Clear any previous recipients
    return $mail;
}