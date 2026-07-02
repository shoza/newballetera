<?php
// config/mail.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

require_once __DIR__ . '/../vendor/autoload.php';

// Credentials live in config/secrets.php (gitignored). See secrets.example.php.
$__secrets = is_file(__DIR__ . '/secrets.php') ? require __DIR__ . '/secrets.php' : [];

define('SMTP_HOST', 'p3plzcpnl506104.prod.phx3.secureserver.net');
define('SMTP_PORT', 465);
define('SMTP_USER', $__secrets['smtp_user'] ?? '');
define('SMTP_PASS', $__secrets['smtp_pass'] ?? '');
define('SMTP_FROM', 'noreply@newballetera.com');
define('SMTP_NAME', 'New Ballet Era');

function send_mail(string $to, string $subject, string $body, string $reply_to = ''): bool
{
    $mail = new PHPMailer(true);

    try {
        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = SMTP_PORT;

        $mail->setFrom(SMTP_FROM, SMTP_NAME);
        $mail->addAddress($to);
        if ($reply_to) {
            [$reply_email, $reply_name] = str_contains($reply_to, '<')
                ? [trim(substr($reply_to, strpos($reply_to, '<') + 1), '>'), trim(substr($reply_to, 0, strpos($reply_to, '<')))]
                : [$reply_to, ''];
            $mail->addReplyTo($reply_email, $reply_name);
        }

        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        return true;
    } catch (PHPMailerException $e) {
        error_log('Mailer Error: ' . $mail->ErrorInfo);
        return false;
    }
}
