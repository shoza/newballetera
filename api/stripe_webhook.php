<?php
// Stripe sends raw POST — read before any framework parsing
$payload    = file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/mail.php';
require_once __DIR__ . '/../config/stripe.php';

if (!STRIPE_WEBHOOK_SECRET) {
    http_response_code(200); // accept silently until secret is configured
    exit;
}

if (!verify_stripe_signature($payload, $sig_header, STRIPE_WEBHOOK_SECRET)) {
    http_response_code(400);
    echo 'Invalid signature';
    exit;
}

$event = json_decode($payload, true);
if (!$event) { http_response_code(400); exit; }

if ($event['type'] !== 'payment_intent.succeeded') {
    http_response_code(200); // ignore other events
    exit;
}

$pi       = $event['data']['object'];
$pi_id    = $pi['id'];
$meta     = $pi['metadata'] ?? [];
$app_id   = (int)($meta['application_id']   ?? 0);
$app_type = $meta['application_type']        ?? '';

if (!$app_id || !in_array($app_type, ['dancer', 'choreo'], true) || !$pdo) {
    http_response_code(200);
    exit;
}

$table = $app_type === 'dancer' ? 'dancer_applications' : 'choreographer_applications';

$row = $pdo->prepare("SELECT * FROM $table WHERE id = ? AND stripe_pi_id = ? AND payment_status != 'paid'");
$row->execute([$app_id, $pi_id]);
$app = $row->fetch();

if (!$app) {
    http_response_code(200); // already handled or not found
    exit;
}

$pdo->prepare("UPDATE $table SET payment_status = 'paid' WHERE id = ?")
    ->execute([$app_id]);

$name  = $app['full_name'];
$email = $app['email'];

if ($app_type === 'dancer') {
    $photos  = json_decode($app['dance_photos'] ?? '[]', true) ?: [];
    $subject = "New Dancer Application — $name";
    $body    = "New dancer application (payment confirmed via webhook).\n\n"
             . "Name:  $name\nEmail: $email\nPhone: {$app['phone']}\n\n"
             . "Videos:\n  1. {$app['video_link_1']}\n  2. {$app['video_link_2']}\n  3. {$app['video_link_3']}\n\n"
             . "Headshot: " . ($app['headshot_path'] ?: '—') . "\n"
             . "Resume:   " . ($app['resume_path']   ?: '—') . "\n"
             . "Photos:   " . count($photos) . " file(s)\n";
} else {
    $subject = "New Choreographer Application — $name";
    $body    = "New choreographer application (payment confirmed via webhook).\n\n"
             . "Name:  $name\nEmail: $email\nPhone: {$app['phone']}\n\n"
             . "Videos:\n  1. {$app['video_link_1']}\n  2. {$app['video_link_2']}\n  3. {$app['video_link_3']}\n\n"
             . "Resume:       " . ($app['resume_path']       ?: '—') . "\n"
             . "Cover Letter: " . ($app['cover_letter_path'] ?: '—') . "\n";
}

send_mail(ADMIN_EMAIL,                $subject, $body, "$name <$email>");
send_mail('yarikfarifonov@gmail.com', $subject, $body, "$name <$email>");

$confirm_body = "Dear $name,\n\nThank you for applying to New Ballet Era. "
    . "Your application and payment of \$25 have been received. "
    . "We will review your materials and be in touch soon.\n\nWarm regards,\nNew Ballet Era";
send_mail($email, "Application Received — New Ballet Era", $confirm_body);

http_response_code(200);
echo 'ok';
