<?php

require '../assets/vendor/phpmailer/src/Exception.php';
require '../assets/vendor/phpmailer/src/PHPMailer.php';
require '../assets/vendor/phpmailer/src/SMTP.php';

// Inisialisasi PHPMailer
use PHPMailer\PHPMailer\PHPMailer;

// Alamat email penerima dan pengirim
$receiving_email_address = 'ar.akasara.jawa@gmail.com';
$sender_email_address = 'ar.akasara.jawa@gmail.com';
$password_smtp = 'ouclmrjeqwqfypvm';

// Periksa apakah request adalah POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Ambil data dari form dan sanitasi input
  $name = htmlspecialchars($_POST['name']);
  $email = htmlspecialchars($_POST['email']);
  $subject = "Email dari JawaLens | " . htmlspecialchars($_POST['subject']);
  $message = htmlspecialchars($_POST['message']);

  // Validasi input
  if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    echo json_encode([
      'status' => 'error',
      'message' => 'Semua field wajib diisi.',
    ]);
    exit;
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
      'status' => 'error',
      'message' => 'Format email tidak valid.',
    ]);
    exit;
  }

  // Setup PHPMailer
  $mail = new PHPMailer(true);
  try {
    // Pengaturan server SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';  // Server SMTP Gmail
    $mail->SMTPAuth = true;
    $mail->Username = $sender_email_address;  // Alamat email pengirim
    $mail->Password = $password_smtp;  // Password aplikasi untuk pengirim
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Pengaturan penerima dan pengirim email
    $mail->setFrom($sender_email_address, 'JawaLens');
    $mail->addAddress($receiving_email_address);  // Alamat email penerima
    $mail->addReplyTo($email, $name);  // Alamat email balasan

    // Pengaturan konten email
    $mail->isHTML(false);  // Mengirimkan email dalam format teks
    $mail->Subject = $subject;
    $mail->Body = "Anda menerima pesan baru dari $name.\n\nPesan:\n$message\n";

    // Kirim email
    if ($mail->send()) {
      echo json_encode([
        'status' => 'success',
        'message' => 'Pesan Anda sudah dikirim. Terima kasih!',
      ]);
    }
  } catch (Exception $e) {
    echo json_encode([
      'status' => 'error',
      'message' => 'Maaf, terjadi kesalahan. Email tidak dapat dikirim. ' . $mail->ErrorInfo,
    ]);
  }
} else {
  echo json_encode([
    'status' => 'error',
    'message' => 'Metode request tidak valid.',
  ]);
}