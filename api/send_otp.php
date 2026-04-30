<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Path to your PHPMailer files

$mail = new PHPMailer(true);

try {
    // 1. SMTP Server Settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; // Use Gmail SMTP server
    $mail->SMTPAuth   = true;
    $mail->Username   = 'your-email@gmail.com'; // Your real email
    $mail->Password   = 'your-app-password'; // Your 16-character App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // 2. Generate and Send OTP
    $otp = mt_rand(100000, 999999); // Generate a real 6-digit random code
    $mail->setFrom('your-email@gmail.com', 'AgriLease');
    $mail->addAddress($_POST['email']); // Recipient's email from your form

    $mail->isHTML(true);
    $mail->Subject = 'Your AgriLease Verification Code';
    $mail->Body    = "Your code is: <b>$otp</b>";

    $mail->send();
    
    // 3. Store for Verification
    session_start();
    $_SESSION['otp'] = $otp; // Save real OTP in session to check later
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $mail->ErrorInfo]);
}
?>