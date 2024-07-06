<?php
// Include the Composer autoload file
require '../vendor/autoload.php';

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

// Set up SMTP
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com'; // Your SMTP server
$mail->SMTPAuth = true;
$mail->Username = 'gampanithin@gmail.com'; // SMTP username
$mail->Password = 'xgkm dytw ygoz agvj'; // SMTP password
$mail->SMTPSecure = 'ssl';
$mail->Port = 465 ; // TCP port to connect to

// Set up email details
$mail->setFrom('gampanithin@gmail.com'); // Sender's email address and name
$mail->addAddress('gampanithin123@gmail.com'); // Recipient's email address and name
$mail->Subject = 'Test Email'; // Email subject
$mail->Body = 'This is a test email sent using PHPMailer.'; // Email body

// Send the email
if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent.';
}
?>
