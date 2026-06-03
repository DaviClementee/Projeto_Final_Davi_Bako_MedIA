<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

final class Mailer

{

  private array $cfg;

  public function __construct()
  {
    $this->cfg = require __DIR__ . '/../config/mail.php';
  }

  public function send(string $to, string $subject, string $html): void
  {

    $mail = new PHPMailer(true);

    try {

      $mail->isSMTP();

      $mail->Host = $this->cfg['host'];

      $mail->SMTPAuth = true;

      $mail->Username = $this->cfg['username'];

      $mail->Password = $this->cfg['password'];

      $mail->Port = (int)$this->cfg['port'];

      // encryption

      $enc = $this->cfg['encryption'] ?? '';

      if ($enc === 'tls') {

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      } elseif ($enc === 'ssl') {

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
      }

      $mail->setFrom($this->cfg['from_email'], $this->cfg['from_name']);

      $mail->addAddress($to);

      $mail->isHTML(true);

      $mail->CharSet = 'UTF-8';

      $mail->Subject = $subject;

      $mail->Body = $html;

      $mail->send();
    } catch (MailException $e) {

      throw new Exception("Erro ao enviar email: " . $e->getMessage());
    }
  }
}
